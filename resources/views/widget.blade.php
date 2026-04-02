<!DOCTYPE html>
<html>
<head>
    <title>Widget</title>
</head>


<body>
    <form id="contact-form" method="POST">
    <p>Contact Us</p><br>
        <label for="name">name:</label><br>
        <input type="text" id="name" name="name" required><br>
        <label for="email">email:</label><br>
        <input type="email" id="email" name="email" required><br>
        <label for="phone">phone:</label><br>
        <input type="text" id="phone" name="phone" required><br>
        <label for="topic">topic:</label><br>
        <input type="text" id="topic" name="topic" required><br>
        <label for="text">text:</label><br>
        <textarea id="text" name="text" required></textarea><br>
        <label for="attachment">attachment:</label><br>
        <input type="file" id="attachment" name="attachment"><br>
        <button type="submit">send</button>
    </form>
</body>
<script>
    document.getElementById('contact-form').addEventListener('submit', async function(event) {
        event.preventDefault();
        const formdata = new FormData(this);
        const resp = await fetch('/api/tickets', {
            method: 'POST',
            body: formdata
        });
        if (resp.ok) {
            const data = await resp.json();
            console.log('Success:', data);
        } else {
            console.error('Error:', resp.statusText);
        }
    });
</script>
</html>
