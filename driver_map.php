<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles/alex-styles.css">
<title>Mapa Ruta</title>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
#map {
    height: 400px;
    border-radius: 10px;
}
</style>

</head>
<body>

<header>
    <h2>Ruta Activa</h2>
</header>

<div class="container">
    <div class="card">
        <h3>Mapa en Tiempo Real</h3>

        <div id="map"></div>

        <br>

        <button class="btn-secondary" id="btnIniciar">Iniciar Viaje</button>
        <button class="btn-danger" id="btnFinalizar">Finalizar Viaje</button>
    </div>
</div>

<script>

let viajeActivo = false;
let watchId = null;
let autobus_id = 1; // Luego puede venir de sesión

// Crear mapa centrado en Tulancingo
const map = L.map('map').setView([20.1167, -98.7333], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
.addTo(map);

const marcador = L.marker([20.1167, -98.7333]).addTo(map);
const rutaLinea = L.polyline([], {color:'green'}).addTo(map);

let puntos = [];

// =============================
// INICIAR VIAJE
// =============================
document.getElementById("btnIniciar").addEventListener("click", () => {

    if(viajeActivo) return;

    viajeActivo = true;
    alert("Viaje iniciado 🚍");

    if (navigator.geolocation) {

        watchId = navigator.geolocation.watchPosition(

            pos => {

                let lat = pos.coords.latitude;
                let lng = pos.coords.longitude;
                let velocidad = pos.coords.speed ?? 0;

                // Mover marcador suavemente
                marcador.setLatLng([lat,lng]);
                map.panTo([lat,lng]);

                // Dibujar ruta
                puntos.push([lat,lng]);
                rutaLinea.setLatLngs(puntos);

                // Cambiar color según velocidad
                if(velocidad > 15){
                    rutaLinea.setStyle({color:'green'});
                } else if(velocidad > 5){
                    rutaLinea.setStyle({color:'yellow'});
                } else {
                    rutaLinea.setStyle({color:'red'});
                }

                // Enviar al backend
                fetch("api/guardarUbicacion.php",{
                    method:"POST",
                    headers:{"Content-Type":"application/json"},
                    body:JSON.stringify({
                        autobus_id: autobus_id,
                        lat: lat,
                        lng: lng,
                        velocidad: velocidad
                    })
                }).catch(err => console.log("Error enviando datos:", err));

            },

            error => {
                alert("No se pudo obtener la ubicación. Activa el GPS.");
                console.log(error);
            },

            {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 5000
            }
        );

    } else {
        alert("Tu navegador no soporta geolocalización.");
    }
});

// =============================
// FINALIZAR VIAJE
// =============================
document.getElementById("btnFinalizar").addEventListener("click", () => {

    if(!viajeActivo) return;

    viajeActivo = false;
    alert("Viaje finalizado");

    if(watchId !== null){
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
    }

});

</script>

</body>
</html>