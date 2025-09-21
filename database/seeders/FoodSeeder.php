<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Food;

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Food::create(['name' => 'Nasi Goreng', 'price' => 15000, 'category' => 'Makanan', 'description' => 'Nasi goreng spesial', 'is_active' => true]);
        Food::create(['name' => 'Mie Goreng', 'price' => 12000, 'category' => 'Makanan', 'description' => 'Mie goreng ayam', 'is_active' => true]);
        Food::create(['name' => 'Es Teh', 'price' => 5000, 'category' => 'Minuman', 'description' => 'Es teh manis', 'is_active' => true]);
    }
}
