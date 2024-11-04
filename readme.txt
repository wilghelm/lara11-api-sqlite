 To launch app you need

 -clone repository
 - >cd lara11-api-sqlite
 - >composer install
- >cp .env.example .env
- >php artisan key:generate
- >php artisan migrate
- >php artisan db:seed
- >composer run dev

Use rest endpoints you can by link
 http://localhost:8000/api/documentation

To launch test
 >php artisan test

