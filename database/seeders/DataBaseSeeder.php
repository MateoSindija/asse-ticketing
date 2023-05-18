<?php

use App\Models\Client;
use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;

class DataBaseSeeder extends Seeder
{
    const NUMBER_OF_ENTRIES = 40;
    // @return void
    public function run()
    {
        User::factory()->count(DataBaseSeeder::NUMBER_OF_ENTRIES)->create();
        Client::factory()->count(DataBaseSeeder::NUMBER_OF_ENTRIES)->create();
        Ticket::factory()->count(DataBaseSeeder::NUMBER_OF_ENTRIES)->create();
        Comment::factory()->count(DataBaseSeeder::NUMBER_OF_ENTRIES)->create();
    }
}
