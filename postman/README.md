# Coffee Shop POS API - Postman Collection

## Overview
This collection contains organized API endpoints for the Coffee Shop POS system. Import this file into Postman to test and manage all API endpoints.

## Translation System
The system supports automatic translation between English and Khmer:
- Category names are stored in English in the database
- When displayed in the UI, they are automatically translated using Laravel's `__()` helper function
- Translations are defined in `lang/en.json` and `lang/km.json`
- Current supported category translations:
  - Cake → នំខេក
  - Juice → ទឹកផ្លែឈើ
  - Coffee → កាហ្វេ
  - Hot Coffee → កាហ្វេក្តៅ
  - Iced Coffee → កាហ្វេទឹកកក
  - Flavored Coffee → កាហ្វេរសជាតិ
  - Non-Coffee Drinks → ភេសជ្ជៈផ្សេងៗ

## Base URL
```
http://localhost:8000/api
```

## How to Import
1. Open Postman
2. Click **Import** button
3. Select **Upload Files**
4. Choose `postman/CoffeeShopPOS-API.json`
5. Click **Import**

## Collection Structure

### 1. Categories
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/category/list` | List all categories |
| GET | `/category/{id}` | Get category detail |
| POST | `/category/create` | Create new category |
| POST | `/category/update` | Update category |
| GET | `/category/delete/{id}` | Delete category |

### 2. Products
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/product/list` | List all products |
| GET | `/product/{id}` | Get product detail |
| POST | `/product/create` | Create new product |
| POST | `/product/update` | Update product |
| GET | `/product/delete/{id}` | Delete product |

### 3. Product Sizes
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/product/sizes/{productId}` | Get product sizes |
| POST | `/product/size/create` | Create product size |
| POST | `/product/size/update` | Update product size |
| GET | `/product/size/delete/{id}` | Delete product size |

### 4. Discounts
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/discount/list` | List all discounts |
| POST | `/discount/create` | Create discount |
| POST | `/discount/update` | Update discount |
| GET | `/discount/delete/{id}` | Delete discount |

### 5. Orders
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/order/list` | List all orders |
| GET | `/order/{id}` | Get order detail |
| POST | `/order/create` | Create new order |
| POST | `/order/status/update` | Update order status |
| GET | `/order/delete/{id}` | Delete order |

### 6. Tax Settings
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/tax/list` | List all taxes |
| POST | `/tax/create` | Create tax |
| POST | `/tax/update` | Update tax |
| GET | `/tax/delete/{id}` | Delete tax |

### 7. Feedback
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/feedback/list` | List all feedback |
| GET | `/feedback/list/{productId}` | List feedback by product |
| POST | `/feedback/create` | Create feedback |

## Setup Steps

### 1. Seed Database
```bash
php artisan migrate:fresh --seed
```

### 2. Start Server
```bash
php artisan serve
```

### 3. Test API
- Import collection to Postman
- Update `baseUrl` variable if needed
- Test endpoints in order (Categories → Products → Sizes → Discounts → Orders → Tax → Feedback)

## Sample Data
The database includes:
- **4 Categories**: Hot Coffee, Iced Coffee, Flavored Coffee, Non-Coffee Drinks
- **26 Products**: Various coffee drinks with sizes S, M, L, XXL
- **Exchange Rate**: 4100 KHR = 1 USD

## Postman Variables
| Variable | Default Value | Description |
|----------|---------------|-------------|
| `baseUrl` | `http://localhost:8000/api` | API base URL |
| `exchangeRate` | `4100` | KHR to USD rate |

## Testing Order Recommendation
1. **Categories** - Create/read categories first
2. **Products** - Then create products with category_id
3. **Product Sizes** - Add sizes to products
4. **Discounts** - Create discounts for products
5. **Orders** - Create orders using products
6. **Tax** - Manage tax settings
7. **Feedback** - Add product reviews
