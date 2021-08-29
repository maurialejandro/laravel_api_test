<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Platos;
use Illuminate\Support\Facades\DB;

class PlatosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('platos')->insert([
            'name' => "Papas fritas",
            'price' => 1000,
        ]);
        DB::table('platos')->insert([
            'name' => "Cazuela",
            'price' => 1500,
        ]);
        DB::table('platos')->insert([
            'name' => "Spaghetti",
            'price' => 2000,
        ]);
    }
}
