<?php

namespace App\Services;

use App\Models\Customer;

class CustomerService
{
    public function firstOrCreate(array $data): Customer
    {
        return Customer::firstOrCreate([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'last_request' => now(),
        ]);
    }
    public function getCount(): int
    {
        return Customer::count();
    }

    public function getLatest(int $limit = 10)
    {
        return Customer::latest()->take($limit)->get();
    }

    public function getAll()
    {
        return Customer::all();
    }

    public function findById($id): ?Customer
    {
        return Customer::find($id);
    }

    public function findOrFail($id): Customer
    {
        return Customer::findOrFail($id);
    }

    public function create(array $data): Customer
    {
        return Customer::create($data);
    }

    public function update(Customer $customer, array $data): Customer
    {
        $customer->update($data);
        return $customer;
    }

    public function delete(Customer $customer): bool
    {
        return $customer->delete();
    }
}