Rulare locala

1. Pune folderul "L6" in htdocs (XAMPP) sau www (WAMP).
2. Porneste serverul Apache.
3. Deschide in browser: http://localhost/L6/L6.html
4. Formularul trimite prin fetch la api.php si actualizeaza lista in timp real.
5. Fisierul SQLite "students.db" va fi creat automat in acelasi folder si trebuie sa aiba drepturi de scriere.
6. Pentru testare directa API: GET http://localhost/L6/api.php va returna JSON cu lista de studenti; POST aceleiasi rute primeste JSON {"nume":"...","an":1,"media":9.5} si va returna studentul adaugat.
