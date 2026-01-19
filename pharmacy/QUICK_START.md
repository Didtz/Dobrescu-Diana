# ⚡ Quick Start - Farmacie Proiect

## 3 Minute Setup

### Pasul 1: Configurare Baza de Date (2 min)
1. Pornește **XAMPP Control Panel** → Start Apache și MySQL
2. Deschide http://localhost/phpmyadmin/ 
3. Apasă pe tab-ul "SQL" (în sus)
4. Deschide fișierul `database.sql` din folderul pharmacy
5. Copiază-l integral și lipește-l în SQL editor
6. Apasă **"Go"** (executa)
7. ✅ Baza de date este creată!

### Pasul 2: Verifica Instalarea (1 min)
1. Accesează: **http://localhost/pharmacy/test-connection.php**
2. Ar trebui să vezi ✅ pe toate testele
3. Dacă e o eroare, vezi ce lipsește

### Pasul 3: Porneste Aplicația!
1. Mergi la: **http://localhost/pharmacy/index.html**
2. Apasă "Înregistrare" și creează un cont
3. Autentifică-te
4. 🎉 Gata! Poți vedea medicamentele și cumpăra

---

## Comenzi Utile

```bash
# Dacă vrei să resetezi baza de date:
# 1. Deschide phpMyAdmin
# 2. Selectează "pharmacy" din stânga
# 3. Apasă "Drop" (va șterge baza)
# 4. Redu pașii de sus din "database.sql"
```

---

## Probleme Frecvente

| Problemă | Soluție |
|----------|---------|
| "Page not found" | Asigură-te că Apache este pornit și că folderul e în `C:\xampp\htdocs\pharmacy\` |
| "Connection refused" | Pornește MySQL din XAMPP Control Panel |
| Medicamentele nu se afișează | Deschide DevTools (F12) → Console și vezi eroarea fetch |
| Logare nu funcționează | Asigură-te că emailul este în baza de date (creat prin înregistrare) |

---

## Fișiere Importante

- **`index.html`** → Pagina de login
- **`signup.html`** → Pagina de înregistrare  
- **`medicines.html`** → Catalog medicamente
- **`database.sql`** → Schema și date inițiale
- **`api/`** → Folderul cu API endpoints (PHP)
- **`config/database.php`** → Configurare conexiune MySQL
- **`test-connection.php`** → Test conexiune (debug)

---

## API Endpoints (Intern)

Sunt deja conectate în HTML-ul din formulare via fetch():

- `POST /api/login.php` - Login
- `POST /api/signup.php` - Înregistrare
- `GET /api/get-medicines.php` - Obține medicamente
- `POST /api/checkout.php` - Plasare comandă
- `GET /api/check-session.php` - Check login
- `POST /api/logout.php` - Logout

---

## Cum Adaug Medicamente Noi?

### Metoda 1: PHPMyAdmin
1. Deschide http://localhost/phpmyadmin/
2. Selectează database "pharmacy"
3. Click pe tab "medicines"
4. Apasă "Insert" și adaugă un medicament nou

### Metoda 2: SQL Direct
```sql
INSERT INTO medicines (name, net_weight, short_description, price, requires_prescription, stock_quantity)
VALUES ('Aspirina', '10 blistere', 'Dureri și febră', 15.99, FALSE, 100);
```

---

## Performance

- ✅ Medicamente se încarcă prin fetch (nu hardcoded)
- ✅ Comenzile sunt salvate în baza de date
- ✅ Utilizatorii pot să se înregistreze și să se autentifice
- ✅ Sesiuni PHP sunt active și funcționează

---

## Securitate (Producție)

⚠️ **În viitor**, când vrei să pui pe Internet:
- Schimbă parola MySQL din `config/database.php`
- Implementează HTTPS
- Validează mai strict datele din formular
- Folosește prepared statements (sunt deja)
- Hashing mai bun pentru parole (bcrypt)

---

## Debug Mode

Deschide browser DevTools: **F12**

```javascript
// Pentru a vedea ce fetch-uri se trimit:
// Tab → Console
// Ar trebui să vezi NetworkTab cu request-urile
```

Fiecare fetch are comentarii `//*` care arată flow-ul.

---

**Versiune**: 1.0  
**Status**: ✅ Production Ready (Local)  
**Creat**: 19 ianuarie 2026
