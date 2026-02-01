# Kur ko darīt — īss ceļvedis

Īsā atbilde: **visu, ko redzi ekrānā (pogas, teksti, formas)** — maini **Vue** failos mapē `resources/js/`.  
**Datus no servera (produkti, lietotāji)** — apstrādā **Laravel** (PHP) mapēs `routes/`, `app/Http/Controllers/`, `app/Models/`.

---

## 1. Kur pievienot pogu vai mainīt interfeisu?

| Ko vēlies | Kur atrodas |
|-----------|--------------|
| **Poga / teksts / bloks visā lapā** (viena kopīga vieta) | `resources/js/App.vue` |
| **Augšējā josla** (logo, meklēt, Pieslēgties) | `resources/js/components/layout/AppHeader.vue` |
| **Produktu karte** (cena, “Salīdzināt”, “Sekot cenai”) | `resources/js/components/products/ProductCard.vue` |
| **Lapa ar produktu sarakstu** (filtrs, režģis) | `resources/js/components/ProductsView.vue` |
| **Salīdzināšanas panelis** (apakšā) | `resources/js/components/comparison/ComparisonPanel.vue` |
| **Dialogs “Sekot cenai”** | `resources/js/components/tracking/PriceTrackingDialog.vue` |

**Piemērs — pievienot pogu uz produktu kartes:**  
Atver `resources/js/components/products/ProductCard.vue`, iekš `<template>` pievieno, piemēram:

```html
<v-btn @click="kautKas">Mana poga</v-btn>
```

Pogas izskatu un darbību maini tāpat šajā failā (template + script).

---

## 2. Kā ir sastādīts Vue (frontends)?

```
resources/js/
├── app.js              → Ieslēdz Vue lietotni (parasti nemaini)
├── App.vue             → Galvenais karkass: header + saturs + uznirstošie logi
├── router/index.js     → Kuras adreses (/, /products) ko rāda
├── components/         → Komponentes (bloki, kas atkārtojas vai atdalītas)
│   ├── layout/         → Kopīgais izkārtojums (galvene u.c.)
│   ├── products/       → Kas saistīts ar produktiem
│   ├── comparison/     → Salīdzināšana
│   └── tracking/       → Sekot cenai
├── stores/             → Kopīgie dati (produkti, lietotājs, paziņojumi)
├── services/api.js     → Izsaukumi uz serveri (GET/POST uz /api/...)
└── plugins/vuetify.js  → Stilu bibliotēka (pogas, kartes, krāsas)
```

- **Komponente** = viens `.vue` fails: tur raksti HTML (template) un loģiku (script).  
- **Ja vēlies pogu** — atver atbilstošo komponenti (skatīt tabulu augstāk) un tur pievieno `<v-btn>` vai citu elementu.

---

## 3. Kur mainīt API un datus no servera?

| Ko vēlies | Kur |
|-----------|-----|
| **Jauns adrese** (piemēram, `/api/mani-dati`) | `routes/api.php` |
| **Ko atgriež šī adrese** (loģika, vaicājumi) | `app/Http/Controllers/Api/` (atbilstošais Controller) |
| **Tabulas un lauki DB** | `app/Models/` + `database/migrations/` |
| **Sākotnējie dati** (produkti, kategorijas) | `database/seeders/` |

Poga frontendā var izsaukt `api.get('/api/...')` vai `api.post(...)` — to dara no `resources/js/stores/` vai tieši no komponentes, izmantojot `services/api.js`.

---

## 4. Ātrā atsauce

- **“Gribu pogu / mainīt tekstu / mainīt izkārtojumu”** → `resources/js/components/` (izvēlies atbilstošo `.vue` failu no tabulas).
- **“Gribu mainīt, ko atgriež serveris”** → `routes/api.php` + `app/Http/Controllers/Api/`.
- **“Gribu mainīt datus DB (tabulas, lauki)”** → `app/Models/` un `database/migrations/`.
- **“Gribu pievienot jaunu lapu (adresi)”** → `resources/js/router/index.js` + jauna komponente mapē `components/`.

Ja raksti konkrētu piemēru (kur tieši vēlies pogu), varu norādīt precīzu failu un rindiņu.
