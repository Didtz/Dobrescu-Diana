# 📊 Project Architecture Overview

## System Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    PHARMACY WEB APPLICATION                │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  FRONTEND (HTML/CSS/JavaScript)                             │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  index.html (Login)                                  │  │
│  │  • Form with email & password                        │  │
│  │  • Fetch POST to api/login.php                       │  │
│  │  • //* markers for fetch implementation              │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  signup.html (Register)                              │  │
│  │  • Form with user details (name, email, password)    │  │
│  │  • Client validation (age >= 18, password match)     │  │
│  │  • Fetch POST to api/signup.php                      │  │
│  │  • //* markers for fetch implementation              │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  medicines.html (Catalog & Shopping)                 │  │
│  │  • Dynamic product grid                              │  │
│  │  • Fetch GET medicines from api/get-medicines.php    │  │
│  │  • Fetch POST orders to api/checkout.php             │  │
│  │  • Filter, search, cart, checkout                    │  │
│  │  • //* markers for all fetch calls                   │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  BACKEND API (PHP)                                          │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  api/login.php                 [POST]                │  │
│  │  ├─ Input: { email, password }                       │  │
│  │  ├─ Validation                                       │  │
│  │  ├─ Database query (users table)                     │  │
│  │  ├─ Password verification (SHA256)                   │  │
│  │  ├─ Session creation                                │  │
│  │  └─ Output: { success, user, message }              │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  api/signup.php                [POST]                │  │
│  │  ├─ Input: { name, surname, email, password, ... }  │  │
│  │  ├─ Validation (all fields, age, email format)       │  │
│  │  ├─ Check duplicate email                            │  │
│  │  ├─ Generate unique username                         │  │
│  │  ├─ Hash password (SHA256)                           │  │
│  │  ├─ Insert into users table                          │  │
│  │  ├─ Create session                                   │  │
│  │  └─ Output: { success, user, message }              │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  api/get-medicines.php          [GET]                │  │
│  │  ├─ Join medicines + categories tables               │  │
│  │  ├─ Aggregate category names per medicine            │  │
│  │  ├─ Format data for frontend                         │  │
│  │  └─ Output: { success, medicines[] }                │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  api/checkout.php               [POST]               │  │
│  │  ├─ Check authentication (session)                   │  │
│  │  ├─ Input: { cart[] }                                │  │
│  │  ├─ Validate cart not empty                          │  │
│  │  ├─ Calculate total                                  │  │
│  │  ├─ Insert order (orders table)                      │  │
│  │  ├─ Insert order items (order_items table)           │  │
│  │  ├─ Transaction handling                             │  │
│  │  └─ Output: { success, order_id, message }          │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  api/check-session.php          [GET]                │  │
│  │  ├─ Verify $_SESSION['logged_in']                    │  │
│  │  └─ Output: { logged_in, user {} }                  │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  api/logout.php                 [POST]               │  │
│  │  ├─ Destroy session                                  │  │
│  │  └─ Output: { success, message }                    │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  config/database.php                                 │  │
│  │  ├─ Database class                                   │  │
│  │  ├─ getConnection() method                           │  │
│  │  ├─ closeConnection() method                         │  │
│  │  └─ XAMPP configuration (root:, pharmacy)            │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  DATABASE (MySQL)                                           │
│  ┌──────────────────────────────────────────────────────┐  │
│  │  Database: pharmacy                                  │  │
│  │                                                       │  │
│  │  ┌─ users                                           │  │
│  │  │  ├─ id (PK)                                      │  │
│  │  │  ├─ username (UNIQUE)                            │  │
│  │  │  ├─ email (UNIQUE)                               │  │
│  │  │  ├─ password (SHA256)                            │  │
│  │  │  ├─ first_name, last_name                        │  │
│  │  │  ├─ phone, address, age, cnp                     │  │
│  │  │  ├─ role (customer/employee/admin)               │  │
│  │  │  └─ is_active (BOOLEAN)                          │  │
│  │  │                                                   │  │
│  │  ├─ medicines                                       │  │
│  │  │  ├─ id (PK)                                      │  │
│  │  │  ├─ name (UNIQUE)                                │  │
│  │  │  ├─ net_weight                                   │  │
│  │  │  ├─ short_description                            │  │
│  │  │  ├─ description                                  │  │
│  │  │  ├─ requires_prescription (BOOLEAN)              │  │
│  │  │  ├─ stock_quantity (INT)                         │  │
│  │  │  ├─ price (DECIMAL)                              │  │
│  │  │  └─ side_effects (TEXT)                          │  │
│  │  │                                                   │  │
│  │  ├─ categories                                      │  │
│  │  │  ├─ id (PK)                                      │  │
│  │  │  ├─ name                                         │  │
│  │  │  └─ description                                  │  │
│  │  │                                                   │  │
│  │  ├─ medicine_categories (Many-to-Many)              │  │
│  │  │  ├─ id (PK)                                      │  │
│  │  │  ├─ medicine_id (FK → medicines)                 │  │
│  │  │  └─ category_id (FK → categories)                │  │
│  │  │                                                   │  │
│  │  ├─ orders                                          │  │
│  │  │  ├─ id (PK)                                      │  │
│  │  │  ├─ user_id (FK → users)                         │  │
│  │  │  ├─ total_amount (DECIMAL)                       │  │
│  │  │  ├─ status (pending/completed/cancelled)         │  │
│  │  │  └─ order_date (TIMESTAMP)                       │  │
│  │  │                                                   │  │
│  │  └─ order_items                                     │  │
│  │     ├─ id (PK)                                      │  │
│  │     ├─ order_id (FK → orders)                       │  │
│  │     ├─ medicine_id (FK → medicines)                 │  │
│  │     ├─ quantity (INT)                               │  │
│  │     ├─ unit_price (DECIMAL)                         │  │
│  │     └─ subtotal (DECIMAL)                           │  │
│  │                                                       │  │
│  │  Sample Data:                                        │  │
│  │  • 12 medicines preloaded                            │  │
│  │  • 11 categories preloaded                           │  │
│  │  • Relationships configured                          │  │
│  └──────────────────────────────────────────────────────┘  │
│                                                              │
└─────────────────────────────────────────────────────────────┘
```

## Data Flow Diagram

```
┌─────────────────┐
│   User Action   │
│  (Login, Register,
│   Buy Item)     │
└────────┬────────┘
         │
         ▼
    ┌─────────┐
    │ Browser │
    │ (JS)    │
    └────┬────┘
         │
         │ //* Fetch API call
         │ (JSON payload)
         │
         ▼
    ┌──────────────┐
    │ PHP Endpoint │
    │ (api/*.php)  │
    └────┬─────────┘
         │
         │ Parse JSON
         │ Validate input
         │
         ▼
    ┌──────────────┐
    │   Database   │
    │   (MySQL)    │
    └────┬─────────┘
         │
         │ Query result
         │
         ▼
    ┌──────────────┐
    │ PHP Response │
    │ (JSON)       │
    └────┬─────────┘
         │
         │ //* Return to frontend
         │
         ▼
    ┌──────────────┐
    │   Browser    │
    │ Update DOM   │
    │ Show message │
    └──────────────┘
```

## Request-Response Flow Examples

### 1. Login Flow
```
Browser → POST /api/login.php
{
  "email": "user@example.com",
  "password": "password123"
}
        ↓
PHP checks database
        ↓
Browser ← Response
{
  "success": true,
  "message": "Autentificare reusita",
  "user": {
    "id": 1,
    "username": "john_doe",
    "first_name": "John",
    "last_name": "Doe",
    "role": "customer"
  }
}
        ↓
JavaScript: Redirect to medicines.html
```

### 2. Load Medicines Flow
```
Browser → GET /api/get-medicines.php
        ↓
PHP queries:
  SELECT m.*, GROUP_CONCAT(c.name) 
  FROM medicines m 
  LEFT JOIN medicine_categories mc
  LEFT JOIN categories c
        ↓
Browser ← Response
{
  "success": true,
  "medicines": [
    {
      "id": 1,
      "name": "Theraflu",
      "price": 35.99,
      "requires_prescription": false,
      "categories": ["cold", "flu"]
    },
    ...
  ]
}
        ↓
JavaScript: Render product grid
```

### 3. Checkout Flow
```
Browser → POST /api/checkout.php
{
  "cart": [
    { "name": "Theraflu", "price": 35.99 },
    { "name": "Paracetamol", "price": 15.99 }
  ]
}
        ↓
PHP:
  1. Check session (user logged in?)
  2. Insert into orders table
  3. Insert into order_items table
  4. Transaction commit
        ↓
Browser ← Response
{
  "success": true,
  "message": "Comanda plasata cu succes",
  "order_id": 42
}
        ↓
JavaScript: Show success message, clear cart
```

## Fetch API Usage Pattern

All fetch calls follow this pattern:

```javascript
//* Description of what fetch does
fetch('api/endpoint.php', {
    method: 'POST' or 'GET',
    headers: {
        'Content-Type': 'application/json'
    },
    //* Send data to server
    body: JSON.stringify({ ... })
})
//* Parse JSON response
.then(response => response.json())
//* Handle successful response
.then(data => {
    if (data.success) {
        // Do something
    } else {
        // Show error
    }
})
//* Handle network errors
.catch(error => {
    console.error('Fetch error:', error);
});
```

## Technology Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| **Frontend** | HTML5 | Structure |
| | CSS3 | Styling & Responsive Design |
| | JavaScript (ES6) | Dynamic behavior & Fetch API |
| **Backend** | PHP 7+ | Server logic & Business logic |
| | MySQLi | Database interaction |
| | Sessions | User authentication |
| **Database** | MySQL 5.7+ | Data storage & Relationships |
| **Server** | Apache (XAMPP) | Web server |
| **Network** | HTTP/REST API | Client-Server communication |

## File Sizes & Performance

| File | Size | Type |
|------|------|------|
| index.html | ~2 KB | HTML |
| signup.html | ~5 KB | HTML |
| medicines.html | ~40 KB | HTML |
| styles.css | ~8 KB | CSS |
| medicine-styles.css | ~3 KB | CSS |
| api/*.php (6 files) | ~15 KB total | PHP |
| database.sql | ~6 KB | SQL |

Load time: < 1 second (local XAMPP)

---

**Created**: 19 January 2026  
**Architecture Version**: 1.0  
**Status**: Production Ready (Local)
