// Configuración
const SUPABASE_URL = 'https://kdvkzyoecsqzuguohfmk.supabase.co';
const SUPABASE_KEY = 'sb_publishable_3AyvQekNP4vsK2L14JhG5Q_1mERiBDi';
const _supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);


// Inicializar mapa
const map = L.map('map').setView([20.1167, -98.7333], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Variables globales
let busMarker = null;
let userMarker = null;
let ultimaPosPasajero = null;
let markerDestino = null;
let latDestino = null;
let lngDestino = null;
let direccionDestino = null;
let rutaOficialLayer = null;
let suscripcionRealtime = null;
let cveRutaActual = null;
let trazadoTiempoReal = L.polyline([], {weight: 5}).addTo(map);

// Iconos de bus y usario
const busIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448339.png',
    iconSize: [40, 40],
    iconAnchor: [20, 20]
});

const userIcon = L.divIcon({
    className: 'user-location-icon',
    html: '<div style="background-color: #3498db; width: 15px; height: 15px; border-radius: 50%; border: 2px solid white; box-shadow: 0 0 5px rgba(0,0,0,0.5);"></div>',
    iconSize: [15, 15],
    iconAnchor: [7, 7]
});

const idBusASeguir = 0; 

seguirUbicacionPasajero();

// Cargar la ruta oficial
async function dibujarRutaOficial(cve_ruta) {

    if (rutaOficialLayer) {
        map.removeLayer(rutaOficialLayer);
    }

    if (trazadoTiempoReal) {
        trazadoTiempoReal.setLatLngs([]); 
    }

    const { data: puntos, error } = await _supabase
        .from('puntos_de_ruta') // Nombre exacto de tu imagen
        .select('latitud, longitud') // Nombres exactos de tu imagen
        .eq('cve_ruta', cve_ruta)
        .order('orden', { ascending: true });

    if (error || !puntos) return console.error("Error cargando ruta:", error);

    // Convertimos datos: nota que tu tabla usa 'latitud' y 'longitud'
    const latlngs = puntos.map(p => [p.latitud, p.longitud]);
    rutaOficialLayer = L.polyline(latlngs, {
        color: '#fa3419', 
        weight: 4, 
        opacity: 0.8,
        lineJoin: 'round'
    }).addTo(map);

    rutaOficialLayer.on('click', (e) => {
        L.DomEvent.stopPropagation(e); // Evita que el clic pase al mapa
        mostrarDetallesRuta(cve_ruta);
    });
    
    if (latlngs.length > 0) {
        map.fitBounds(rutaOficialLayer.getBounds(), { padding: [50, 50] });
    }
}


async function mostrarDetallesRuta(cve_ruta) {
    // 1. Consultar detalles usando .maybeSingle()
    const { data: rutaInfo, error } = await _supabase
        .from('rutas') 
        .select('*')
        .eq('cve_rutas', cve_ruta)
        .maybeSingle(); 

    if (error) {
        console.error("Error técnico:", error);
        return;
    }

    // 2. Si la ruta no existe en la tabla 'rutas'
    if (!rutaInfo) {
        alert("No se encontraron detalles técnicos para la Ruta " + cve_ruta);
        // Opcional: llenar con datos genéricos
        document.getElementById('det-nombre-ruta').innerText = "Ruta " + cve_ruta;
        document.getElementById('det-tarifa').innerText = "Pendiente";
        document.getElementById('panel-ruta-detalle').classList.add('active');
        return;
    }

    // 3. Si existe, llenar el panel normalmente
    cveRutaActual=cve_ruta;
    document.getElementById('det-nombre-ruta').innerText = rutaInfo.nombre;
    document.getElementById('det-tarifa').innerText = `$${rutaInfo.tarifa || '10.00'}`;
    document.getElementById('det-frecuencia').innerText = rutaInfo.frecuencia || '15 min';
    document.getElementById('det-horario').innerText = rutaInfo.horario || '06:00 - 20:00';

    const { data: busesActivos } = await _supabase
        .from('monitoreo')
        .select('cve_bus')
        .eq('cve_ruta', cve_ruta)

    const btnSeguir = document.getElementById('btn-seguir-viaje');
    if (busesActivos && busesActivos.length > 0) {
        btnSeguir.innerHTML = `Seguir Unidad (${busesActivos.length} activas)`;
        btnSeguir.classList.remove('hidden');
    } else {
        btnSeguir.classList.add('hidden'); // Ocultar si no hay nadie en vivo
    }
    console.log(`Pruebas ${busesActivos}...`);

    // 4. Mostrar panel
    document.getElementById('panel-ruta-detalle').classList.add('active');
}

function cerrarDetalles() {
    document.getElementById('panel-ruta-detalle').classList.remove('active');
}

