<div>
   <h2>Users</h2>
        <p>Total Users: {{ $usersCount }}</p>
        <ul>
                @foreach($users as $user)
            <li>
                {{ $user->name }} - {{ $user->email }}
            </li>
        @endforeach
        </ul> <!-- Because you are alive, everything is possible. - Thich Nhat Hanh -->
</div>