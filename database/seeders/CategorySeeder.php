<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nosaukums' => 'Viedtālruņi',
                'apraksts' => 'Viedtālruņi un viedtālruņu aksesuāri',
            ],
            [
                'nosaukums' => 'Portatīvie datori',
                'apraksts' => 'Klēpjdatori, ultraportatīvie un darba stacijas',
            ],
            [
                'nosaukums' => 'Televizori',
                'apraksts' => 'TV, monitori un displeji',
            ],
            [
                'nosaukums' => 'Austiņas un skaļruņi',
                'apraksts' => 'Austiņas, kolonnas un audio iekārtas',
            ],
            [
                'nosaukums' => 'Plānskrāņi',
                'apraksts' => 'Planšetdatori un e-grāmatu lasītāji',
            ],
            // Katalogā izmantotās apakškategorijas
            [
                'nosaukums' => 'Telefoni — ierīces',
                'apraksts' => 'Viedtālruņi un mobilās ierīces',
            ],
            [
                'nosaukums' => 'Telefoni — vāciņi',
                'apraksts' => 'Aizsargvāciņi un maciņi telefoniem',
            ],
            [
                'nosaukums' => 'Telefoni — lādētāji',
                'apraksts' => 'Sienas, auto un bezvadu lādētāji telefoniem',
            ],
            [
                'nosaukums' => 'Sadzīves tehnika — ledusskapji',
                'apraksts' => 'Brīvi stāvoši un iebūvējami ledusskapji un saldētavas',
            ],
            [
                'nosaukums' => 'Sadzīves tehnika — cepeškrāsnis',
                'apraksts' => 'Brīvi stāvošas un iebūvējamas cepeškrāsnis',
            ],
            [
                'nosaukums' => 'Cits',
                'apraksts' => 'Citas elektropreces un aksesuāri',
            ],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['nosaukums' => $cat['nosaukums']],
                ['apraksts' => $cat['apraksts']]
            );
        }
    }
}
