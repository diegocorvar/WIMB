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

        <div id="reader" style="width:100%; max-width:400px; margin:auto;"></div>

        <br>

        <div id="resultado" style="text-align:center; font-weight:bold; font-size:18px;"></div>

        <br>

        <button class="btn-primary" onclick="iniciarEscaneo()">
            Activar Cámara
        </button>

    </div>
</div>

<script>

let html5QrCode;

function iniciarEscaneo() {

    const resultado = document.getElementById("resultado");
    resultado.innerHTML = "📷 Activando cámara...";

    html5QrCode = new Html5Qrcode("reader");

    Html5Qrcode.getCameras().then(devices => {

        if (devices && devices.length) {

            const cameraId = devices[0].id;

            html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: 250
                },
                async (decodedText) => {

                    await html5QrCode.stop();

                    resultado.innerHTML = "🔎 Validando boleto...";

                    // Validar en el servidor SIN salir de la página
                    fetch("validar_qr.php?boleto_id=" + decodedText)
                    .then(res => res.text())
                    .then(data => {

                        if(data.includes("válido")){
                            resultado.innerHTML = "✅ BOLETO VÁLIDO";
                            resultado.style.color = "green";
                        } else {
                            resultado.innerHTML = data;
                            resultado.style.color = "red";
                        }

                    });

                }
            );

        } else {
            resultado.innerHTML = "❌ No se encontró cámara";
        }

    }).catch(err => {
        resultado.innerHTML = "❌ Error al acceder a la cámara";
        console.log(err);
    });
}

</script>

</body>
</html>