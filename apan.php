<?php
$file="rutas.json";

if($_SERVER["REQUEST_METHOD"]==="POST"){
    $rutas=file_exists($file)?json_decode(file_get_contents($file),true):[];
    $rutas[]=[
        "ruta"=>$_POST["ruta"],
        "destino"=>$_POST["destino"],
        "lat"=>$_POST["lat"],
        "lng"=>$_POST["lng"],
        "fecha"=>date("Y-m-d H:i")
    ];
    file_put_contents($file,json_encode($rutas,JSON_PRETTY_PRINT));
    echo json_encode(["ok"=>true]); exit;
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
let map = L.map('map', {
    zoomControl: false,
    preferCanvas: true
}).setView([19.7120,-98.4500],14);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{
    maxZoom:19
}).addTo(map);

let rutas = [
["Centro → ITESA",19.7063,-98.4525,"ITESA Apan"],
["Centro → Mercado",19.7132,-98.4481,"Mercado Apan"],
["Centro → Hospital",19.7180,-98.4540,"Hospital Apan"],
["Centro → Presidencia",19.7115,-98.4490,"Presidencia Apan"],
["Centro → Deportiva",19.7150,-98.4600,"Unidad Deportiva"]
];

let marker = null;
let rutaLinea = null;
let lat,lng,nombre,destino;

const busIcon = L.icon({
    iconUrl:'https://cdn-icons-png.flaticon.com/512/61/61231.png',
    iconSize:[38,38],
    iconAnchor:[19,38]
});

function mostrar(){
    let box = lista;
    box.innerHTML="";
    box.style.display="block";

    rutas.forEach((r,i)=>{
        box.innerHTML += `
        <div onclick="seleccionar(${i})">
        🟢 ${r[0]} <b>(RECOMENDADA)</b>
        </div>`;
    });
}

function seleccionar(i){
    let r = rutas[i];
    lat = r[1];
    lng = r[2];
    nombre = r[0];
    destino = r[3];

    ponerMarker();
    dibujarRuta([19.7120,-98.4500],[lat,lng]);
    lista.style.display="none";
}

function ponerMarker(){
    if(marker) map.removeLayer(marker);

    marker = L.marker([lat,lng],{icon:busIcon})
    .addTo(map)
    .bindPopup("🚌 "+destino)
    .openPopup();

    map.flyTo([lat,lng],16,{duration:0.8});
    menu.style.display="flex";
}

function dibujarRuta(origen,destinoCoords){
    if(rutaLinea) map.removeLayer(rutaLinea);

    rutaLinea = L.polyline([origen,destinoCoords],{
        weight:5,
        opacity:0.7
    }).addTo(map);
}

map.on("click", e=>{
    lat = e.latlng.lat;
    lng = e.latlng.lng;
    nombre = "Ruta personalizada";

    destino = "Obteniendo dirección...";
    ponerMarker();

    // Cancelar petición anterior si existe
    if(window.reverseController){
        window.reverseController.abort();
    }

    window.reverseController = new AbortController();

    setTimeout(()=>{
        window.reverseController.abort();
    },5000); // timeout 5 segundos

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`,{
        headers:{
            "Accept":"application/json"
        },
        signal: window.reverseController.signal
    })
    .then(r=>{
        if(!r.ok) throw new Error("Error API");
        return r.json();
    })
    .then(d=>{
        if(d && d.display_name){
            destino = d.display_name;
        }else{
            destino = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
        }
        marker.setPopupContent("🚌 "+destino).openPopup();
    })
    .catch(()=>{
        destino = `Lat: ${lat.toFixed(5)}, Lng: ${lng.toFixed(5)}`;
        marker.setPopupContent("🚌 "+destino).openPopup();
    });
});

function guardar(){
    if(!lat || !lng){
        alert("Selecciona una ruta primero");
        return;
    }

    let f = new FormData();
    f.append("ruta",nombre);
    f.append("destino",destino);
    f.append("lat",lat);
    f.append("lng",lng);

    fetch("",{method:"POST",body:f})
    .then(r=>r.json())
    .then(()=>{
        alert("Ruta guardada 🚍");
    })
    .catch(()=>{
        alert("Error al guardar");
    });
}

function misRutas(){
    fetch("rutas.json")
    .then(r=>r.json())
    .then(data=>{
        contenido.innerHTML="<h3>Mis Rutas</h3>";

        if(!data || data.length===0){
            contenido.innerHTML+="<p>No hay rutas guardadas</p>";
        }else{
            data.slice().reverse().forEach(r=>{
                contenido.innerHTML+=`
                <div style="background:white;margin:6px 0;padding:6px;border-radius:8px">
                🚌 <b>${r.ruta}</b><br>
                📍 ${r.destino}<br>
                📅 ${r.fecha}
                </div>`;
            });
        }

        panel.classList.add("active");
    })
    .catch(()=>{
        contenido.innerHTML="<p>Error cargando rutas</p>";
        panel.classList.add("active");
    });
}
</script>
</body>
</html>