<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles/alex-styles.css">
<title>Escanear QR</title>
<meta charset="UTF-8">
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
</head>
<body>

<header>
    <h2>Escáner de Código QR</h2>
</header>

<div class="container">

    <div class="card">
        <h3>Escanear Ticket</h3>

        <!-- Contenedor real de cámara -->
        <div id="reader" style="width:100%; max-width:400px; margin:auto;"></div>

        <br>

        <div id="resultado" style="text-align:center; font-weight:bold;"></div>

        <br>

        <button class="btn-primary" onclick="iniciarEscaneo()">
            Activar Cámara
        </button>

    </div>

</div>

<script>

let qrScanner;

function iniciarEscaneo() {

    document.getElementById("resultado").innerHTML = "📷 Activando cámara...";

    qrScanner = new Html5Qrcode("reader");

    qrScanner.start(
        { facingMode: "environment" }, // Cámara trasera
        {
            fps: 10,
            qrbox: 250
        },
        (decodedText) => {

            // Cuando detecta QR correctamente
            document.getElementById("resultado").innerHTML =
                "✅ QR escaneado con éxito";

            qrScanner.stop();

        },
        (errorMessage) => {
            // errores de lectura (los ignoramos)
        }
    ).catch(err => {
        document.getElementById("resultado").innerHTML =
            "❌ Error al abrir la cámara";
        console.log(err);
    });
}

</script>

</body>
</html>