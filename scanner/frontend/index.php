<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scanner de Billets - JO 2024</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f1f1f1;
            color: #333;
            text-align: center;
            margin: 20px;
        }
        video {
            width: 100%;
            max-width: 500px;
            border: 3px solid #4CAF50;
            margin-top: 20px;
        }
        #qr-result {
            margin-top: 30px;
            font-weight: bold;
            color: #4CAF50;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 24px;
            cursor: pointer;
            font-size: 18px;
            border-radius: 6px;
            margin: 10px;
        }
        button:disabled {
            background-color: #ccc;
        }
    </style>
</head>
<body>

    <h1>Scanner Votre Billet pour les JO 2024</h1>
    <p>Scannez le QR code sur votre billet pour accéder à l'événement et vérifier sa validité !</p>
    <button id="start-scan">Commencer la Scan</button>
    <button id="stop-scan" disabled>Arrêter le Scan</button>
    <br>
    <video id="video" autoplay playsinline></video>
    <canvas id="canvas" style="display: none;"></canvas>
    <p id="qr-result"></p>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

    <script>
        const videoElement = document.getElementById('video');
        const canvasElement = document.getElementById('canvas');
        const canvasContext = canvasElement.getContext('2d');
        const startScanBtn = document.getElementById('start-scan');
        const stopScanBtn = document.getElementById('stop-scan');
        const resultDisplay = document.getElementById('qr-result');

        let videoStream = null;
        let isScanning = false;

        startScanBtn.addEventListener('click', async () => {
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } });
                videoElement.srcObject = videoStream;
                isScanning = true;
                startScanBtn.disabled = true;
                stopScanBtn.disabled = false;
                resultDisplay.textContent = "";

                requestAnimationFrame(scanQRCode);
            } catch (err) {
                console.error("Erreur d'accès à la caméra: ", err);
                alert("Impossible d'accéder à la caméra. Veuillez réessayer.");
            }
        });

        stopScanBtn.addEventListener('click', () => {
            stopScan();
        });

        function stopScan() {
            isScanning = false;
            startScanBtn.disabled = false;
            stopScanBtn.disabled = true;
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoElement.srcObject = null;
            }
        }

        function scanQRCode() {
            if (!isScanning) return;

            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            canvasContext.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            const imageData = canvasContext.getImageData(0, 0, canvasElement.width, canvasElement.height);
            const qrCode = jsQR(imageData.data, canvasElement.width, canvasElement.height);

            if (qrCode) {
                resultDisplay.textContent = `Billet valide détecté ! Détails : ${qrCode.data}`;
                stopScan(); // Arrête le scan après détection
                return;
            }

            requestAnimationFrame(scanQRCode);
        }
    </script>

</body>
</html>
