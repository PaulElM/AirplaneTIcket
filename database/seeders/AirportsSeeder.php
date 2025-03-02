<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Airport::insert([
            ['code' => 'JFK', 'name' => 'John F. Kennedy International Airport', 'country' => 'USA'],
            ['code' => 'LAX', 'name' => 'Los Angeles International Airport', 'country' => 'USA'],
        ]);
    }

}
