<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tariffs')->insert([
            'name' => 'Быстрый старт',
            'code' => 'quick start',
            'price' => '0',
            'contacts' => 30,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('tariffs')->insert([
            'name' => 'Малый бизнес',
            'code' => 'small business',
            'price' => '5400',
            'contacts' => 300,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('tariffs')->insert([
            'name' => 'Средний бизнес',
            'code' => 'middle business',
            'price' => '15000',
            'contacts' => 1000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('tariffs')->insert([
            'name' => 'Большой бизнес',
            'code' => 'big business',
            'price' => '36000',
            'contacts' => 3000,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        DB::table('tariffs')->insert([
            'name' => 'Персонал',
            'code' => 'personal',
            'price' => '0',
            'contacts' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
