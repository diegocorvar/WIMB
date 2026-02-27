// Configuración
const SUPABASE_URL = 'https://kdvkzyoecsqzuguohfmk.supabase.co';
const SUPABASE_KEY = 'sb_publishable_3AyvQekNP4vsK2L14JhG5Q_1mERiBDi';
const _supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);

let watchId = null;
const cve_bus = 10; 

const btnIniciar = document.getElementById('btnIniciar');
const btnFinalizar = document.getElementById('btnFinalizar');
const txtEstado = document.getElementById('txtEstado');
const indicator = document.getElementById('indicator');

async function enviarUbicacion(lat, long, velocidad) {
    const { error } = await _supabase
        .from('monitoreo')
        .upsert({ 
            cve_bus: cve_bus, // Esta es la "Primary Key" o columna única para el UPSERT
            lat: lat, 
            long: long, 
            velocidad: velocidad,
            last_update: new Date().toISOString(),
            estado: 'En ruta'
        }, { onConflict: 'cve_bus' }); // Esto asegura que solo se modifique un registro

    if (error) console.error("Error enviando datos:", error.message);
}

btnIniciar.addEventListener('click', () => {
    if (!navigator.geolocation) return alert("GPS no soportado");

    watchId = navigator.geolocation.watchPosition(
        (pos) => {
            const { latitude, longitude, speed } = pos.coords;
            enviarUbicacion(latitude, longitude, speed || 0);
            
            // Feedback visual para el chofer
            txtEstado.innerText = "Transmitiendo en vivo...";
            indicator.classList.add('online');
            btnIniciar.style.display = 'none';
            btnFinalizar.style.display = 'inline-block';
        },
        (err) => console.error(err),
        { enableHighAccuracy: true }
    );
});

btnFinalizar.addEventListener('click', async () => {
    if (watchId) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        
        // Opcional: Marcar como desconectado en la DB
        await _supabase.from('monitoreo').update({ estado: 'Fuera de servicio' }).eq('cve_bus', cve_bus);

        txtEstado.innerText = "Desconectado";
        indicator.classList.remove('online');
        btnIniciar.style.display = 'inline-block';
        btnFinalizar.style.display = 'none';
        alert("Monitoreo finalizado.");
    }
});