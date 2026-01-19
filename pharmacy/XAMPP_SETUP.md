# Ghid Configurare Proiect Farmacie - XAMPP

## Pași pentru Configurare și Rulare Locală

### 1. Pregătire XAMPP

1. **Instalează XAMPP** (dacă nu ai instalat deja):
   - Descarcă de la: https://www.apachefriends.org/
   - Instalează cu setări standard

2. **Pornește XAMPP Control Panel**:
   - Porniți Apache (pentru server web)
   - Porniți MySQL (pentru baza de date)

### 2. Crearea Bazei de Date

1. **Deschide phpMyAdmin**:
   - Mergi la: http://localhost/phpmyadmin/
   - Logare cu:
     - Utilizator: `root`
     - Parolă: (lăsată goală pe XAMPP standard)

2. **Creează baza de date**:
   - Apasă pe tab-ul "SQL"
   - Copiază și lipește întregul conținut din fișierul `database.sql`
   - Apasă "Executa" (Go)
   - Ar trebui să vezi mesajul de succes

3. **Verificare**:
   - În panoul din stânga, ar trebui să vezi `pharmacy` database
   - Deschide-o și ar trebui să vezi tabelele: `users`, `medicines`, `categories`, `medicine_categories`, `orders`, `order_items`

### 3. Poziționarea Fișierelor Proiectului

Proiectul trebuie să fie în folderul `htdocs` al XAMPP:

```
C:\xampp\htdocs\
├── pharmacy\
│   ├── index.html
│   ├── signup.html
│   ├── medicines.html
│   ├── styles.css
│   ├── medicine-styles.css
│   ├── database.sql
│   ├── api\
│   │   ├── login.php
│   │   ├── signup.php
│   │   ├── logout.php
│   │   ├── check-session.php
│   │   ├── get-medicines.php
│   │   └── checkout.php
│   └── config\
│       └── database.php
```

### 4. Configurarea Conexiunii la Baza de Date

Fișierul `config/database.php` conține configurația de conectare:

```php
private $host = "localhost";          // Server MySQL
private $db_name = "pharmacy";        // Nume baza de date
private $username = "root";           // Utilizator MySQL
private $password = "";               // Parolă goală (standard XAMPP)
```

Aceste setări sunt deja configurate corect pentru XAMPP standard. Dacă ai schimbat parola MySQL, actualizeaza `$password`.

### 5. Pornirea Serverului Web Local

1. **Acceseaza aplicația în browser**:
   - http://localhost/pharmacy/index.html
   - sau http://localhost/pharmacy/medicines.html

2. **Testarea conexiunii la baza de date**:
   - Incearcă să te înregistrezi cu un utilizator nou
   - Verifica în phpMyAdmin dacă utilizatorul s-a adăugat în tabelul `users`

### 6. Testare Funcționalități

#### Test 1: Înregistrare Utilizator
1. Apasă "Înregistrare"
2. Completează formularul cu datele tale
3. Apasă "Creează Cont"
4. Ar trebui redirecționat la pagina medicamentelor

#### Test 2: Autentificare
1. Revino la https://localhost/pharmacy/index.html
2. Introdu email-ul și parola
3. Apasă "Autentificare"
4. Ar trebui redirecționat la medicamente

#### Test 3: Vizualizare Medicamente
1. Pagina ar trebui să afișeze medicamentele din baza de date
2. Poți filtra după categorie, preț, rețetă
3. Poți căuta după nume

#### Test 4: Adaugă în Coș și Cumpărare
1. Apasă pe un medicament pentru a vedea detalii
2. Apasă "Adaugă în Coș"
3. Apasă pe coșul din colțul drept (🛒)
4. Apasă "Finalizează comanda"
5. Confirmă comanda
6. Ar trebui să vezi mesaj de succes

### 7. Depanare Probleme

#### Eroare: "Conexiune esuata la baza de date"
- Asigură-te că MySQL este pornit în XAMPP
- Verifica dacă username și parola din `config/database.php` sunt corecte
- Verifica dacă baza de date `pharmacy` există în phpMyAdmin

#### Eroare: "Database não encontrada"
- Asigură-te că ai rulat tot scriptul din `database.sql`
- Verifica în phpMyAdmin dacă tabelele au fost create

#### Medicamentele nu se încarcă
- Verifica consolul developer (F12) pentru erori fetch
- Asigură-te că `api/get-medicines.php` este acesibil
- Verifica dacă baza de date are medicamente în tabelul `medicines`

#### Eroare CORS
- Dacă vezi erori de CORS, asigură-te că fișierele PHP au header-urile corecte:
  ```php
  header('Access-Control-Allow-Origin: *');
  ```

### 8. Configurare Port Alternativ (Dacă 80 este ocupat)

1. Deschide `C:\xampp\apache\conf\httpd.conf`
2. Găsește linia: `Listen 80`
3. Schimbă la: `Listen 8080` (sau alt port liber)
4. Restartează Apache
5. Accesează aplicația pe: `http://localhost:8080/pharmacy/`

### 9. Exportare/Backup Baza de Date

Pentru a salva baza de date:
1. Mergi la phpMyAdmin
2. Selectează baza de date `pharmacy`
3. Apasă tab-ul "Export"
4. Apasă "Go" pentru a descărca SQL file

### 10. Comenzi MySQL Utile (Command Line)

Deschide Command Prompt și navighează la folderul XAMPP:

```bash
cd C:\xampp\mysql\bin

# Accesează MySQL
mysql -u root

# Vezi bazele de date
SHOW DATABASES;

# Selectează pharmacy
USE pharmacy;

# Vezi tabelele
SHOW TABLES;

# Vezi utilizatorii
SELECT * FROM users;

# Vezi medicamentele
SELECT * FROM medicines;
```

## Starea Actuală a Proiectului

✅ Baza de date: CREATĂ (users, medicines, categories, orders)
✅ API Endpoints:
   - `/api/login.php` - Autentificare
   - `/api/signup.php` - Înregistrare
   - `/api/get-medicines.php` - Obține medicamente
   - `/api/checkout.php` - Plasare comandă
   - `/api/check-session.php` - Verifică sesiune
   - `/api/logout.php` - Deconectare

✅ Frontend:
   - `index.html` - Login cu fetch API
   - `signup.html` - Înregistrare cu fetch API
   - `medicines.html` - Catalog cu fetch API
   - Sunt marcate locurile de implementare fetch cu `//*`

## Note de Securitate

⚠️ **IMPORTANT - Pentru producție**:
- Schimbă parola MySQL din `config/database.php`
- Folosește HTTPS în loc de HTTP
- Implementează validare mai strictă a datelor
- Implementează protecție CSRF
- Hashing mai securizat pentru parole (bcrypt în loc de SHA256)

## Probleme și Soluții Rapide

| Problemă | Soluție |
|----------|---------|
| Apariția erorilor de sesiune | Asigură-te că sesiunile PHP sunt habitate |
| Medicamentele nu se afișează | Verifica fetch-ul în browser console (F12) |
| Erori la login | Asigură-te că email-ul este introdus corect |
| Comanda nu se salvează | Verifica dacă MySQL este pornit și conectat |

---

**Data creării**: 19 ianuarie 2026
**Status**: Gata pentru rulare locală pe XAMPP
