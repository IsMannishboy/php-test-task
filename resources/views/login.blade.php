<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

</head>
<body>
    <form id="login-form" method="POST">
        <p>Login</p><br>
        <label for="name">Name:</label><br>
        <input type="text" id="name" name="name" required><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>
</body>
<script>
    document.getElementById('login-form').addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(this);
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        fetch('/auth/login', {
            method: 'POST',
               
            headers: {
        'X-CSRF-TOKEN': token
         },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.user) {
            window.location.href = '/admin';
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
</script>
</html>
