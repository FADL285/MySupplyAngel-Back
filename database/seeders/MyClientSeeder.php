<?php

namespace Database\Seeders;

use App\Models\MyClient;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MyClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MyClient::create([
            'name' => 'test',
            'comment' => 'test',
        ]);

        MyClient::create([
            'name' => 'test',
            'comment' => 'test',
        ]);

        MyClient::create([
            'name' => 'test',
            'comment' => 'test',
        ]);

        MyClient::create([
            'name' => 'test',
            'comment' => 'test',
        ]);
    }
}
