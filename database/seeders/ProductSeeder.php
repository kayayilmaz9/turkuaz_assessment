<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Product::create([
            'category_id' => 3,
            'name' => 'İnce Memed',
            'author' => 'Yaşar Kemal',
            'author_type' => 'local',
            'price' => 48.75,
            'stock' => 10
        ]);

        \App\Models\Product::create([
            'category_id' => 3,
            'name' => 'Tutunamayanlar',
            'author' => 'Oğuz Atay',
            'author_type' => 'local',
            'price' => 90.3,
            'stock' => 20
        ]);

        \App\Models\Product::create([
            'category_id' => 3,
            'name' => 'Kürk Mantolu Madonna',
            'author' => 'Sabahattin Ali',
            'author_type' => 'local',
            'price' => 9.1,
            'stock' => 4
        ]);

        \App\Models\Product::create([
            'category_id' => 3,
            'name' => 'Fareler ve İnsanlar',
            'author' => 'John Steinback',
            'author_type' => 'global',
            'price' => 35.75,
            'stock' => 8
        ]);

        \App\Models\Product::create([
            'category_id' => 3,
            'name' => 'Şeker Portakalı',
            'author' => 'Jose Mauro De Vasconcelos',
            'author_type' => 'global',
            'price' => 33,
            'stock' => 1
        ]);

        \App\Models\Product::create([
            'category_id' => 3,
            'name' => 'Kuyucaklı Yusuf',
            'author' => 'Sabahattin Ali',
            'author_type' => 'local',
            'price' => 10.4,
            'stock' => 2
        ]);
    }
}
