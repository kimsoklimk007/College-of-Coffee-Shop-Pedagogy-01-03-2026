# Coffee Shop API (FastAPI)

## Setup

1. Install dependencies:
```bash
cd api
pip install -r requirements.txt
```

2. Run the server:
```bash
python main.py
```

The API will run on `http://localhost:8000`

## Endpoints

- `GET /` - Root endpoint
- `GET /health` - Health check
- `POST /send-menu` - Send menu to Telegram
- `POST /send-order` - Send order to Telegram
- `POST /webhook` - Telegram webhook handler

## Telegram Bot Setup

1. Create a bot via @BotFather on Telegram
2. Get the bot token
3. Set webhook: `https://your-domain.com/webhook`
