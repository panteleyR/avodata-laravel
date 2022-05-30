<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = DB::table('users')->insert([
            'name' => 'СуперАдмин',
            'email' => 'superadmin@test.ru',
            'password' => bcrypt('paroltest123'),
            'is_super_admin' => true,
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $admin = DB::table('users')->insert([
            'name' => 'Админ',
            'email' => 'admin@test.ru',
            'password' => bcrypt('paroltest123'),
            'is_super_admin' => false,
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $employee = DB::table('users')->insert([
            'name' => 'Сотрудник',
            'email' => 'employee@test.ru',
            'password' => bcrypt('paroltest123'),
            'is_super_admin' => false,
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
        $analytic = DB::table('users')->insert([
            'name' => 'Анилитик',
            'email' => 'analytic@test.ru',
            'password' => bcrypt('paroltest123'),
            'is_super_admin' => false,
            'email_verified_at' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
