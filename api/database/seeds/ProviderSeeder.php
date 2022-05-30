<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProviderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('providers')->insert([
            'name' => 'BASE',
            'on' => true,
            'code' => 'console.log("base really work")',
            'description' => 'Даст Бог воли - даст и боли',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
