<p align="center">
  <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo">
</p>

# 🦷 Dental Backend – Laravel 12

Ovo je backend aplikacija za **Dental sistem**, razvijena u Laravel 12.  
Aplikacija je pripremljena za rad sa frontend SAP dashboard aplikacijama (Vue Notus, Webshop), i koristi **JWT autentifikaciju** i **role-based pristup**.

---

## ✅ Funkcionalnosti

- ✅ JWT autentifikacija (login, register)
- ✅ Email verifikacija
- ✅ Role sistem: `admin`, `company`
- ✅ Kategorije i podkategorije
- ✅ API za povezivanje sa mobilnom/web SAP aplikacijom

---

## ⚙️ Instalacija

### 1. Kloniraj repozitorijum

git clone https://github.com/andjica/dental-backend.git
cd dental-backend

2. Instaliraj PHP pakete

composer install

3. Postavi .env fajl

cp .env.example .env
php artisan key:generate

4. Podesi MySQL konekciju u .env

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dental
DB_USERNAME=root
DB_PASSWORD=
Napravi bazu dental ručno u phpMyAdmin-u.

5. Instaliraj JWT paket

composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
Laravel će automatski generisati JWT_SECRET u .env. 
Po želji dodaj i:
JWT_TTL=1440

6. Migracije i seedovanje
php artisan migrate --seed
Seederi kreiraju:

Roles (admin, company)
Kategorije i podkategorije
Države i gradove (ako koristiš countries.json)

7. Pokreni lokalni server
php artisan serve
🧪 Test ruta
Da proveriš da sve radi:
GET http://127.0.0.1:8000/api/test-api
Očekivani odgovor:
{ "status": "API routes are working!" }

🔐 Autentifikacija i tokeni
Kada korisnik uspešno uradi login ili registraciju, API vraća JWT token.

Authorization: Bearer <token>
🛠️ Zahtevi sistema
PHP 8.2+
Composer
MySQL server (XAMPP / MAMP / phpMyAdmin)
Laravel CLI

📦 Frontend
Ovaj projekat je pripremljen da se poveže sa dva posebna frontend SAP interfejsa:
Admin & Company Dashboard (Vue Notus)
Webshop aplikacija (takođe u Vue)
Frontend SPA koristi ovaj backend kao glavni API servis.
