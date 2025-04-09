<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner QR Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        video {
            width: 100%;
            max-width: 400px;
            margin-top: 20px;
        }
        #qr-result {
            margin-top: 20px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

    <h1>Scanner un QR Code</h1>
    <button id="start-scan">Démarrer le scan</button>
    <button id="stop-scan" disabled>Arrêter le scan</button>
    <br>
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas" style="display: none;"></canvas>
    <p id="qr-result"></p>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const context = canvas.getContext('2d');
        const startBtn = document.getElementById('start-scan');
        const stopBtn = document.getElementById('stop-scan');
        const resultDisplay = document.getElementById('qr-result');

        let stream = null;
        let scanning = false;

        startBtn.addEventListener('click', async () => {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                video.srcObject = stream;
                scanning = true;
                startBtn.disabled = true;
                stopBtn.disabled = false;
                resultDisplay.textContent = "";

                requestAnimationFrame(scan);
            } catch (err) {
                console.error("Erreur d'accès à la caméra: ", err);
                alert("Impossible d'accéder à la caméra.");
            }
        });

        stopBtn.addEventListener('click', () => {
            stopScan();
        });

        function stopScan() {
            scanning = false;
            startBtn.disabled = false;
            stopBtn.disabled = true;
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
        }

        function scan() {
            if (!scanning) return;

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);

            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const qrCode = jsQR(imageData.data, canvas.width, canvas.height);

            if (qrCode) {
                resultDisplay.textContent = `QR Code détecté : ${qrCode.data}`;
                stopScan(); // Stoppe le scan après détection
                return;
            }

            requestAnimationFrame(scan);
        }
    </script>

</body>
</html>
