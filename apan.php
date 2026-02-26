<?php
// ESTE ARCHIVO SOLO RECIBE clvRuta
// AQUÍ DESPUÉS EL EQUIPO BACKEND CONECTARÁ MYSQL

if($_SERVER["REQUEST_METHOD"]==="POST"){
    
    $clvRuta = $_POST["clvRuta"];

    // Aquí después conectarán la base:
    /*
    INSERT INTO mis_rutas (clvRuta, fecha)
    VALUES ($clvRuta, NOW())
    */

    echo json_encode([
        "status"=>"ok",
        "clvRuta"=>$clvRuta
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
<style>
body{margin:0;font-family:sans-serif;background:#e9eef2;display:flex;justify-content:center;align-items:center;height:100vh}
.app{width:95%;max-width:1000px;height:85vh;background:white;border-radius:20px;overflow:hidden;position:relative;box-shadow:0 15px 30px rgba(0,0,0,.2)}
#map{height:100%}
.top{position:absolute;top:15px;left:50%;transform:translateX(-50%);width:80%;display:flex;z-index:1000}
.top input{flex:1;padding:10px;border:none;border-radius:20px 0 0 20px;background:#f3e1b6}
.top button{width:50px;border:none;border-radius:0 20px 20px 0;background:#23998e;color:white;cursor:pointer}
.list{position:absolute;top:60px;left:50%;transform:translateX(-50%);width:80%;background:white;border-radius:10px;padding:10px;display:none;z-index:1000}
.list div{padding:6px;cursor:pointer}
.list div:hover{background:#f3e1b6}
.menu{position:absolute;bottom:20px;right:20px;display:none;flex-direction:column;gap:10px;z-index:1000}
.menu button{width:55px;height:55px;border-radius:50%;border:none;background:#23998e;color:white;font-size:20px;cursor:pointer}
.panel{position:absolute;bottom:-350px;left:50%;transform:translateX(-50%);width:90%;max-width:450px;height:280px;background:#f3e1b6;border-radius:20px 20px 0 0;padding:10px;transition:.3s;overflow:auto;z-index:1001}
.panel.active{bottom:0}
</style>
</head>
<body>

<div class="app">

<div class="top">
<input id="busqueda" placeholder="Buscar ruta...">
<button onclick="mostrar()">🔍</button>
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

// 🔎 ESTA LISTA DESPUÉS VIENE DE TABLA rutas
// FORMATO EXACTO QUE USARÁ LA BASE
let rutas = [
{clvRuta:1, nombre:"Ruta Centro - ITESA", color:"red"},
{clvRuta:2, nombre:"Ruta Centro - Mercado", color:"blue"},
{clvRuta:3, nombre:"Ruta Centro - Hospital", color:"green"}
];

// 🔍 MOSTRAR BÚSQUEDA
function mostrar(){
lista.innerHTML="";
lista.style.display="block";

rutas.forEach(r=>{
lista.innerHTML+=`
<div onclick="seleccionar(${r.clvRuta})">
🟢 ${r.nombre}
</div>`;
});
}

// 🚍 SELECCIONAR RUTA (usa clvRuta)
function seleccionar(id){

clvRutaSeleccionada=id;

if(rutaLinea) map.removeLayer(rutaLinea);

// AQUÍ DESPUÉS EL BACKEND HARÁ:
// SELECT * FROM puntos_ruta WHERE clvRuta=id ORDER BY orden

// Simulación visual temporal:
rutaLinea=L.polyline([
[19.7120,-98.4500],
[19.7200,-98.4600]
],{weight:6}).addTo(map);

menu.style.display="flex";
lista.style.display="none";
}

// 🗺 CLICK PARA SABER DIRECCIÓN (esto sí funciona real)
map.on("click",function(e){

let lat=e.latlng.lat;
let lng=e.latlng.lng;

if(marker) map.removeLayer(marker);

marker=L.marker([lat,lng]).addTo(map);

fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
.then(r=>r.json())
.then(data=>{
let direccion=data.display_name || "Dirección no encontrada";
marker.bindPopup("📍 "+direccion).openPopup();
});

});

// 💾 GUARDAR SOLO clvRuta
function guardar(){

if(!clvRutaSeleccionada){
alert("Selecciona una ruta primero");
return;
}

let f=new FormData();
f.append("clvRuta",clvRutaSeleccionada);

fetch("",{method:"POST",body:f})
.then(r=>r.json())
.then(data=>{
alert("Ruta enviada con clave: "+data.clvRuta);
});

}

// 📋 MIS RUTAS (preparado para backend)
function misRutas(){

// DESPUÉS HARÁ:
// SELECT r.nombre, r.color
// FROM mis_rutas m
// JOIN rutas r ON r.clvRuta = m.clvRuta

contenido.innerHTML=`
<h3>Mis Rutas</h3>
<p>Aquí el backend devolverá las rutas reales usando JOIN.</p>
`;

panel.classList.add("active");
}

</script>
</body>
</html>