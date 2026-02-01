# Dokumenta “Programmrisinājuma plānošana un progress” saturs — Elektra

Atbildes uz PDF failā *Planosana_progress.pdf* norādītajām sadaļām.

---

## 1. Plānotais risinājums

### a. Funkcionālās prasības (ko sistēma dara)

- **Reģistrācija un pieslēgšanās** — lietotāju reģistrācija, ienākšana ar e-pastu/paroli; lomas (lietotājs, redaktors, administrators).
- **Preču katalogs** — preču saraksts ar meklēšanu, filtrēšanu pēc kategorijas; preces karte ar aprakstu, specifikācijām un cenām no veikaliem.
- **Salīdzināšana** — preču izvēle salīdzināšanai un cenu/specifikāciju salīdzināšana vienā skatā.
- **Cenu sekotājs** — iespēja pievienot preci “sekotajām”; paziņojumi par cenu izmaiņām.
- **Labojumi** — lietotāji/redaktori var iesniegt labojumus (preces dati); redaktori/administratori tos apstiprina vai noraida.
- **Paziņojumi** — lasīt paziņojumus, atzīmēt kā izlasītus.
- **Administrēšana (CRUD)** — administratori var pievienot, rediģēt un dzēst preces, kategorijas, veikalus.

### b. Nefunkcionālās prasības (kā sistēma strādā)

- **Paroles un drošība** — Laravel Sanctum API tokeni; paroles hashētas (Laravel default).
- **Piekļuves tiesības** — lomas (`loma`: lietotājs, redaktors, administrators); politikas (piem., labojumu apstiprināšana tikai redaktoram/admin).
- **Saderība** — tīmekļa lietotne (desktop un mobilā pārlūkā); responsīvs dizains ar Vuetify.
- **Dizains** — Vuetify (Material Design), vienots izskats (galvene, kartes, pogas, dialogi).

---

## 2. Atskaites daļa: kas jau ir izdarīts

### a. Front-end progress

**Kas izveidots:**

- **Lapas:** galvenā (`/`) ar pāreju uz produktu lapu; produktu lapa (`/products`) ar režģi un produktu kartēm.
- **Dizains:** Vuetify komponentes (kartes, pogas, dialogi), galvene ar logo, meklēšanas lauku un pogām; salīdzināšanas un sekotāju panelis.
- **Komponentes:** `AppHeader`, `ProductCard`, `ProductsView`, `ComparisonPanel`, `PriceTrackingDialog`; cenu formatēšana (`price.js`).
- **Validācija:** frontendā pamata (formu lauki); pilna validācija vēl nav visur.
- **Responsivitāte:** Vuetify režģis — pielāgojas ekrāna platumam.

**Lapu saraksts un īss apraksts:**

| Lapa / skats        | Apraksts |
|---------------------|----------|
| `/`                 | Sākumlapa; pāreja uz produktu katalogu. |
| `/products`         | Produktu saraksts režģī; katra prece — karte ar nosaukumu, cenu, pogām “Salīdzināt” un “Sekot cenai”. |
| Salīdzināšanas panelis | Apakšā atveras panelis ar izvēlētajām precēm; atveras ar pogu “Salīdzināt izvēlēto (N)”. |
| Dialogs “Sekot cenai” | Uznirstošais logs, lai pievienotu preci sekotajām (UI daļēji savienots ar backend). |

**Trūkst:** reģistrācijas/ienākšanas formu lapas, lietotāja profila lapas, admin/redaktora CRUD lapas, paziņojumu saraksta attēlošana.

---

### b. Back-end progress

**Kas izveidots:**

- **API maršruti** (`routes/api.php`): reģistrācija, ienākšana, iziešana; produkti, kategorijas, veikali; salīdzināšana; sekotās preces; labojumi; paziņojumi; lietotāja profils.
- **Autorizācija:** Laravel Sanctum (`auth:sanctum`); aizsargātie maršruti tikai ielogotiem; `admin` un `approve` (labojumi) pēc lomas un politikas.
- **Datu apstrāde:** kontrolieri izmanto modeļus un atgriež JSON; daļēja kļūdu apstrāde (piem., tukšs masīvs, ja kategorijas/veikali vēl nav).
- **Validācija:** request validācija kontrolieros (piem., reģistrācija, login).
- **Kļūdu apstrāde:** 404 fallback; daļēji — atgriež atbilstošus HTTP kodus.

**CRUD — kas strādā:**

