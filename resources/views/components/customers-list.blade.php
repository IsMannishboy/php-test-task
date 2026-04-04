<div>
     <h2>Customers</h2>
        <p>Total Customers: {{ $customersCount }}</p>
        <ul>
            @foreach($customers as $customer)
                <li>{{ $customer->name }} - {{ $customer->email }}</li>
            @endforeach
        </ul><!-- Simplicity is the ultimate sophistication. - Leonardo da Vinci -->
</div>