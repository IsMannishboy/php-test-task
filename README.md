#####DESCRIPTION#########

simple crm system is created
this service allows :to send tickets,requset_limmiter implemented so each customer can send only one ticket per day,
manage tickets in admin panel,only manager can visit this page,so added role middleware.here such opperations are allowed:change status,text,topic,filter tickets,check and download files

######STRUCTURE#######

all relationsheeps are provided as it metioned in the task:
tickets have foreign key customer_id and all files binded to their tickets
there are migrations,seeds,

USER SEEDER:
        User::factory()->count(5)->create();
        $user = User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('1234'),
        ]);
        $user->assignRole('admin');
        $manager = User::factory()->create([
            'name' => 'manager',
            'email' => 'manager@example.com',
            'password' => bcrypt('1234'),
        ]);
        $manager->assignRole('manager');

ROLE SEEDER:

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

CUSTOMER SEEDER:
Customer::factory()->count(5)->create();

TICKET SEEDER:
 $customer = Customer::factory()->create();

    Ticket::factory()
        ->count(5)
        ->for($customer, 'customer')
        ->create();

DB SEEDER:

        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CustomerSeeder::class,
            TicketSeeder::class,
        ]);
for roles spatie/laravel-permission is used
this library allows to mange roles and their permissions statefull in db,there special,roles,permissions,model_has_roles tables are created


all logic devided on controllers,models and services,controllers use models only via services 

created special request classes(TicketsWIthFiltersRequest,UpdateTicketRequest) for check post data

added additional middleware : role middleware which chekc if user have a requaired role(manager)

auth via sessions,session storage: postgres

main page divided by components

cors config allows to inplement widget in another domains

spatie/laravel-medialibrary is used for manage files.This library provides a media table where file data is stored
this library is used along with php storage
#####TESTS#####
there future TicketTest class is provided,all tests passed
######LAUNCH INSTRUCTION###########
composer install
php atisan serve
if you want to run in docker 

