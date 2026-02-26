// Configuración de Supabase
const SUPABASE_URL = 'https://kdvkzyoecsqzuguohfmk.supabase.co';
const SUPABASE_KEY = 'sb_publishable_3AyvQekNP4vsK2L14JhG5Q_1mERiBDi';
const _supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);

// Inicializar mapa (Centrado en la terminal o ciudad por defecto)
const map = L.map('map').setView([20.1167, -98.7333], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Variables globales
let busMarker = null;
let trazadoTiempoReal = L.polyline([], {weight: 5}).addTo(map);
const idBusASeguir = 1; // Este ID debe coincidir con el del chofer

// Icono personalizado para el autobús
const busIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448339.png', // Puedes usar uno local
    iconSize: [40, 40],
    iconAnchor: [20, 20]
});

// 1. FUNCIÓN: Cargar la ruta oficial (la que debe seguir)
async function dibujarRutaOficial(idRuta) {
    const { data: puntos, error } = await _supabase
        .from('puntos_ruta')
        .select('lat, lng')
        .eq('id_ruta', idRuta)
        .order('orden', { ascending: true });

    if (puntos) {
        const latlngs = puntos.map(p => [p.lat, p.lng]);
        L.polyline(latlngs, {color: '#3388ff', weight: 3, dashArray: '5, 10', opacity: 0.5}).addTo(map);
    }
}

// 2. FUNCIÓN: Actualizar el bus y su rastro en el mapa
function actualizarMapa(lat, lng, velocidad) {
    const nuevaPos = [lat, lng];

    // Mover o crear el marcador del bus
    if (!busMarker) {
        busMarker = L.marker(nuevaPos, {icon: busIcon}).addTo(map);
    } else {
        busMarker.setLatLng(nuevaPos);
    }

    // Actualizar la "estela" (color según velocidad)
    let colorTrafico = 'green';
    if (velocidad <= 5) colorTrafico = 'red';
    else if (velocidad <= 15) colorTrafico = 'yellow';

    trazadoTiempoReal.addLatLng(nuevaPos);
    trazadoTiempoReal.setStyle({color: colorTrafico});

    // Centrar suavemente si el bus se sale del mapa
    if (!map.getBounds().contains(nuevaPos)) {
        map.panTo(nuevaPos);
    }

    document.getElementById('status').innerText = `En movimiento: ${Math.round(velocidad)} km/h`;
}

// 3. TIEMPO REAL: Suscribirse a la tabla de monitoreo
const canal = _supabase
    .channel('seguimiento_pasajero')
    .on('postgres_changes', 
        { event: 'INSERT', schema: 'public', table: 'monitoreo_buses', filter: `id_autobus=eq.${idBusASeguir}` }, 
        (payload) => {
            const { lat, lng, velocidad } = payload.new;
            actualizarMapa(lat, lng, velocidad);
        }
    )
    .on('postgres_changes', 
        { event: 'UPDATE', schema: 'public', table: 'monitoreo_buses', filter: `id_autobus=eq.${idBusASeguir}` }, 
        (payload) => {
            const { lat, lng, velocidad } = payload.new;
            actualizarMapa(lat, lng, velocidad);
        }
    )
    .subscribe();

// Ejecución inicial
dibujarRutaOficial(1); // Supongamos que queremos ver la ruta ID 1