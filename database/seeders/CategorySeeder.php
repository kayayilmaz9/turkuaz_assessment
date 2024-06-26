<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\Category::create(['name' => 'Biyografi']);
        \App\Models\Category::create(['name' => 'Bilim']);
        \App\Models\Category::create(['name' => 'Roman']);
    }
}
