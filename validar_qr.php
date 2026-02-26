<?php

if(isset($_GET['boleto_id'])){

    $boleto_id = $_GET['boleto_id'];
    $archivo = "boletos/$boleto_id.json";

    if(!file_exists($archivo)){
        die("❌ Boleto inválido");
    }

    $data = json_decode(file_get_contents($archivo), true);

    if($data['usos'] >= $data['max_usos']){
        die("🚫 Este boleto ya no se puede usar");
    }

    $data['usos']++;

    file_put_contents($archivo, json_encode($data));

    echo "✅ Boleto válido para ".$data['ruta'];
    echo "<br>Uso ".$data['usos']." de ".$data['max_usos'];
}
?>