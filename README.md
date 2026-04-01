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
- `GET /api/purchases/{id}` - Lihat detail pembelian

### Report
- `GET /api/report/purchases` - Report menggunakan Store Procedure
  - Parameter: `start_date`, `end_date`, `product_id` (optional)
  - Output: tanggal, nama_produk, total_transaksi, total_qty, total_amount

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
      {"product_id":1,"qty":2}
    ]
  }'
```

**Get Report Purchases (dengan Store Procedure):**
```bash
curl -X GET "http://127.0.0.1:8000/api/report/purchases?start_date=2026-04-01&end_date=2026-04-30" \
  -H "Authorization: Bearer TOKEN"
```

Dengan filter product_id:
```bash
curl -X GET "http://127.0.0.1:8000/api/report/purchases?start_date=2026-04-01&end_date=2026-04-30&product_id=1" \
  -H "Authorization: Bearer TOKEN"
```

---

## Database Tables

- **products**: id, name, price, timestamps
- **purchases**: id, date, total_price, timestamps
- **purchase_items**: id, purchase_id, product_id, qty, price, timestamps
- **users**: id, name, email, password, timestamps

## Store Procedure

### sp_report_purchases
```sql
CALL sp_report_purchases('2026-04-01', '2026-04-30', NULL)
```

Parameter:
- `start_date` (DATE) - Filter tanggal mulai
- `end_date` (DATE) - Filter tanggal akhir
- `product_id` (BIGINT UNSIGNED, nullable) - Filter produk (NULL = semua produk)

Output columns:
- `tanggal` - Tanggal pembelian
- `nama_produk` - Nama produk
- `total_transaksi` - Jumlah transaksi untuk tanggal & produk tersebut
- `total_qty` - Total qty terjual
- `total_amount` - Total amount/revenue

---

## Response Format

Semua API return JSON dengan format:
```json
{
  "success": true,
  "data": { ... }
}
```
