<!DOCTYPE html>
<html>
<head>
    <title>Widget</title>
</head>

<<<<<<< HEAD

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
=======
<body>
    <form id="contact-form" method="POST">
        <p>Contact Us</p><br>

        <label>name:</label><br>
        <input type="text" id="name" name="name" required><br>

        <label>email:</label><br>
        <input type="email" id="email" name="email" required><br>

        <label>phone:</label><br>
        <input type="text" id="phone" name="phone" required><br>

        <label>topic:</label><br>
        <input type="text" id="topic" name="topic" required><br>

        <label>text:</label><br>
        <textarea id="text" name="text" required></textarea><br>

        <div id="dropzone" style="border:1px dashed black; padding:20px; margin-top:10px;">
            Drop file here
        </div>

        <p id="file-name"></p>

        <button type="submit">send</button>
    </form>
</body>

<script>
let selectedFile = null;

const dropzone = document.getElementById('dropzone');
const fileNameDisplay = document.getElementById('file-name');

dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
});

dropzone.addEventListener('drop', (e) => {
    e.preventDefault();

    const file = e.dataTransfer.files[0];
    if (file) {
        selectedFile = file;
        console.log('Selected file:', file);
        fileNameDisplay.textContent = file.name;
    }
});

document.getElementById('contact-form').addEventListener('submit', async function(event) {
    event.preventDefault();

    const form = this;
    const formdata = new FormData();

    formdata.append('name', form.name.value);
    formdata.append('email', form.email.value);
    formdata.append('phone', form.phone.value);
    formdata.append('topic', form.topic.value);
    formdata.append('text', form.text.value);

    if (selectedFile) {
        formdata.append('attachment', selectedFile);
    }

    for (let [key, value] of formdata.entries()) {
        console.log(key, value);
    }

    const resp = await fetch('/api/tickets', {
        method: 'POST',
        body: formdata
    });

    const data = await resp.json();
    console.log(data);
});
</script>
</html>
>>>>>>> tickets-logic
