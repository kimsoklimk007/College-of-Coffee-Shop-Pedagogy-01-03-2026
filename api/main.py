from fastapi import FastAPI, HTTPException, BackgroundTasks
from pydantic import BaseModel
from typing import Optional, List
import httpx
import os

app = FastAPI(title="Coffee Shop Telegram Bot API")

TELEGRAM_API_URL = "https://api.telegram.org/bot"

class OrderMessage(BaseModel):
    bot_token: str
    chat_id: str
    text: str

class MenuItem(BaseModel):
    name: str
    size: Optional[str] = None
    price: float
    quantity: int = 1

class OrderRequest(BaseModel):
    bot_token: str
    chat_id: str
    items: List[MenuItem]
    customer_name: Optional[str] = None
    customer_phone: Optional[str] = None
    delivery_address: Optional[str] = None

def send_telegram_message(bot_token: str, chat_id: str, text: str):
    """Send message to Telegram"""
    url = f"{TELEGRAM_API_URL}{bot_token}/sendMessage"
    payload = {
        "chat_id": chat_id,
        "text": text,
        "parse_mode": "Markdown"
    }
    try:
        response = httpx.post(url, json=payload, timeout=10)
        return response.json()
    except Exception as e:
        return {"ok": False, "error": str(e)}

@app.get("/")
def root():
    return {"message": "Coffee Shop Telegram Bot API", "status": "running"}

@app.get("/health")
def health_check():
    return {"status": "healthy"}

@app.post("/send-menu")
async def send_menu(order: OrderMessage):
    """Send menu to customer via Telegram"""
    result = send_telegram_message(order.bot_token, order.chat_id, order.text)
    if result.get("ok"):
        return {"success": True, "message": "Menu sent successfully"}
    raise HTTPException(status_code=400, detail=result.get("error", "Failed to send message"))

@app.post("/send-order")
async def send_order(order: OrderRequest, background_tasks: BackgroundTasks):
    """Receive order from customer via Telegram and forward to admin"""

    order_text = "=== NEW ORDER ===\n\n"

    if order.customer_name:
        order_text += f"Customer: {order.customer_name}\n"
    if order.customer_phone:
        order_text += f"Phone: {order.customer_phone}\n"
    if order.delivery_address:
        order_text += f"Address: {order.delivery_address}\n"

    order_text += "\n--- Order Items ---\n"

    total = 0
    for item in order.items:
        subtotal = item.price * item.quantity
        total += subtotal
        size_info = f" ({item.size})" if item.size else ""
        order_text += f"× {item.quantity} {item.name}{size_info} = ${subtotal:.2f}\n"

    order_text += f"\n*Total: ${total:.2f}*"

    result = send_telegram_message(order.bot_token, order.chat_id, order.text)

    if result.get("ok"):
        return {"success": True, "order_text": order_text, "total": total}
    raise HTTPException(status_code=400, detail=result.get("error", "Failed to send order"))

@app.post("/webhook")
async def telegram_webhook(update: dict):
    """Handle incoming Telegram webhooks"""
    message = update.get("message", {})
    text = message.get("text", "")
    chat_id = message.get("chat", {}).get("id")

    if text.startswith("/start"):
        welcome_text = "Welcome to Coffee Shop! Use /menu to see our products."
        return {"method": "sendMessage", "chat_id": chat_id, "text": welcome_text}

    elif text == "/menu":
        menu_text = "Please visit our website to see the menu: http://your-site.com/menu"
        return {"method": "sendMessage", "chat_id": chat_id, "text": menu_text}

    return {"ok": True}

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="127.0.0.1", port=5000)
