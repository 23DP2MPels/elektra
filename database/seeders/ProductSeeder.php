<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Specification;
use App\Models\Store;
use App\Models\PriceHistory;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Viedtālruņi' => Category::where('nosaukums', 'Viedtālruņi')->first()?->kategorijas_id,
            'Portatīvie datori' => Category::where('nosaukums', 'Portatīvie datori')->first()?->kategorijas_id,
            'Televizori' => Category::where('nosaukums', 'Televizori')->first()?->kategorijas_id,
            'Austiņas un skaļruņi' => Category::where('nosaukums', 'Austiņas un skaļruņi')->first()?->kategorijas_id,
            'Plānskrāņi' => Category::where('nosaukums', 'Plānskrāņi')->first()?->kategorijas_id,
        ];

        $stores = Store::all();
        if ($stores->isEmpty()) {
            return;
        }

        $products = $this->getProductsData($categories);
        foreach ($products as $data) {
            $catId = $categories[$data['category']] ?? null;
            if (!$catId) {
                continue;
            }
            $product = Product::updateOrCreate(
                [
                    'nosaukums' => $data['nosaukums'],
                    'modelis' => $data['modelis'] ?? '',
                ],
                [
                    'apraksts' => $data['apraksts'],
                    'razotajs' => $data['razotajs'],
                    'kategorijas_id' => $catId,
                    'attels_url' => $data['attels_url'] ?? null,
                ]
            );

            foreach ($data['specs'] as $param => $value) {
                Specification::updateOrCreate(
                    [
                        'preces_id' => $product->preces_id,
                        'parametrs' => $param,
                    ],
                    ['vertiba' => $value]
                );
            }

            $store = $stores->random();
            PriceHistory::updateOrCreate(
                [
                    'preces_id' => $product->preces_id,
                    'veikala_id' => $store->veikala_id,
                ],
                [
                    'cena' => $data['cena'],
                    'iepriekšējā_cena' => $data['iepriekseja_cena'] ?? null,
                    'pieejams' => true,
                ]
            );
        }
    }

    private function getProductsData(array $categories): array
    {
        return [
            // ========== Viedtālruņi (5) ==========
            [
                'category' => 'Viedtālruņi',
                'nosaukums' => 'Samsung Galaxy S24 Ultra 5G',
                'modelis' => 'SM-S928B',
                'razotajs' => 'Samsung',
                'apraksts' => 'Samsung Galaxy S24 Ultra ir premium viedtālrunis ar 6.8 collu Dynamic AMOLED 2X displeju (3120x1440 px, 120 Hz), titāna korpusu un S Pen. Galvenā kamera: 200 MP platleņķa, 50 MP periskopa teleskops (5x), 12 MP ultrawide, 10 MP teleskops (3x). Priekšējā 12 MP. Snapdragon 8 Gen 3, 12 GB RAM, 256/512 GB/1 TB. Akumulators 5000 mAh, ātrā uzlāde 45 W, bezvadu 15 W. IP68, stereo skaļruņi, Android 14, 7 gadu atjauninājumi.',
                'cena' => 1299.00,
                'iepriekseja_cena' => 1399.00,
                'specs' => [
                    'Ekrāna izmērs' => '6.8"',
                    'Ekrāna izšķirtspēja' => '3120 x 1440 px (QHD+)',
                    'Ekrāna tips' => 'Dynamic AMOLED 2X',
                    'Atjaunināšanas biežums' => '120 Hz',
                    'RAM' => '12 GB',
                    'Iebūvētā atmiņa' => '256 GB',
                    'Galvenā kamera' => '200 MP + 50 MP + 12 MP + 10 MP',
                    'Priekšējā kamera' => '12 MP',
                    'Procesors' => 'Qualcomm Snapdragon 8 Gen 3',
                    'Akumulators' => '5000 mAh',
                    'Ātrā uzlāde' => '45 W',
                    'Operētājsistēma' => 'Android 14',
                    'Svars' => '232 g',
                    'Sim kartes' => '2x Nano SIM / eSIM',
                    'Ūdensaizsardzība' => 'IP68',
                    'S Pen' => 'Jā (iebūvēts)',
                ],
            ],
            [
                'category' => 'Viedtālruņi',
                'nosaukums' => 'Apple iPhone 15 Pro Max',
                'modelis' => 'A3108',
                'razotajs' => 'Apple',
                'apraksts' => 'iPhone 15 Pro Max ar 6.7 collu Super Retina XDR OLED (2796x1290), titāna korpusu un Action pogu. Kamera: 48 MP galvenā ar 2x optisko, 12 MP ultrawide, 12 MP periskopa teleskops 5x. Priekšējā 12 MP ar Autofocus. A17 Pro čips, 8 GB RAM, 256/512 GB/1 TB. Akumulators līdz 29 h atskaņošanai. USB-C 3.2, IP68, Ceramic Shield, iOS 17.',
                'cena' => 1399.00,
                'iepriekseja_cena' => 1499.00,
                'specs' => [
                    'Ekrāna izmērs' => '6.7"',
                    'Ekrāna izšķirtspēja' => '2796 x 1290 px',
                    'Ekrāna tips' => 'Super Retina XDR OLED',
                    'Atjaunināšanas biežums' => '120 Hz (ProMotion)',
                    'RAM' => '8 GB',
                    'Iebūvētā atmiņa' => '256 GB',
                    'Galvenā kamera' => '48 MP + 12 MP + 12 MP (5x)',
                    'Priekšējā kamera' => '12 MP',
                    'Procesors' => 'Apple A17 Pro',
                    'Akumulators' => '~4422 mAh',
                    'Ātrā uzlāde' => '27 W (kabeļa)',
                    'Operētājsistēma' => 'iOS 17',
                    'Svars' => '221 g',
                    'Sim kartes' => '1x Nano SIM + eSIM',
                    'Ūdensaizsardzība' => 'IP68',
                    'Korpusa materiāls' => 'Titāns',
                ],
            ],
            [
                'category' => 'Viedtālruņi',
                'nosaukums' => 'Xiaomi 14 Ultra',
                'modelis' => '24030PN60G',
                'razotajs' => 'Xiaomi',
                'apraksts' => 'Xiaomi 14 Ultra — foto fokuss: Leica četru kameru sistēma (50 MP galvenā 1″ LYT-900, 50 MP ultrawide, 50 MP teleskops 3.2x, 50 MP periskops 5x). 6.73″ LTPO AMOLED 120 Hz, 3200x1440. Snapdragon 8 Gen 3, 16 GB RAM, 512 GB. 5000 mAh, 90 W uzlāde, 80 W bezvadu. IP68, stereo, Android 14, MIUI.',
                'cena' => 999.00,
                'specs' => [
                    'Ekrāna izmērs' => '6.73"',
                    'Ekrāna izšķirtspēja' => '3200 x 1440 px',
                    'Ekrāna tips' => 'LTPO AMOLED',
                    'Atjaunināšanas biežums' => '120 Hz',
                    'RAM' => '16 GB',
                    'Iebūvētā atmiņa' => '512 GB',
                    'Galvenā kamera' => '50 MP (Leica) x4',
                    'Priekšējā kamera' => '32 MP',
                    'Procesors' => 'Snapdragon 8 Gen 3',
                    'Akumulators' => '5000 mAh',
                    'Ātrā uzlāde' => '90 W (vads), 80 W (bezvadu)',
                    'Operētājsistēma' => 'Android 14, MIUI',
                    'Svars' => '219 g',
                    'Sim kartes' => '2x Nano SIM',
                    'Ūdensaizsardzība' => 'IP68',
                ],
            ],
            [
                'category' => 'Viedtālruņi',
                'nosaukums' => 'Google Pixel 8 Pro',
                'modelis' => 'G1MNW',
                'razotajs' => 'Google',
                'apraksts' => 'Pixel 8 Pro ar 6.7″ LTPO OLED (1344x2992, 120 Hz), Tensor G3 čipu un uzlabotu AI foto. Kameras: 50 MP galvenā, 48 MP ultrawide ar makro, 48 MP teleskops 5x. Priekšējā 10.5 MP. 12 GB RAM, 128/256/512 GB. 5050 mAh, 30 W uzlāde, 23 W bezvadu. IP68, Android 14, 7 gadu atjauninājumi.',
                'cena' => 899.00,
                'specs' => [
                    'Ekrāna izmērs' => '6.7"',
                    'Ekrāna izšķirtspēja' => '1344 x 2992 px',
                    'Ekrāna tips' => 'LTPO OLED',
                    'Atjaunināšanas biežums' => '120 Hz',
                    'RAM' => '12 GB',
                    'Iebūvētā atmiņa' => '256 GB',
                    'Galvenā kamera' => '50 MP + 48 MP + 48 MP (5x)',
                    'Priekšējā kamera' => '10.5 MP',
                    'Procesors' => 'Google Tensor G3',
                    'Akumulators' => '5050 mAh',
                    'Ātrā uzlāde' => '30 W',
                    'Operētājsistēma' => 'Android 14',
                    'Svars' => '213 g',
                    'Sim kartes' => '1x Nano SIM + eSIM',
                    'Ūdensaizsardzība' => 'IP68',
                ],
            ],
            [
                'category' => 'Viedtālruņi',
                'nosaukums' => 'OnePlus 12 5G',
                'modelis' => 'CPH2583',
                'razotajs' => 'OnePlus',
                'apraksts' => 'OnePlus 12: 6.82″ LTPO AMOLED 2K 120 Hz, Snapdragon 8 Gen 3, 16 GB RAM, 256 GB. Hasselblad kameras: 50 MP Sony LYT-808, 48 MP ultrawide, 64 MP periskopa 3x. Priekšējā 32 MP. 5400 mAh, 100 W SUPERVOOC, 50 W bezvadu. OxygenOS 14, IP65, ātrā uzlāde.',
                'cena' => 799.00,
                'specs' => [
                    'Ekrāna izmērs' => '6.82"',
                    'Ekrāna izšķirtspēja' => '3168 x 1440 px',
                    'Ekrāna tips' => 'LTPO AMOLED',
                    'Atjaunināšanas biežums' => '120 Hz',
                    'RAM' => '16 GB',
                    'Iebūvētā atmiņa' => '256 GB',
                    'Galvenā kamera' => '50 MP + 48 MP + 64 MP (3x)',
                    'Priekšējā kamera' => '32 MP',
                    'Procesors' => 'Snapdragon 8 Gen 3',
                    'Akumulators' => '5400 mAh',
                    'Ātrā uzlāde' => '100 W',
                    'Operētājsistēma' => 'Android 14, OxygenOS 14',
                    'Svars' => '220 g',
                    'Sim kartes' => '2x Nano SIM',
                    'Ūdensaizsardzība' => 'IP65',
                ],
            ],
            // ========== Portatīvie datori (4) ==========
            [
                'category' => 'Portatīvie datori',
                'nosaukums' => 'Apple MacBook Pro 14" M3 Pro',
                'modelis' => 'MRTQ3',
                'razotajs' => 'Apple',
                'apraksts' => 'MacBook Pro 14 collas ar M3 Pro čipu (11‑kodolu CPU, 14‑kodolu GPU), 18 GB vienotās atmiņas, 512 GB SSD. Liquid Retina XDR displejs 3024x1964, ProMotion 120 Hz. Ierobežots skaņas izvads, MagSafe 3, Thunderbolt 4 x3, HDMI, SDXC. macOS Sonoma. Ilga baterijas darbība.',
                'cena' => 2199.00,
                'specs' => [
                    'Displeja izmērs' => '14.2"',
                    'Izšķirtspēja' => '3024 x 1964 px',
                    'Procesors' => 'Apple M3 Pro (11-core CPU, 14-core GPU)',
                    'RAM' => '18 GB (vienota atmiņa)',
                    'Glabāšana' => '512 GB SSD',
                    'Videokarte' => 'M3 Pro 14-core GPU',
                    'Operētājsistēma' => 'macOS Sonoma',
                    'Baterija' => 'Līdz 17 h video atskaņošana',
                    'Svars' => '1.55 kg',
                    'Porti' => 'Thunderbolt 4 x3, HDMI, SDXC, MagSafe 3',
                    'Klaviatūra' => 'Backlit ar Touch ID',
                    'Kamera' => '1080p FaceTime HD',
                ],
            ],
            [
                'category' => 'Portatīvie datori',
                'nosaukums' => 'Dell XPS 15 9530',
                'modelis' => 'XPS9530-7342',
                'razotajs' => 'Dell',
                'apraksts' => 'Dell XPS 15: 15.6″ OLED 3.5K (3456x2160) touch displejs, Intel Core i7-13700H, 32 GB DDR5, 1 TB NVMe SSD, NVIDIA GeForce RTX 4050 6 GB. Alumīnija korpuss, backlit klaviatūra, Windows 11 Pro. 86 Wh baterija, Thunderbolt 4, USB-C, pilna HD kamera.',
                'cena' => 1899.00,
                'specs' => [
                    'Displeja izmērs' => '15.6"',
                    'Izšķirtspēja' => '3456 x 2160 px (OLED)',
                    'Procesors' => 'Intel Core i7-13700H (14 kodoli)',
                    'RAM' => '32 GB DDR5',
                    'Glabāšana' => '1 TB NVMe SSD',
                    'Videokarte' => 'NVIDIA GeForce RTX 4050 6 GB',
                    'Operētājsistēma' => 'Windows 11 Pro',
                    'Baterija' => '86 Wh',
                    'Svars' => '1.86 kg',
                    'Porti' => 'Thunderbolt 4 x2, USB-C, HDMI 2.0',
                    'Klaviatūra' => 'Backlit',
                    'Kamera' => '1080p',
                ],
            ],
            [
                'category' => 'Portatīvie datori',
                'nosaukums' => 'Lenovo ThinkPad X1 Carbon Gen 11',
                'modelis' => '21HM006CLV',
                'razotajs' => 'Lenovo',
                'apraksts' => 'ThinkPad X1 Carbon 11. paaudze: 14″ 2.8K OLED (2880x1800) vai FHD+, Intel Core i7-1365U, 16 GB LPDDR5, 512 GB SSD. Iegrauzta klaviatūra, TrackPoint, lieliska taustiņu gaita. 57 Wh baterija, Thunderbolt 4, HDMI 2.1, Windows 11 Pro. Karbons un magnijs, ~1.12 kg.',
                'cena' => 1699.00,
                'specs' => [
                    'Displeja izmērs' => '14"',
                    'Izšķirtspēja' => '2880 x 1800 px (OLED)',
                    'Procesors' => 'Intel Core i7-1365U',
                    'RAM' => '16 GB LPDDR5',
                    'Glabāšana' => '512 GB SSD',
                    'Videokarte' => 'Intel Iris Xe (iegulta)',
                    'Operētājsistēma' => 'Windows 11 Pro',
                    'Baterija' => '57 Wh',
                    'Svars' => '~1.12 kg',
                    'Porti' => 'Thunderbolt 4 x2, USB-A, HDMI 2.1',
                    'Klaviatūra' => 'Backlit, TrackPoint',
                    'Kamera' => '1080p ar privātuma vāciņu',
                ],
            ],
            [
                'category' => 'Portatīvie datori',
                'nosaukums' => 'ASUS ROG Zephyrus G16',
                'modelis' => 'GU603VI',
                'razotajs' => 'ASUS',
                'apraksts' => 'ROG Zephyrus G16 — gaming klēpjdators: 16″ 2.5K 165 Hz IPS, Intel Core i9-13900H, 32 GB DDR5, 1 TB SSD, NVIDIA GeForce RTX 4070 8 GB. Per-key RGB, Ānis skaļruņi, Windows 11. 90 Wh baterija, ļoti plāns korpuss gaming segmentam.',
                'cena' => 2299.00,
                'specs' => [
                    'Displeja izmērs' => '16"',
                    'Izšķirtspēja' => '2560 x 1600 px',
                    'Atjaunināšanas biežums' => '165 Hz',
                    'Procesors' => 'Intel Core i9-13900H',
                    'RAM' => '32 GB DDR5',
                    'Glabāšana' => '1 TB NVMe SSD',
                    'Videokarte' => 'NVIDIA GeForce RTX 4070 8 GB',
                    'Operētājsistēma' => 'Windows 11',
                    'Baterija' => '90 Wh',
                    'Svars' => '~2.0 kg',
                    'Porti' => 'USB-C, HDMI 2.1, USB-A x2',
                    'Klaviatūra' => 'Per-key RGB',
                ],
            ],
            // ========== Televizori (3) ==========
            [
                'category' => 'Televizori',
                'nosaukums' => 'Samsung QE65S95C',
                'modelis' => 'QE65S95CAU',
                'razotajs' => 'Samsung',
                'apraksts' => 'Samsung 65″ 4K QD-OLED TV ar 120 Hz, Quantum Dot, Object Tracking Sound+, Dolby Atmos. 4x HDMI 2.1, eARC, HDR10+, HLG. Tizen OS, Smart TV, AirPlay 2, Bixby. Zemais ievads un plaši skatīšanās leņķi.',
                'cena' => 2499.00,
                'specs' => [
                    'Displeja izmērs' => '65"',
                    'Izšķirtspēja' => '3840 x 2160 (4K UHD)',
                    'Panelis' => 'QD-OLED',
                    'Atjaunināšanas biežums' => '120 Hz',
                    'HDR' => 'HDR10+, HLG, Dolby Vision',
                    'Skaņa' => 'Object Tracking Sound+, Dolby Atmos',
                    'HDMI' => '4x HDMI 2.1',
                    'Smart TV' => 'Tizen OS',
                    'Wi-Fi' => 'Wi-Fi 5, Bluetooth 5.0',
                    'Kontrasts' => 'Bezgalīgs (OLED)',
                ],
            ],
            [
                'category' => 'Televizori',
                'nosaukums' => 'LG OLED55C3',
                'modelis' => 'OLED55C3LLA',
                'razotajs' => 'LG',
                'apraksts' => 'LG 55″ 4K OLED evo (C3): α9 Gen6 procesors, 120 Hz, Dolby Vision IQ, Dolby Atmos. 4x HDMI 2.1, NVIDIA G-Sync, AMD FreeSync. webOS 23, Magic Remote, ThinQ AI. Zemais enerģijas patēriņš un izcila bilde.',
                'cena' => 1299.00,
                'specs' => [
                    'Displeja izmērs' => '55"',
                    'Izšķirtspēja' => '3840 x 2160 (4K)',
                    'Panelis' => 'OLED evo',
                    'Atjaunināšanas biežums' => '120 Hz',
                    'Procesors' => 'α9 Gen6',
                    'HDR' => 'Dolby Vision IQ, HDR10, HLG',
                    'HDMI' => '4x HDMI 2.1',
                    'G-Sync / FreeSync' => 'Jā',
                    'Smart TV' => 'webOS 23',
                ],
            ],
            [
                'category' => 'Televizori',
                'nosaukums' => 'Sony XR-75X90L',
                'modelis' => 'XR-75X90L',
                'razotajs' => 'Sony',
                'apraksts' => 'Sony 75″ 4K Full Array LED ar XR Cognitive Processor, 120 Hz, XR Triluminos, local dimming. Dolby Vision, IMAX Enhanced, Acoustic Center Sync. Google TV, Chromecast, Apple AirPlay. 4x HDMI 2.1, eARC.',
                'cena' => 1799.00,
                'specs' => [
                    'Displeja izmērs' => '75"',
                    'Izšķirtspēja' => '3840 x 2160 (4K)',
                    'Panelis' => 'Full Array LED',
                    'Atjaunināšanas biežums' => '120 Hz',
                    'Procesors' => 'XR Cognitive Processor',
                    'HDR' => 'Dolby Vision, HDR10, HLG',
                    'HDMI' => '4x HDMI 2.1',
                    'Smart TV' => 'Google TV',
                    'Skaņa' => 'Acoustic Center Sync',
                ],
            ],
            // ========== Austiņas (3) ==========
            [
                'category' => 'Austiņas un skaļruņi',
                'nosaukums' => 'Sony WH-1000XM5',
                'modelis' => 'WH-1000XM5',
                'razotajs' => 'Sony',
                'apraksts' => 'Sony WH-1000XM5 — vadošās NC austiņas ar V1 procesoru, vairākiem mikrofoniem un 30 stundu bateriju. LDAC, DSEE Extreme, 40 mm drivers. Ārkārtīgi labs trokšņu slāpēšanas un skaņas kvalitāte. Salokāms dizains, Quick Attention, Speak-to-Chat.',
                'cena' => 349.00,
                'specs' => [
                    'Tips' => 'Virsgalvas, bezvadu',
                    'Troksņu slāpēšana' => 'Jā (aktīvā)',
                    'Baterija' => 'Līdz 30 h (NC ieslēgts)',
                    'Uzlādes laiks' => '~3 h',
                    'Bluetooth' => '5.2',
                    'Kodeki' => 'LDAC, AAC, SBC',
                    'Diafragma' => '40 mm',
                    'Mikrofoni' => '8 (ziņām, sarunām)',
                    'Svars' => '~250 g',
                    'Salokāms' => 'Jā',
                ],
            ],
            [
                'category' => 'Austiņas un skaļruņi',
                'nosaukums' => 'Apple AirPods Pro (2. paaudze)',
                'modelis' => 'MQD83',
                'razotajs' => 'Apple',
                'apraksts' => 'AirPods Pro 2: aktīvā trokšņu slāpēšana un Adaptive Audio, H2 čips, Personalised Spatial Audio. Uzlādes korpusā — skaņas atradums, U1, lādēšana ar Apple Watch lādētāju. IP54, līdz 6 h klausīšanās ar NC, līdz 30 h ar korpusu.',
                'cena' => 279.00,
                'specs' => [
                    'Tips' => 'Ieslēdzamās (True Wireless)',
                    'Troksņu slāpēšana' => 'Jā (aktīvā)',
                    'Baterija (austiņas)' => 'Līdz 6 h (NC)',
                    'Baterija (korpusā)' => 'Līdz 30 h kopā',
                    'Bluetooth' => '5.3',
                    'Čips' => 'Apple H2',
                    'IP' => 'IP54',
                    'Spatial Audio' => 'Jā (personalised)',
                ],
            ],
            [
                'category' => 'Austiņas un skaļruņi',
                'nosaukums' => 'Sennheiser Momentum 4 Wireless',
                'modelis' => 'M4ME',
                'razotajs' => 'Sennheiser',
                'apraksts' => 'Sennheiser Momentum 4 Wireless: 60 stundu baterija, Adaptive Noise Cancellation, 42 mm drivers, aptX Adaptive, AAC. Smart Control lietotne, Transparency režīms. Augsta skaņas kvalitāte un ērts vilkšanas dizains.',
                'cena' => 329.00,
                'specs' => [
                    'Tips' => 'Virsgalvas, bezvadu',
                    'Troksņu slāpēšana' => 'Jā (Adaptive NC)',
                    'Baterija' => 'Līdz 60 h',
                    'Bluetooth' => '5.2',
                    'Kodeki' => 'aptX Adaptive, AAC, SBC',
                    'Diafragma' => '42 mm',
                    'Svars' => '~293 g',
                    'Salokāms' => 'Jā',
                ],
            ],
            // ========== Plānskrāņi (2) ==========
            [
                'category' => 'Plānskrāņi',
                'nosaukums' => 'Apple iPad Pro 12.9" M2',
                'modelis' => 'MQE23',
                'razotajs' => 'Apple',
                'apraksts' => 'iPad Pro 12.9″ ar M2 čipu, Liquid Retina XDR (mini-LED) displeju 2732x2048, ProMotion 120 Hz. 256 GB / 8 GB RAM. USB-C 4, Face ID, 4 skaļruņi, 5G opcija. Atbalsts Apple Pencil 2 un Magic Keyboard. iPadOS 17.',
                'cena' => 1299.00,
                'specs' => [
                    'Displeja izmērs' => '12.9"',
                    'Izšķirtspēja' => '2732 x 2048 px',
                    'Panelis' => 'Liquid Retina XDR (mini-LED)',
                    'Procesors' => 'Apple M2',
                    'RAM' => '8 GB',
                    'Glabāšana' => '256 GB',
                    'Kameras' => '12 MP platleņķa, 10 MP ultrawide',
                    'Priekšējā kamera' => '12 MP Ultra Wide (Centered Stage)',
                    'Baterija' => 'Līdz 10 h',
                    'Ports' => 'USB-C 4 (Thunderbolt)',
                    'Operētājsistēma' => 'iPadOS 17',
                ],
            ],
            [
                'category' => 'Plānskrāņi',
                'nosaukums' => 'Samsung Galaxy Tab S9+ 5G',
                'modelis' => 'SM-X810',
                'razotajs' => 'Samsung',
                'apraksts' => 'Galaxy Tab S9+: 12.4″ Dynamic AMOLED 2X 120 Hz, Snapdragon 8 Gen 2, 12 GB RAM, 256 GB (paplašināms). S Pen iekļauts, IP68. Četru skaļruņu stereo, Dolby Atmos. 10090 mAh, 45 W uzlāde. Android 13, DeX režīms.',
                'cena' => 899.00,
                'specs' => [
                    'Displeja izmērs' => '12.4"',
                    'Izšķirtspēja' => '2800 x 1752 px',
                    'Panelis' => 'Dynamic AMOLED 2X',
                    'Procesors' => 'Snapdragon 8 Gen 2',
                    'RAM' => '12 GB',
                    'Glabāšana' => '256 GB (microSD)',
                    'S Pen' => 'Iekļauts',
                    'Baterija' => '10090 mAh',
                    'Ūdensaizsardzība' => 'IP68',
                    'Operētājsistēma' => 'Android 13',
                ],
            ],
        ];
    }
}
