<?php

if(isset($_GET['boleto_id'])){

    $boleto_id = $_GET['boleto_id'];
    $archivo = "boletos/" . $boleto_id . ".json";

    if(!file_exists($archivo)){
        echo "invalido";
        exit;
    }

    $data = json_decode(file_get_contents($archivo), true);

    if($data['usos'] >= $data['max_usos']){
        echo "usado";
    } else {
        echo "activo";
    }

} else {
    echo "sin_id";
}
?>