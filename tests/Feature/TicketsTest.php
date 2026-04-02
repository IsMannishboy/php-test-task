<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Customer;
use App\Models\Ticket;

class TicketControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_ticket_with_new_customer()
    {
        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'topic' => 'Support',
            'text' => 'Help me please',
            'attachment' => UploadedFile::fake()->image('test.jpg'),
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('customers', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'phone' => '123456789',
        ]);

        $this->assertDatabaseHas('tickets', [
            'topic' => 'Support',
            'text' => 'Help me please',
            'status' => 'new',
        ]);
    }

    public function test_it_reuses_existing_customer()
    {
        $customer = Customer::factory()->create([
            'email' => 'john@example.com',
        ]);

        $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '999999999',
            'topic' => 'Support',
            'text' => 'Second ticket',
        ]);

        $this->assertEquals(1, Customer::count());
    }

    public function test_it_uploads_attachment()
    {
        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'topic' => 'Support',
            'text' => 'With file',
            'attachment' => $file,
        ]);

        $response->assertStatus(201);

        $ticket = Ticket::first();

        $this->assertTrue($ticket->hasMedia('attachments'));
    }
}