<?php
// ESTE ARCHIVO RECIBE clvRuta O nueva_direccion
if($_SERVER["REQUEST_METHOD"]==="POST"){
    
    $clvRuta = $_POST["clvRuta"] ?? null;
    $nuevaDireccion = $_POST["nuevaDireccion"] ?? null;
    $lat = $_POST["lat"] ?? null;
    $lng = $_POST["lng"] ?? null;

    // Aquí después conectarán la base:
    /*
    IF clvRuta:
        INSERT INTO mis_rutas (clvRuta, fecha) VALUES ($clvRuta, NOW())
    ELSE:
        INSERT INTO mis_rutas (direccion, lat, lng, fecha) VALUES ($nuevaDireccion, $lat, $lng, NOW())
    */

    echo json_encode([
        "status"=>"ok",
        "clvRuta"=>$clvRuta,
        "nuevaDireccion"=>$nuevaDireccion,
        "lat"=>$lat,
        "lng"=>$lng
    ]);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>SmartBus Apan</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<link rel="stylesheet" href="./styles/uriel-styles.css">
<style>

</style>
</head>
<body>

<div class="app">

<div class="top">
<input id="busqueda" placeholder="Buscar ruta o cualquier dirección... (↵ Enter)">
<button onclick="buscar()">🔍</button>
</div>

<div id="coords-display" style="display: none;">
<strong>📍 Coordenadas:</strong><br>
<span id="lat-lng"></span>
</div>

<div id="resultado-busqueda">
<span id="direccion-resultado"></span>
<button onclick="guardarDireccion()" style="margin-left:10px;background:white;color:#4CAF50;border:none;padding:5px 10px;border-radius:3px;cursor:pointer">🚌 Guardar</button>
</div>

<div class="list" id="lista"></div>
<div id="map"></div>

<div class="menu" id="menu">
<button onclick="guardar()">🚌</button>
<button onclick="misRutas()">📋</button>
</div>

<div class="panel" id="panel">
<div style="text-align:right;cursor:pointer" onclick="panel.classList.remove('active')">✖</div>
<div id="contenido"></div>
</div>

</div>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>

let map = L.map('map',{zoomControl:false}).setView([19.7120,-98.4500],14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19}).addTo(map);

let marker=null;
let rutaLinea=null;
let clvRutaSeleccionada=null;
let direccionActual=null;
let latActual=null;
let lngActual=null;

// 🔎 LISTA DE RUTAS (después de BD)
let rutas = [
{clvRuta:1, nombre:"Ruta Centro - ITESA", color:"red"},
{clvRuta:2, nombre:"Ruta Centro - Mercado", color:"blue"},
{clvRuta:3, nombre:"Ruta Centro - Hospital", color:"green"}
];

// 🔍 ENTER PARA BUSCAR
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('busqueda').addEventListener('keypress', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            buscar();
        }
    });
});

// 🔍 BÚSQUEDA UNIVERSAL (rutas O direcciones)
function buscar(){
    let termino = document.getElementById('busqueda').value.trim();
    
    if(!termino){
        alert("Escribe algo para buscar");
        return;
    }
    
    lista.innerHTML="";
    lista.style.display="block";
    
    // 1. Buscar en rutas existentes
    let rutasEncontradas = rutas.filter(r => 
        r.nombre.toLowerCase().includes(termino.toLowerCase())
    );
    
    if(rutasEncontradas.length > 0){
        rutasEncontradas.forEach(r=>{
            lista.innerHTML+=`
            <div onclick="seleccionar(${r.clvRuta})" style="background:${r.color};color:white;">
            🚌 ${r.nombre}
            </div>`;
        });
    }
    
    // 2. Buscar dirección en OpenStreetMap
    buscarDireccion(termino);
}

