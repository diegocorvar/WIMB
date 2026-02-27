<?php
$qr_url = null;
$boleto_id = null;
$error = null;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['generar'])) {

    if (!empty($_POST['ruta'])) {

        $ruta = trim($_POST['ruta']);

        // Generar ID más seguro
        $boleto_id = uniqid("RUTA_", true);

        // Datos del QR
        $datosQR = json_encode([
            "boleto_id" => $boleto_id,
            "ruta" => $ruta
        ], JSON_UNESCAPED_UNICODE);

        // Control de usos
        $control = [
            "usos" => 0,
            "max_usos" => 2,
            "ruta" => $ruta
        ];

        // Crear carpeta si no existe
        if (!is_dir("boletos")) {
            if (!mkdir("boletos", 0755, true)) {
                $error = "No se pudo crear el directorio de boletos.";
            }
        }

        // Guardar archivo JSON
        if (!$error) {
            file_put_contents(
                "boletos/" . $boleto_id . ".json",
                json_encode($control, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            );

            $qr_url = "https://quickchart.io/qr?size=300&text=" . urlencode($datosQR);
        }

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

            <div style="text-align:center;">
                <img src="<?php echo htmlspecialchars($qr_url); ?>" 
                     alt="Código QR del boleto"
                     style="max-width:250px; border-radius:12px;">
                
                <p style="margin-top:15px; font-weight:600;">
                    ID: <?php echo htmlspecialchars($boleto_id); ?>
                </p>
            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>