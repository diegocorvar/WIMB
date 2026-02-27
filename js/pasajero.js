// Configuración
const SUPABASE_URL = 'https://kdvkzyoecsqzuguohfmk.supabase.co';
const SUPABASE_KEY = 'sb_publishable_3AyvQekNP4vsK2L14JhG5Q_1mERiBDi';
const _supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);


// Inicializar mapa
const map = L.map('map').setView([20.1167, -98.7333], 14);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Variables globales
let busMarker = null;
let trazadoTiempoReal = L.polyline([], {weight: 5}).addTo(map);

const busIcon = L.icon({
    iconUrl: 'https://cdn-icons-png.flaticon.com/512/3448/3448339.png',
    iconSize: [40, 40],
    iconAnchor: [20, 20]
});
// VARIABLES GLOBALES - Asegúrate de que coincidan con el conductor
const idBusASeguir = 10; // Antes tenías 'B0001', pero el conductor usa 10

// 1. FUNCIÓN: Cargar la ruta oficial
async function dibujarRutaOficial(cve_ruta) {
    console.log("Intentando cargar ruta con clave:", cve_ruta);

    const { data: puntos, error } = await _supabase
        .from('puntos_de_ruta') // Nombre exacto de tu imagen
        .select('latitud, longitud') // Nombres exactos de tu imagen
        .eq('cve_ruta', cve_ruta)
        .order('orden', { ascending: true });

    if (error || !puntos) return console.error("Error cargando ruta:", error);

    // Convertimos datos: nota que tu tabla usa 'latitud' y 'longitud'
    const latlngs = puntos.map(p => [p.latitud, p.longitud]);
    const poly = L.polyline(latlngs, {
        color: '#fa3419', 
        weight: 3, 
        lineJoin: "round", 
        opacity: 0.8
    }).addTo(map);

    if (latlngs.length > 0) map.fitBounds(poly.getBounds());
}

// 2. FUNCIÓN: Actualizar el bus
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

// 3. TIEMPO REAL: Suscribirse a la tabla de monitoreo
// IMPORTANTE: El filtro debe usar 'cve_bus' para coincidir con el conductor
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

// Ejecución inicial: Usamos 1 o la clave de ruta que tengas en la tabla 'rutas'
dibujarRutaOficial(1);