async function activarSeguimientoReal() {
    if (!ultimaPosPasajero || !cveRutaActual) return;

    // 1. Aseguramos tipos de datos numéricos
    const parametros = {
        lat_bus: parseFloat(ultimaPosPasajero[0]),
        lng_bus: parseFloat(ultimaPosPasajero[1]),
        id_ruta: parseInt(cveRutaActual)
    };

    console.log("Enviando a RPC:", parametros);

    // 2. Llamada con nombres de parámetros exactos
    const { data: busEncontrado, error } = await _supabase
        .rpc('buscar_bus_en_ruta_cercano', parametros);

    if (error) {
        // Esto te dirá exactamente qué falta (ej: "argument p_lat does not exist")
        console.error("Detalle del error 400:", error.message);
        return;
    }

    if (busEncontrado && busEncontrado.length > 0) {
        const infoBus = busEncontrado[0];
        alert(`🚌 Bus ${infoBus.cve_bus} localizado a ${Math.round(infoBus.distancia)}m`);

    } else {
        alert("📴 No hay buses activos en esta ruta ahora mismo.");
    }

    const infoBus = busEncontrado[0];
    const distanciaKm = (infoBus.distancia / 1000).toFixed(1);

    alert(`¡Unidad encontrada! El bus ${infoBus.cve_bus} está a ${distanciaKm} km de ti.`);

    // 2. Iniciar seguimiento visual
    console.log("Iniciando suscripción para el bus ID:", infoBus.cve_bus);
    iniciarSuscripcionBus(10);
    cerrarDetalles();
}

function iniciarSuscripcionBus(idBus) {
    // Limpiar suscripción anterior si existía
    if (suscripcionRealtime) {
        _supabase.removeChannel(suscripcionRealtime);
    }

    // Resetear el rastro del bus en el mapa
    if (trazadoTiempoReal) trazadoTiempoReal.setLatLngs([]);

    // Crear nueva suscripción Realtime para el bus específico
    suscripcionRealtime = _supabase
        .channel(`seguimiento_${idBus}`)
        .on('postgres_changes', { 
            event: 'UPDATE', 
            schema: 'public', 
            table: 'monitoreo', 
            filter: `cve_bus=eq.${idBus}` 
        }, (payload) => {
            const { lat, long, velocidad } = payload.new;
            actualizarMapa(lat, long, velocidad);
            
            // Opcional: Centrar mapa en el bus si el usuario lo desea
            // map.panTo([lat, long]);
        })
        .subscribe();
}

// Actualizar el bus
function actualizarMapa(lat, long, velocidad) {
    const nuevaPos = [lat, long];
    if (!busMarker) {
        busMarker = L.marker(nuevaPos, {icon: busIcon}).addTo(map);
    } else {
        busMarker.setLatLng(nuevaPos);
    }

    let colorTrafico = (velocidad <= 5) ? 'red' : (velocidad <= 15) ? 'yellow' : 'green';
    trazadoTiempoReal.addLatLng(nuevaPos);
    trazadoTiempoReal.setStyle({color: colorTrafico});

    if (!map.getBounds().contains(nuevaPos)) map.panTo(nuevaPos);
    
    const statusLabel = document.getElementById('status');
    if (statusLabel) statusLabel.innerText = `En movimiento: ${Math.round(velocidad)} km/h`;
}

// Suscribirse a la tabla de monitoreo
const canal = _supabase
    .channel('seguimiento_pasajero')
    .on('postgres_changes', 
        { event: '*', schema: 'public', table: 'monitoreo', filter: `cve_bus=eq.${idBusASeguir}` }, 
        (payload) => {
            const { lat, long, velocidad } = payload.new;
            actualizarMapa(lat, long, velocidad);
        }
    )
    .subscribe();


// Mostrar la ubicación del pasajero
function seguirUbicacionPasajero() {
    if (!navigator.geolocation) {
        console.warn("El navegador no soporta geolocalización.");
        return;
    }

    navigator.geolocation.watchPosition(
        (pos) => {
            const { latitude, longitude } = pos.coords;
            const userPos = [latitude, longitude];
            ultimaPosPasajero = userPos;

            if (!userMarker) {
                userMarker = L.marker(userPos, { icon: userIcon }).addTo(map)
                    .bindPopup("Tu ubicación");
                map.setView(userPos, 15); 
            } else {
                userMarker.setLatLng(userPos);
            }
        },
        (err) => {
            console.error("Error obteniendo ubicación del pasajero:", err);
        },
        {
            enableHighAccuracy: true,
            maximumAge: 0,
            timeout: 5000
        }
    );
}

document.getElementById('btnCentrar').addEventListener('click', () => {
    if (ultimaPosPasajero) {
        map.flyTo(ultimaPosPasajero, 16); 
    } else {
        alert("Aún no se detecta tu ubicación. Asegúrate de tener el GPS activo.");
    }
});

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('busqueda').addEventListener('input', function(e) {
        buscar(); 
    });
});



function buscar(){
    let termino = document.getElementById('busqueda').value.trim();
    
    lista.innerHTML="";
    lista.style.display="block";
    
    // Buscar en rutas existentes
    
    
    // Buscar dirección en OpenStreetMap
    buscarDireccion(termino);
}

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

