<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Customer;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Spatie\Permission\PermissionRegistrar;
use App\Models\Ticket;
use Illuminate\Support\Facades\Storage;
class TicketsTest extends TestCase
{
    use RefreshDatabase;
    public function SetUpRolesAndPermissions(){
        $permissions = [
            'create tickets',
            'view tickets',
            'delete tickets',
            'update tickets',
        ];

        $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ]);
        $managerRole = Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'web',
        ]);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $adminRole->syncPermissions(Permission::all());
        $managerRole->syncPermissions(['view tickets','update tickets']);
    }

    public function test_it_creates_ticket_with_new_customer_and_check_request_limmiter()
    {
        $response = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'topic' => 'Support',
            'text' => 'Help me please',
            'attachment' => UploadedFile::fake()->create('test.txt', 100),
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
        $responseAgain = $this->postJson('/api/tickets', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '123456789',
            'topic' => 'Support',
            'text' => 'Help me please',
            'attachment' => UploadedFile::fake()->create('test.txt', 100),
        ]);

        $responseAgain->assertStatus(429);

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

    public function test_it_uploads_file()
    {
        $file = UploadedFile::fake()->create('test.txt', 100);

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
    public function test_it_updates_tickets_and_check_role(){
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        //setting roles

        $this->SetUpRolesAndPermissions();
        $user = User::factory()->create([
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => bcrypt('1234'),
            ]);

        $user->assignRole('manager');

        $ticket = Ticket::factory()->create();
        // try to update as a manager
        $response = $this->actingAs($user)->putJson("/api/tickets/{$ticket->id}", [
            'topic' => 'Updated Topic',
            'text' => 'Updated text',
                    'status' => 'processing',
                ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'topic' => 'Updated Topic',
            'text' => 'Updated text',
            'status' => 'processing',
        ]);
        //as admin
        $user->syncRoles('admin');
        $response = $this->actingAs($user)->putJson("/api/tickets/{$ticket->id}", [
            'topic' => 'Updated Topic',
            'text' => 'Updated text',
                    'status' => 'processing',
                ]);

        $response->assertStatus(403);
        // without session
        $response = $this->putJson("/api/tickets/{$ticket->id}", [
            'topic' => 'Updated Topic',
            'text' => 'Updated text',
                    'status' => 'processing',
                ]);

        $response->assertStatus(403);
        // another role
        $response = $this->withSession([
                'role' => 'role', 
                ])->putJson("/api/tickets/{$ticket->id}", [
                    'topic' => 'Updated Topic',
                    'text' => 'Updated text',
                    'status' => 'processing',
                ]);

        $response->assertStatus(403);
    }
    public function test_it_downloads_attachment(){
            $this->SetUpRolesAndPermissions();
             $user = User::factory()->create([
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => bcrypt('1234'),
            ]);

            $user->assignRole('manager');
            Storage::fake('public');

            $ticket = Ticket::factory()->create();

            $file = UploadedFile::fake()->create('test.txt', 100);

            $ticket
                ->addMedia($file)
                ->toMediaCollection('attachments');

            $response = $this->actingAs($user)->get("/api/tickets/{$ticket->id}/download");
            $response->assertStatus(200);
        }
        public function test_download_returns_404_if_ticket_not_found(){
            $this->SetUpRolesAndPermissions();
            $user = User::factory()->create();
            $user->assignRole('manager');

            $response = $this->actingAs($user)->get('/api/tickets/999/download');

            $response->assertStatus(404);
            $response->assertJson([
                'message' => 'Ticket not found',
            ]);
        }
        public function test_download_returns_404_if_no_attachment(){
            $ticket = Ticket::factory()->create();

            $this->SetUpRolesAndPermissions();
            $user = User::factory()->create();
            $user->assignRole('manager');

            $response = $this->actingAs($user)->get("/api/tickets/{$ticket->id}/download");

            $response->assertStatus(404);
            $response->assertJson([
                'message' => 'No attachment found',
            ]);
        }
        public function test_it_filters_tickets_by_date(){
            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
            $this->SetUpRolesAndPermissions();
            $user = User::factory()->create();
            $user->assignRole('manager');

            $oldTicket = Ticket::factory()->create([
                'created_at' => '2016-01-01 00:00:00',
            ]);

            $newTicket = Ticket::factory()->create([
                'created_at' => '2030-01-01 00:00:00',
            ]);

            $response = $this->actingAs($user)->withHeaders([
                'X-Date' => '2025-12-31',
            ])->getJson('/tickets/filters');

            $response->assertStatus(200);

            $response->assertJsonFragment([
                'id' => $newTicket->id,
            ]);

            $response->assertJsonMissing([
                'id' => $oldTicket->id,
            ]);
            
        }
        public function test_it_filters_by_status(){
                            app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

                Ticket::factory()->create(['status' => 'new']);
                Ticket::factory()->create(['status' => 'processing']);
                    $this->SetUpRolesAndPermissions();
                    $user = User::factory()->create();
                    $user->assignRole('manager');
                $response = $this->actingAs($user)->withHeaders([
                    'status' => 'new',
                ])->getJson('/tickets/filters');

                $response->assertStatus(200);

                $response->assertJsonCount(1);
            }
            public function test_it_filters_by_customer_email(){
                $this->SetUpRolesAndPermissions();
                $user = User::factory()->create();
                $user->assignRole('manager');
                $customer = Customer::factory()->create([
                    'email' => 'test@example.com',
                ]);

                $ticket = Ticket::factory()->create([
                    'customer_id' => $customer->id,
                ]);


                $response = $this->actingAs($user)->withHeaders([
                    'email' => 'test@example.com',
                ])->getJson('/tickets/filters');

                $response->assertStatus(200);
                                    $response->assertJsonCount(1);

                
            }
            public function test_it_by_phone(){
                $this->SetUpRolesAndPermissions();
                $user = User::factory()->create();
                $user->assignRole('manager');
                $customer = Customer::factory()->create([
                    'phone' => '123456789',
                ]);

                $ticket = Ticket::factory()->create([
                    'customer_id' => $customer->id,
                ]);
                $response = $this->actingAs($user)->withHeaders([
                    'phone' => '123456789',
                ])->getJson('/tickets/filters');

                $response->assertStatus(200);
                $response->assertJsonCount(1);
            }
            public function test_it_filters_by_customer_id() {
                $this->SetUpRolesAndPermissions();
                $user = User::factory()->create();
                $user->assignRole('manager');
                $customer = Customer::factory()->create();

                $ticket = Ticket::factory()->create([
                    'customer_id' => $customer->id,
                ]);

                $response = $this->actingAs($user)->withHeaders([
                    'customerId' => $customer->id,
                ])->getJson('/tickets/filters');

                $response->assertStatus(200);

                $response->assertJsonCount(1);
            }
}