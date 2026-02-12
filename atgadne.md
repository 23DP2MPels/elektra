# elektra

## Первый запуск (один раз)

В папке проекта выполни по порядку:

```bash
# 1. Скопировать настройки (если ещё не сделано)
cp .env.example .env

# 2. Зависимости PHP (создаёт папку vendor/)
composer install

# 3. Ключ приложения
php artisan key:generate

# 4. Зависимости Node (Vite, Vue, Tailwind)
npm install --legacy-peer-deps

# 5. База данных (SQLite) — миграции
php artisan migrate

# 6. (Neobligāti) Sākotnējie dati + administrators
php artisan db:seed
```
Pēc `db:seed` vari ienākt kā **administrators**: e-pasts `admin@elektra.test`, parole `password` (vai vērtības no `.env`: `ADMIN_EMAIL`, `ADMIN_PASSWORD`). Administrators var pievienot/dzēst preces, kategorijas, veikalus.

**Padarīt esošu lietotāju par administratoru** (ja jau reģistrējies):  
`php artisan elektra:make-admin tavs@epasts.com` — pēc tam pieslēdzies ar šo e-pastu.

Ja datu bāze jau ir aizpildīta un vēlies tikai izveidot admin kontu:  
`php artisan db:seed --class=AdminSeeder`

**Vai datu bāze pārvietosies ar `git pull` uz citu datoru?**  
Nē. Datu bāzes fails (SQLite u.c.) nav repozitorijā. Uz **cita datora** pēc `git pull` palaid: `php artisan migrate` un `php artisan db:seed` — tad būs jauna DB ar sējēju datiem un admin kontu (`admin@elektra.test` / `password`).

**Administrācija:** pieslēdzies kā administrators → galvenē izvēlne **Administrācija** → lapa `/admin` — preču saraksts ar pogām **Rediģēt** un **Dzēst**, kā arī **Pievienot preci**.

## Запуск проекта

Нужны **два терминала**.

**Терминал 1 — Laravel:**
```bash
php artisan serve
```
Сайт будет доступен по адресу: http://localhost:8000

**Терминал 2 — фронт (Vite, стили):**
```bash
npm run dev
```
Оставь этот процесс включённым, чтобы подгружались CSS/JS.

После этого открой в браузере: **http://localhost:8000**

---

## Структура проекта (после переноса из «project withpur structure»)

**Backend (Laravel):**
- **Миграции** — `database/migrations/2024_01_01_*` (lietotaji, kategorijas, veikali, preces, specifikacijas, cenu_vesture, sekotas_preces, labojumi, pazinojumi)
- **Модели** — `app/Models/`: User, Product, Category, Store, Specification, PriceHistory, Correction, Notification
- **API-контроллеры** — `app/Http/Controllers/Api/`: AuthController, ProductController, TrackedProductController, CategoryController, StoreController, CorrectionController, NotificationController
- **Маршруты API** — `routes/api.php` (префикс `/api`), подключены в `bootstrap/app.php`
- **Политика** — `app/Policies/CorrectionPolicy.php` (право approve для редакторов)
- **Auth** — Laravel Sanctum для API-токенов

**Frontend (Vue 3 + Vite + Vuetify + Pinia):**
- **Точка входа** — `resources/js/app.js` (монтирует Vue-приложение в `#app`)
- **Роутер** — `resources/js/router/index.js` (главная → /products)
- **Страницы** — `resources/js/components/ProductsView.vue`
- **Компоненты** — `resources/js/components/layout/AppHeader.vue`, `products/ProductCard.vue`, `comparison/ComparisonPanel.vue`, `tracking/PriceTrackingDialog.vue`
- **Сторы (Pinia)** — `resources/js/stores/auth.js`, `products.js`, `snackbar.js`
- **Сервисы** — `resources/js/services/api.js` (axios, базовый URL из `VITE_API_BASE_URL`)
- **Плагины** — `resources/js/plugins/vuetify.js`

Главная страница (/) отдаёт SPA (`resources/views/app.blade.php`), все пути обрабатывает Vue Router. API доступен по адресу **http://localhost:8000/api** (например, GET /api/products, POST /api/login).