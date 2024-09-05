<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form with E-Signature</title>
    <link rel="stylesheet" href="styles.css">
</head>
<style>
    body {
    font-family: Arial, sans-serif;
}

.container {
    width: 50%;
    margin: auto;
    text-align: center;
}

.signature-container {
    margin: 20px 0;
}

canvas {
    border: 1px solid #000;
}

</style>
<body>
    <div class="container">
        <h1>Registration Form</h1>
        <form id="registration-form">
            <!-- Other form fields here -->
            <div class="signature-container">
                <label for="signature-pad">E-Signature:</label>
                <canvas id="signature-pad" width="400" height="200"></canvas>
                <button type="button" id="clear-button">Clear</button>
            </div>
            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
    const canvas = document.getElementById('signature-pad');
    const ctx = canvas.getContext('2d');
    const clearButton = document.getElementById('clear-button');
    let drawing = false;

    function startPosition(e) {
        drawing = true;
        draw(e);
    }

    function endPosition() {
        drawing = false;
        ctx.beginPath();
    }

    function draw(e) {
        if (!drawing) return;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';

        ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
    }

    canvas.addEventListener('mousedown', startPosition);
    canvas.addEventListener('mouseup', endPosition);
    canvas.addEventListener('mousemove', draw);

    clearButton.addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    });

    document.getElementById('registration-form').addEventListener('submit', (e) => {
        e.preventDefault();

        // Save the canvas content as a PNG file
        const dataURL = canvas.toDataURL('image/png');
        console.log('Signature saved:', dataURL);

        // Optionally, you can send the dataURL to the server
        // Example: send dataURL via AJAX
    });
});

    </script>
</body>
</html>
