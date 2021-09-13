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
            'user_id' => 1,
            'name' => "Papas fritas",
            'price' => 1000,
        ]);
        
    }
}
