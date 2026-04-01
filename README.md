# PTSMS - Product Purchase Management System

**Developer**: Dzaki Amri Zaidaan  
**Company**: PT SMS

## Cara Run Project

### 1. Install Dependencies
```bash
composer install
```

### 2. Setup .env
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` sesuai database:
```
DB_DATABASE=ptsms
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi Database
```bash
php artisan migrate
```

### 4. Run Server
```bash
php artisan serve
```

Akses: `http://127.0.0.1:8000`

---

## API Endpoints

Base URL: `/api`

### Auth
- `POST /api/login` - Login dengan email & password, dapat token
- `GET /api/me` - Lihat user login (butuh Authorization: Bearer {token})

### Products
- `GET /api/products` - Lihat semua produk
- `POST /api/products` - Buat produk (name, price)

### Purchases
- `GET /api/purchases` - Lihat semua pembelian
- `POST /api/purchases` - Buat pembelian (date, items[])

---

## Contoh Request

**Login:**
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

**Buat Produk:**
```bash
curl -X POST http://127.0.0.1:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{"name":"Pensil","price":5000}'
```

**Buat Pembelian:**
```bash
curl -X POST http://127.0.0.1:8000/api/purchases \
  -H "Content-Type: application/json" \
  -d '{
    "date":"2026-04-01",
    "items":[
      {"product_id":1,"qty":2,"price":5000}
    ]
  }'
```

---

## Database Tables

- **products**: id, name, price, timestamps
- **purchases**: id, date, total_price, timestamps
- **purchase_items**: id, purchase_id, product_id, qty, price, timestamps
- **users**: id, name, email, password, timestamps

---

## Response Format

Semua API return JSON dengan format:
```json
{
  "success": true,
  "data": { ... }
}
```
