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
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['nosaukums' => $cat['nosaukums']],
                ['apraksts' => $cat['apraksts']]
            );
        }
    }
}