// 🌐 BUSCAR DIRECCIÓN EN NOMINATIM
function buscarDireccion(query){
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=5&countrycodes=MX&addressdetails=1`)
    .then(r=>r.json())
    .then(resultados=>{
        if(resultados.length > 0){
            resultados.forEach((resultado, index)=>{
                let direccion = resultado.display_name;
                lista.innerHTML+=`
                <div onclick="seleccionarDireccion('${direccion}', ${resultado.lat}, ${resultado.lon})">
                📍 ${direccion.substring(0,60)}${direccion.length>60?'...':''}
                </div>`;
            });
        }
    });
}

// 📍 SELECCIONAR DIRECCIÓN BUSCADA
function seleccionarDireccion(direccion, lat, lng){
    direccionActual = direccion;
    latActual = lat;
    lngActual = lng;
    
    // Mostrar resultado
    document.getElementById('direccion-resultado').textContent = direccion;
    document.getElementById('resultado-busqueda').style.display = 'block';
    
    // Centrar mapa y marcador
    map.setView([lat, lng], 16);
    
    if(marker) map.removeLayer(marker);
    marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup(`📍 ${direccion}\n🌐 Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`).openPopup();
    
    lista.style.display="none";
    menu.style.display="flex";
}

// 🚍 SELECCIONAR RUTA (usa clvRuta)
function seleccionar(id){
    clvRutaSeleccionada=id;
    
    if(rutaLinea) map.removeLayer(rutaLinea);
    
    // Simulación visual temporal:
    rutaLinea=L.polyline([
    [19.7120,-98.4500],
    [19.7200,-98.4600]
    ],{weight:6, color:rutas.find(r=>r.clvRuta==id)?.color}).addTo(map);
    
    menu.style.display="flex";
    lista.style.display="none";
    document.getElementById('resultado-busqueda').style.display = 'none';
}

// 🗺 CLICK PARA MOSTRAR COORDENADAS + DIRECCIÓN
map.on("click",function(e){
    let lat = e.latlng.lat.toFixed(6);
    let lng = e.latlng.lng.toFixed(6);
    
    document.getElementById('lat-lng').textContent = `Lat: ${lat}, Lng: ${lng}`;
    document.getElementById('coords-display').style.display = 'block';
    
    if(marker) map.removeLayer(marker);
    
    marker=L.marker([lat,lng]).addTo(map);
    
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
    .then(r=>r.json())
    .then(data=>{
        let direccion=data.display_name || "Dirección no encontrada";
        marker.bindPopup(`📍 ${direccion}`).openPopup();
    });
});

// 💾 GUARDAR RUTA O DIRECCIÓN
function guardar(){
    if(clvRutaSeleccionada){
        // Guardar ruta existente
        let f=new FormData();
        f.append("clvRuta",clvRutaSeleccionada);
        fetch("",{method:"POST",body:f})
        .then(r=>r.json())
        .then(data=>alert("✅ Ruta enviada: "+data.clvRuta));
    } else if(direccionActual){
        // Guardar nueva dirección
        guardarDireccion();
    } else {
        alert("Selecciona una ruta o dirección primero");
    }
}

// 💾 GUARDAR DIRECCIÓN ESPECÍFICA
function guardarDireccion(){
    if(!direccionActual){
        alert("No hay dirección seleccionada");
        return;
    }
    
    let f=new FormData();
    f.append("nuevaDireccion", direccionActual);
    f.append("lat", latActual);
    f.append("lng", lngActual);
    
    fetch("",{method:"POST",body:f})
    .then(r=>r.json())
    .then(data=>{
        alert("✅ Dirección guardada: "+data.nuevaDireccion);
        document.getElementById('resultado-busqueda').style.display = 'none';
    });
}

// 📋 MIS RUTAS (preparado para backend)
function misRutas(){
    contenido.innerHTML=`
    <h3>Mis Rutas y Direcciones</h3>
    <p>Backend: SELECT * FROM mis_rutas ORDER BY fecha DESC</p>
    `;
    panel.classList.add("active");
}

</script>
</body>
</html>