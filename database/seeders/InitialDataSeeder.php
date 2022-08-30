<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitialDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'email' => 'admin@omindtech.com',
            'name' => 'Admin',
            'password' => Hash::make('admin'),
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }
}
