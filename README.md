# Innen letöltött (git clone paranccsal) alkalmazás futtatásának lépései:

- composer install
- npm install
- npm run build
- cp .env.example .env
- touch database/database.sqlite
- php artisan key:generate
- php artisan migrate:fresh --seed
- php artisan serve
