<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => '管理者１',
            'email' => 'admin-one@test.com',
            'password' =>bcrypt('password'),
            ];
        DB::table('admins')->insert($param);
            $param = [
            'name' => '管理者2',
            'email' => 'admin-two@test.com',
            'password' => bcrypt('password'),
            ];
        DB::table('admins')->insert($param);
            $param = [
            'name' => '管理者３',
            'email' => 'admin-tree@test.com',
            'password' => bcrypt('password'),
            ];
        DB::table('admins')->insert($param);
    }
}
