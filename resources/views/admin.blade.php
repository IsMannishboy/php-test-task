<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <h1>Admin Panel</h1>
    <p>Welcome, {{ Auth::user()->name }}!</p>

<x-tickets-list 
    :tickets="$tickets"
    :ticketsCount="$ticketsCount"
    :customersCount="$customersCount"
    :customers="$customers"
    :users="$users"
    :usersCount="$usersCount"
/>   

</body>

</html>