| Resurss      | Create | Read | Update | Delete | Piezīmes |
|--------------|--------|------|--------|--------|----------|
| Lietotāji (reģ.) | ✅ API | ✅ /user | ✅ profils | — | Frontend formu nav. |
| Preces       | ✅ (admin) | ✅ saraksts/1 | ✅ (admin) | ✅ (admin) | CRUD tikai API. |
| Kategorijas  | ✅ (admin) | ✅ | ✅ (admin) | ✅ (admin) | API. |
| Veikali      | ✅ (admin) | ✅ | ✅ (admin) | ✅ (admin) | API. |
| Sekotās preces | ✅ | ✅ | ✅ | ✅ | API; frontend daļēji. |
| Labojumi     | ✅ | ✅ | ✅ (approve/reject) | — | Tikai API. |
| Paziņojumi   | — | ✅ | ✅ (read) | ✅ | API; frontend nav. |

---

### c. Datu bāze

**Uzlabojumi / precizējumi:**

- Tabulas ar svešatslēgām: `preces` → `kategorijas`, `specifikacijas` un `cenu_vesture` → `preces`, `cenu_vesture` → `veikali`; `sekotas_preces`, `labojumi`, `pazinojumi` saistītas ar lietotājiem/precēm. `ON DELETE CASCADE` tur, kur loģiski (piem., preces dzēšana dzēš specifikācijas un cenu vēsturi).
- Identifikatori konsekventi: `kategorijas_id`, `veikala_id`, `preces_id`, `specifikacijas_id`, `cenas_id` u.c.
- Lauki: `decimal(10,2)` cenām; `text` aprakstam; `boolean` pieejamībai; `timestamps` tabulās.

**Normalizācija (3NF):**

- Preces, kategorijas, veikali — atsevišķas tabulas; atkārtojumi (piem., preces dati) nav dublēti.
- Cenu vēsture — atsevišķa tabula ar `preces_id` un `veikala_id`; viena prece vienam veikalam — viena cenas ieraksta rinda laika griezumā.
- Specifikācijas — atsevišķa tabula (parametrs, vērtība) ar `preces_id`.
- Sekotās preces, labojumi, paziņojumi — atsevišķas tabulas ar saites uz lietotājiem/precēm. Atbilst 3NF prasībām; papildu normalizācijas problēmas nav fiksētas.

**Ierobežojumi:**

- **Foreign key** ar `onDelete('cascade')` vai atbilstošu darbību — datu integritāte starp tabulām.
- **NOT NULL** obligātajiem laukiem (piem., `nosaukums`, `preces_id`, `veikala_id`, `cena`).
- **Unique** kur nepieciešams (piem., lietotāju e-pasts reģistrācijā) — Laravel migrācijās var pievienot.
- **Decimal** cenām — izvairās no noapaļošanas kļūdām.

**Kas pabeigts un kas trūkst:**

- **Pabeigts:** tabulas lietotājiem, kategorijām, veikaliem, precēm, specifikācijām, cenu vēsturei, sekotajām precēm, labojumiem, paziņojumiem; migrācijas; sējēji (kategorijas, veikali, ~17 preces ar specifikācijām).
- **Trūkst:** indeksi lielākiem vaicājumiem (ja būs daudz datu); formāli dokumentēta 3NF pārbaude; daļa UNIQUE/CHECK ierobežojumu varētu būt precizēti migrācijās.

---

## 3. Darbu plāns līdz februāra beigām

| Līdz | Sasniedzamais rezultāts |
|-----|--------------------------|
| 1. nedēļa (februāris) | Darbojas reģistrācija un pieslēgšanās no frontenda (formas, pāreja pēc login); lietotāja lomas redzamas/izmantojamas saskarnē. |
| 2. nedēļa | “Sekot cenai” pilnībā integrēts: pievienošana no lapas, saraksts “Manas sekotās preces”; pamata paziņojumu saraksts frontendā (lasīt, atzīmēt). |
| 3. nedēļa | Redaktora/admin CRUD no lapas: preču (vai kategoriju/veikalu) pievienošana, rediģēšana, dzēšana; labojumu apstiprināšana/noraidīšana UI. |
| Līdz 28.02. | Pilnībā integrēts frontend ar backend galvenajām funkcijām; pamata testi (vismaz API/feature testi); sagatavota demonstrācija. |

---

*Dokuments aizpildīts pēc PDF “Planosana_progress.pdf” struktūras. Lielākā daļa pašreizējā progresa veikta pēdējās dienās; plāns februārim atspoguļo nākamos soļus.*
