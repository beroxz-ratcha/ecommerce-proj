### Installation Guide for Laravel E-commerce Website

- XAMPP (with PHP 8.1+)
- Composer
- Node.js

## Step-by-Step Installation Guide

1. Install XAMPP with PHP 8.1+ เปิดใช้งานเฉพาะ Apache และ MySQL ใน XAMPP

Configure XAMPP

- เปิดไฟล์ php.ini ในโฟลเดอร์ xampp/php
- เปิดใช้งาน extension .zip โดยการค้นหา ;extension=zip และนำเครื่องหมาย ; ออก
- บันทึกไฟล์และรีสตาร์ท Apache ใน XAMPP

2. Install Composer

- `https://getcomposer.org/download/`
- Download Composer-Setup.exe
- เลือก Path ของ php.exe ซึ่งอยู่ใน xampp/php

3. Install Node.js & npm

### Laravel E-commerce Website Overview

E-commerce application นี้ใช้ Laravel สำหรับ Backend และ Vue.js พร้อม Tailwind.css และ Alpine.js สำหรับ Frontend

## Installation

Need MySQL, PHP8.1, Node.js and composer.

### Setting Up the Laravel Website + API

1. Download the project
2. Copy `.env.example` into `.env` and configure database
3. Navigate to the project's root directory using terminal
4. Run `composer install`
5. Set the encryption key by executing `php artisan key:generate --ansi`
6. Run migrations `php artisan migrate --seed` (ข้ามได้ถ้าคุณไม่ได้สร้างฐานข้อมูลใหม่)
7. Start local server by executing `php artisan serve`
8. Install dependencies ของ Node.js Run `npm install`
   Or
   Install dependencies all project `npm run install-all`

### Setting Up the Vue.js Admin Panel

1. Navigate to `backend` folder
2. Run `npm install` ข้ามได้เลยถ้า run `npm run install-all` ไปแล้ว
3. Copy `backend/.env.example` into `backend/.env`
4. ตั้งค่า `VITE_API_BASE_URL` key in `backend/.env` is set to your Laravel API host (Default: http://localhost:8000)
5. Run `npm run dev`

Web http://localhost:8000
App Backend http://localhost:3000 -> API End Point http://localhost:8000

Running the Project

<!-- `php artisan serve` -> Run PHP Server - Web
`npm run web` -> Run Node.js Vite Mix
`npm run dev` -> Run Vue.js \*มีการ setting ใน package.json `cd backend && npm run dev` -->

`npm run install-all`: "npm install --prefix ./backend && npm install"

`npm start`: "npm-run-all --parallel serve:backend serve:web serve:frontend",
`npm run serve:backend`: "php artisan serve", -> Run PHP Server
`npm run serve:web`: "vite --port=3001", -> Run Node.js Vite Mix
`npm run serve:frontend`: "cd backend && npm run dev", -> Run Vue.js

Default User

```
Admin:
Email: admin@gmail.com
Password: admin123

Seller 1:
Email: seller1@gmail.com
Password: seller123

Seller 2:
Email: seller2@gmail.com
Password: seller123

Customer:
Email: test.cus@gmail.com
Password: 1303161as

Customer:
Email: test.cus2@gmail.com
Password: usertest123

```

## ถ้ามีปัญหา ในการ save image run คำสั่งนี้

`php artisan storage:link`

## ถ้ามีปัญหา เกี่ยวกับ Version stripe

`composer require stripe/stripe-php`

## https://mailtrap.io/

สำหรับ สมัคร vertify email demo

## https://dashboard.stripe.com Payment Demo การชำระเงินออนไลน์

- Login เข้าสู่แดชบอร์ด -> ไปที่ Developers -> API keys -> คัดลอก Secret Key และตั้งค่าใน .env

## Code Formatting

`npm run format`

## Setting Up Gmail for Email (.env)

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_specific_password # ไปสร้างที่ Gmail App Password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="${APP_NAME}" # ชื่อที่จะปรากฏในการส่งอีเมล เช่น "Perdis Website"

share `https://drive.google.com/drive/folders/1fy-lT7PnVDeHMD5p99I-s38WaLLRZOVl?usp=sharing`

npm config list
npm config delete prefix
npm install --no-prefix
