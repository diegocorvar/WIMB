<?php
if(isset($_POST['generar'])){

    $ruta = $_POST['ruta'];

    // ID único del boleto
    $boleto_id = uniqid("RUTA_");

    // Datos que llevará el QR
    $datosQR = json_encode([
        "boleto_id" => $boleto_id,
        "ruta" => $ruta
    ]);

    // Guardamos archivo de control de usos
    $control = [
        "usos" => 0,
        "max_usos" => 2,
        "ruta" => $ruta
    ];

    if(!file_exists("boletos")){
        mkdir("boletos");
    }

    file_put_contents("boletos/$boleto_id.json", json_encode($control));

    $qr_url = "https://quickchart.io/qr?size=300&text=".urlencode($datosQR);
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles/alex-styles.css">
<title>Pagar Boleto</title>
</head>
<body>

<header>
    <h2>Pago Anticipado</h2>
</header>

<div class="container">
    <div class="card">
        <h3>Comprar Boleto</h3>

        <form method="POST">
            <label>Ruta</label>
            <select name="ruta" required>
                <option value="Ruta 1">Ruta 1</option>
                <option value="Ruta 2">Ruta 2</option>
            </select>

            <br><br>

            <button class="btn-primary" name="generar">Generar Boleto</button>
        </form>

        <?php if(isset($qr_url)){ ?>
            <hr>
            <h3>Tu Código QR</h3>
            <img src="<?php echo $qr_url; ?>">
            <p>ID: <?php echo $boleto_id; ?></p>
        <?php } ?>

    </div>
</div>

</body>
</html>