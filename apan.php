<?php
session_start();

if (!isset($_SESSION['mis_rutas'])) {
    $_SESSION['mis_rutas'] = [];
}

if (isset($_POST['guardar_id'])) {
    $idRuta = $_POST['guardar_id'];
    $_SESSION['mis_rutas'][] = $idRuta;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Rutas Apan</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="uriel-styles.css"> <!-- TU CSS SEPARADO -->

</head>
<body>

<div class="barra">
    <input type="text" id="busqueda" placeholder="Buscar dirección en Apan...">
    <button class="buscar" onclick="buscarDireccion()">🔍 Buscar</button>
</div>

<div id="map"></div>

<div id="acciones">
    <button class="guardar" onclick="guardarRuta()">Guardar Ruta</button>
    <button class="mis" onclick="mostrarMisRutas()">Mis Rutas</button>
</div>

<div id="misRutasPanel" style="display:none;">
    <h3>Mis Rutas</h3>
    <div id="listaRutas">
        <?php
        foreach($_SESSION['mis_rutas'] as $ruta){
            echo "<p>ID Ruta: ".$ruta."</p>";
        }
        ?>
    </div>
</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
let map = L.map('map').setView([19.708, -98.452], 14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution:'© OpenStreetMap'
}).addTo(map);

let marcador = null;
let rutaSeleccionadaID = null;

let busIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/61/61231.png',
    iconSize: [35,35]
});

map.on('click', function(e){

    if(marcador){
        map.removeLayer(marcador);
    }

    marcador = L.marker(e.latlng, {icon: busIcon}).addTo(map);

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${e.latlng.lat}&lon=${e.latlng.lng}`)
    .then(res => res.json())
    .then(data => {

        let direccion = data.display_name || "Ubicación en Apan";
        marcador.bindPopup(direccion).openPopup();

        rutaSeleccionadaID = Math.floor(Math.random() * 1000) + 1;

        document.getElementById("acciones").style.display = "block";
    });

});

function buscarDireccion(){
    let texto = document.getElementById("busqueda").value;

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${texto} Apan Hidalgo`)
    .then(res => res.json())
    .then(data => {
        if(data.length > 0){

            let lat = data[0].lat;
            let lon = data[0].lon;

            map.setView([lat, lon], 16);

            if(marcador){
                map.removeLayer(marcador);
            }

            marcador = L.marker([lat, lon], {icon: busIcon}).addTo(map)
                .bindPopup(data[0].display_name)
                .openPopup();

            rutaSeleccionadaID = Math.floor(Math.random() * 1000) + 1;

            document.getElementById("acciones").style.display = "block";
        } else {
            alert("No se encontró la dirección");
        }
    });
}

function guardarRuta(){

    if(!rutaSeleccionadaID){
        alert("Primero selecciona una ruta");
        return;
    }

    let formData = new FormData();
    formData.append("guardar_id", rutaSeleccionadaID);

    fetch("index.php", {
        method:"POST",
        body: formData
    })
    .then(()=> {
        alert("Ruta guardada con ID: " + rutaSeleccionadaID);
    });
}

function mostrarMisRutas(){
    let panel = document.getElementById("misRutasPanel");
    panel.style.display = panel.style.display === "none" ? "block" : "none";
}
</script>

</body>
</html>