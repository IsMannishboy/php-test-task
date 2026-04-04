<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Ticket;

class TicketSeeder extends Seeder{
       public function run(): void
{
    $customer = Customer::factory()->create();

    Ticket::factory()
        ->count(5)
        ->for($customer, 'customer')
        ->create();
}
}
