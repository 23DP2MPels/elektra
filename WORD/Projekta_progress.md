# Projekta „Elektra” progress

Dokuments sastādīts pēc **Uzdevumu nostādnes (Elektra, Māris Pelšs)**. Atspoguļo faktisko darba stāvokli bez pārspīlējuma.

---

## Vispārīga piezīme

Lielākā daļa darba veikta **pēdējās 3 dienās**. Pirms tam projekts gandrīz netika virzīts — tāpēc zemāk esošais saraksts ir īss un atklāts.

---

## Ko ir izdarīts

### Vide un projekta struktūra
- Laravel + Vue 3 + Vite + Vuetify + Pinia iestatīti un darbojas
- Kods no sākotnējās struktūras (“project withpur structure”) pārnests uz Laravel mapēm (migrācijas, modeļi, API kontrolieri, Vue komponentes, stori, servisi)
- `npm run dev` un `php artisan serve` palaiž aplikāciju; API pieejams zem `/api`

### Backend (Laravel)
- **Migrācijas:** lietotāji, kategorijas, veikali, preces, specifikācijas, cenu vēsture, sekotās preces, labojumi, paziņojumi
- **Modeļi:** User, Product, Category, Store, Specification, PriceHistory, Correction, Notification
- **API:** maršruti un kontrolieri reģistrācijai/ienākšanai, produktiem, kategorijām, veikaliem, salīdzināšanai, sekotajām precēm, labojumiem, paziņojumiem
- **Autentifikācija:** Laravel Sanctum (API tokeni)
- **Tiesības:** CorrectionPolicy (apstiprināt labojumus), admin/editor pēc lauka `loma`
- **Sējēji:** CategorySeeder, StoreSeeder, ProductSeeder — ~17 testa preces ar specifikācijām; DatabaseSeeder tos izsauc

### Frontend (Vue)
- SPA: galvenā lapa → `/products`
- Komponentes: AppHeader, ProductCard, ProductsView, ComparisonPanel, PriceTrackingDialog
- Stori (Pinia): auth, products, snackbar
- API izsaukumi caur `services/api.js` (axios)
- Produktu kartēs: cenas formatēšana, pogas “Salīdzināt” un “Sekot cenai”
- Salīdzināšanas panelis — atveras ar pogu “Salīdzināt izvēlēto (N)”, nevis automātiski; izvēle nesākas no nulles aizverot paneli

### Dokumentācija
- **README.md** — instalācija, palaišana, īsa struktūra
- **KU_KO_ATBILD.md** — īss ceļvedis: kur mainīt UI, API, DB

---

## Ko vēl nav darīts / daļēji

- **Lietotāja saskarne:** reģistrācijas/ienākšanas formas, profils, “Sekot cenai” pilna integrācija ar backend (API ir, UI daļēji)
- **Redaktora/admin funkcijas:** preču/kategoriju/veikalu CRUD no lapas, labojumu apstiprināšana UI
- **Paziņojumi:** API ir, attēlošana un lasīšana frontendā nav pabeigta
- **Testi:** pamata Laravel testi palikuši; projekta specifiski testi nav rakstīti
- **Scraping / cenu savākšana:** tikai struktūra (cenu vēsture, sekotās preces); paša savākšanas loģikas nav

---

## Kopsavilkums

| Apgabals            | Stāvoklis        |
|---------------------|------------------|
| Vide, struktūra     | Gatavs           |
| DB, modeļi, API     | Gatavs           |
| Frontend — produkts | Darbojas         |
| Salīdzināšana       | Darbojas         |
| Auth (backend)      | Gatavs           |
| Auth (frontend)     | Daļēji           |
| Sekot cenai (UI)    | Daļēji           |
| Labojumi / admin    | API gatavs, UI nē |
| Paziņojumi          | API gatavs, UI nē |
| Testi               | Nav              |
| Cenu savākšana      | Nav              |

Progress atbilst tam, ka intensīvākais darbs notika pēdējās trīs dienās; pārējais laiks praktiski nav izmantots projekta virzīšanai.
