<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        if (DB::table('users')->count() === 0) {
            DB::table('users')->insert($this->users());
        }
    }


    public function users(){

       return [
            [
                'name'=>'admin',
                'username'=>'adminuser',
                'email'=>'admin@gmail.com',
                'role'=>'admin',
                'password'=>bcrypt('password'),
            ],
            [
                'name'=>'user',
                'username'=>'user',
                'email'=>'user@gmail.com',
                'role'=>'user',
                'password'=>bcrypt('password'),
            ]
        ];
    }
}
