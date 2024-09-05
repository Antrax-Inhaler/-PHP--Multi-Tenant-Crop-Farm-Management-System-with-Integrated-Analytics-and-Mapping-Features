<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<body class="hold-transition">
    <script>
        start_loader()
    </script>
    <style>
        html, body {
            height: 100%;
            width: 100%;
        }
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f4f6f9;
        }
        .signature-container {
            position: relative;
            width: 400px;
            height: 400px;
            border: 1px solid gray;
            background-color: white;
        }
        #signature-pad {
            width: 100%;
            height: 100%;
        }
        .signature-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
    </style>
    <div>
        <div class="signature-container">
            <canvas id="signature-pad"></canvas>
        </div>
        <div class="signature-buttons">
            <button type="button" id="clear-signature" class="btn btn-danger">Clear</button>
            <button type="button" id="save-signature" class="btn btn-success">Save</button>
        </div>
    </div>
    <input type="hidden" id="signature" name="signature">

    <!-- jQuery -->
    <script src="<?php echo base_url ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <script>
        $(function() {
            end_loader();

            // Signature pad functionality
            const canvas = document.getElementById('signature-pad');
            const ctx = canvas.getContext('2d');
            let drawing = false;

            function resizeCanvas() {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext('2d').scale(ratio, ratio);
            }

            window.addEventListener('resize', resizeCanvas);
            resizeCanvas();

            canvas.addEventListener('mousedown', (event) => {
                drawing = true;
                const rect = canvas.getBoundingClientRect();
                ctx.moveTo(event.clientX - rect.left, event.clientY - rect.top);
            });

            canvas.addEventListener('mousemove', (event) => {
                if (!drawing) return;
                const rect = canvas.getBoundingClientRect();
                ctx.lineTo(event.clientX - rect.left, event.clientY - rect.top);
                ctx.stroke();
            });

            canvas.addEventListener('mouseup', () => {
                drawing = false;
                ctx.beginPath();
            });

            canvas.addEventListener('mouseout', () => {
                drawing = false;
            });

            document.getElementById('clear-signature').addEventListener('click', () => {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
            });

            document.getElementById('save-signature').addEventListener('click', () => {
                const dataURL = canvas.toDataURL('image/png');
                $.ajax({
                    type: "POST",
                    url: "save_signature.php",
                    data: {
                        imgBase64: dataURL
                    },
                    success: function(data){
                        alert('Signature saved!');
                    }
                });
            });
        });
    </script>
</body>
</html>