function seleccionarDireccion(direccion, lat, lng){
    direccionActual = direccion;
    latActual = lat;
    lngActual = lng;
    
    // Centrar mapa y marcador
    map.setView([lat, lng], 16);
    
    if (markerDestino) map.removeLayer(markerDestino);
    if (rutaOficialLayer) {
        map.removeLayer(rutaOficialLayer);
        rutaOficialLayer = null;
    }

    markerDestino = L.marker([lat, lng]).addTo(map);
    markerDestino.bindPopup(`📍 ${direccion}`).openPopup();
    
    buscarTransporteCercano(lat, lng, direccion);

    lista.style.display="none";
} 

map.on("click", function(e) {
    const lat = e.latlng.lat;
    const lng = e.latlng.lng;

    if (markerDestino) map.removeLayer(markerDestino);
    if (rutaOficialLayer) {
        map.removeLayer(rutaOficialLayer);
        rutaOficialLayer = null;
    }
    
    markerDestino = L.marker([lat, lng]).addTo(map);
    
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
    .then(r => r.json())
    .then(data => {
        const direccion = data.display_name || "Dirección no encontrada";
        
        if (direccion == "Dirección no encontrada") {
            lista.innerHTML="";
            lista.style.display="none";
        }

        markerDestino.bindPopup(`📍 ${direccion}`).openPopup();

        buscarTransporteCercano(lat, lng, direccion);
    })
    .catch(err => {
        console.error("Error en geocodificación:", err);
        markerDestino.bindPopup(`Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`).openPopup();
    });
});



// Variable para el radio de búsqueda (en metros)
const RADIO_BUSQUEDA = 600; 

// FUNCIÓN PRINCIPAL: Se dispara al buscar o clicar
async function buscarTransporteCercano(lat, lng, nombreLugar) {
    const latNum = parseFloat(lat);
    const lngNum = parseFloat(lng);

    if (rutaOficialLayer) {
        map.removeLayer(rutaOficialLayer);
        rutaOficialLayer = null;
    }
    if (trazadoTiempoReal) {
        trazadoTiempoReal.setLatLngs([]);
    }

    console.log(`Buscando rutas cerca de: ${latNum}, ${lngNum}`);

    const { data: rutasEncontradas, error } = await _supabase
        .rpc('buscar_rutas_cerca', { 
            target_lat: parseFloat(lat), 
            target_lng: parseFloat(lng), 
            radio_metros: RADIO_BUSQUEDA 
        });

    if (error) {
        console.error("Error consultando rutas cercanas:", error.message);
        return;
    }

    const sleep = (ms) => new Promise(resolve => setTimeout(resolve, ms));
    const listaUI = document.getElementById('lista');
    listaUI.innerHTML = `<h3>Rutas cerca de:</h3><p style="font-size:12px; color:gray;">${nombreLugar}</p>`;
    listaUI.style.display = "block";

    if (!rutasEncontradas || rutasEncontradas.length === 0) {
        listaUI.innerHTML += "<div class='no-results'>No hay rutas que pasen cerca de este punto.</div>";
        await sleep(5000);
        lista.innerHTML="";
        lista.style.display="none";
        return;
    }

    const rutasUnicas = [...new Map(rutasEncontradas.map(item => [item.cve_ruta, item])).values()];

    rutasUnicas.forEach(ruta => {
        const distancia = Math.round(ruta.distancia);
        const div = document.createElement('div');
        div.className = 'item-ruta-resultado';
        div.innerHTML = `
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <strong>Ruta ID: ${ruta.cve_ruta}</strong><br>
                    <small>Camina aprox. ${distancia} metros</small>
                </div>
                <span style="font-size:20px;">🚌</span>
            </div>
        `;
        
        div.onclick = () => {
            dibujarRutaOficial(ruta.cve_ruta);
            listaUI.style.display = "none";
        };
        
        listaUI.appendChild(div);
        
    });
}



document.addEventListener('DOMContentLoaded', function() {
    // 1. Revisar si la URL contiene "?accion=buscar"
    const urlParams = new URLSearchParams(window.location.search);
    
    if (urlParams.get('accion') === 'buscar') {
        const inputBusqueda = document.getElementById('busqueda');
        const topBar = document.querySelector('.top');

        // Asegurar que la barra sea visible (en caso de que tenga display:none)
        if (topBar) topBar.style.display = 'flex';

        // Dar foco automáticamente al buscador y hacer un pequeño scroll
        inputBusqueda.focus();
        inputBusqueda.scrollIntoView({ behavior: 'smooth' });

        // Opcional: Mostrar un mensaje o abrir la lista vacía
        console.log("Modo búsqueda activado desde el menú");
    }
});


function mostrarPerfil() {
    const panel = document.getElementById('card-perfil');
    panel.style.display = "block";
}

function cerrarPerfil() {
    const panel = document.getElementById('card-perfil');
    panel.style.display = "none";
}
