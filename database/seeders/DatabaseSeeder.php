<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        if (DB::table('users')->count() === 0) {
            $this->call([
                UserSeeder::class,
            ]);
        }   

        if(Schema::hasTable('attributes') && Schema::hasTable('attribute_translations')){
            if (DB::table('attributes')->count() === 0 && DB::table('attribute_translations')->count() === 0) {
                $this->call([
                    AttributeSeeder::class,
                ]);
            }   
        }

        if(Schema::hasTable('attribute_values')){
            if (DB::table('attribute_values')->count() === 0) {
                $this->call([
                    AttributeValueSeeder::class,
                ]);
            }   
        }
        
        // $this->call([
        //     AttributeValueSeeder::class,
        // ]);




    }
}
