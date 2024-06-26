<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Campaign::create([
            'name' => '2 alana 1 Bedava',
            'description' => 'Sabahattin Ali\'nin Roman kitaplarında 2 üründen 1 tanesi bedava.',
            'discount_type' => 'free_product',
            'discount_value' => 1,
            'conditions' => json_encode(['author' => 'Sabahattin Ali','min_quantity' => 2,'max_free' => 1])
        ]);

        \App\Models\Campaign::create([
            'name' => 'Yerli Yazar İndirimi',
            'description' => 'Yerli Yazar Kitaplarında %5 indirim',
            'discount_type' => 'product_based_percentage',
            'discount_value' => 5,
            'conditions' => json_encode(['author_type' => 'local'])
        ]);

        \App\Models\Campaign::create([
            'name' => '%5 İndirim',
            'description' => '200 TL ve üzeri alışverişlerde sipariş toplamına %5 indirim',
            'discount_type' => 'total_amount_percentage',
            'discount_value' => 5,
            'conditions' => json_encode(['min_total' => 200])
        ]);
    }
}
