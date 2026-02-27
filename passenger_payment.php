<?php
$qr_url = null;
$boleto_id = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['generar'])) {

    if (!empty($_POST['ruta'])) {

        $ruta = trim($_POST['ruta']);

        // Generar ID único
        $boleto_id = uniqid("RUTA_", true);

        // Control de usos (1 solo uso)
        $control = [
            "usos" => 0,
            "max_usos" => 1,
            "ruta" => $ruta
        ];

        if (!is_dir("boletos")) {
            mkdir("boletos", 0755, true);
        }

        file_put_contents(
            "boletos/" . $boleto_id . ".json",
            json_encode($control, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );

        // ⚠️ Ahora el QR solo contiene el ID
        $qr_url = "https://quickchart.io/qr?size=300&text=" . urlencode($boleto_id);

    } else {
        $error = "Debes seleccionar una ruta.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles/alex-styles.css">
<meta charset="UTF-8">
<title>Pagar Boleto</title>
</head>
<body>

<header>
    <h2>Pago Anticipado</h2>
</header>

<div class="container">
    <div class="card">
        <h3>Comprar Boleto</h3>

        <?php if($error): ?>
            <div class="advertencia">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

<form method="POST" autocomplete="off" class="form-modern">

    <div class="form-group">
        <label>Selecciona tu ruta</label>
        <select name="ruta" required>
            <option value="" disabled selected>Elegir ruta</option>
            <option value="Ruta 1">Ruta 1</option>
            <option value="Ruta 2">Ruta 2</option>
        </select>
    </div>

    <button type="submit" class="btn-primary btn-full" name="generar">
        Comprar boleto
    </button>

</form>

        <?php if($qr_url): ?>
            <hr style="margin:30px 0;">

            <h3>Tu Código QR</h3>

            <div style="text-align:center;" id="boletoContainer">
                <img src="<?php echo htmlspecialchars($qr_url); ?>" 
                     alt="Código QR del boleto"
                     style="max-width:250px; border-radius:12px;">
                
                <p style="margin-top:15px; font-weight:600;">
                    ID: <?php echo htmlspecialchars($boleto_id); ?>
                </p>
            </div>

            <script>

            let boletoId = "<?php echo $boleto_id; ?>";

            // Revisar estado cada 2 segundos
            setInterval(() => {

                fetch("estado_boleto.php?boleto_id=" + boletoId)
                .then(res => res.text())
                .then(data => {

                    if(data === "usado"){
                        document.getElementById("boletoContainer").innerHTML = 
                        "<h1 style='color:red;text-align:center;'>BOLETO CANCELADO</h1>";
                    }

                });

            }, 2000);

            </script>

        <?php endif; ?>

    </div>
</div>

</body>
</html>