// Configuración
const SUPABASE_URL = 'https://kdvkzyoecsqzuguohfmk.supabase.co';
const SUPABASE_KEY = 'sb_publishable_3AyvQekNP4vsK2L14JhG5Q_1mERiBDi';
const _supabase = supabase.createClient(SUPABASE_URL, SUPABASE_KEY);

let watchId = null;
const cve_bus = 10; 

// Asegurémonos de que los elementos existen antes de asignar eventos
const btnIniciar = document.getElementById('btnIniciar');
const btnFinalizar = document.getElementById('btnFinalizar');

async function enviarUbicacion(lat, long, velocidad) {
    console.log("Intentando enviar:", {lat, long, velocidad});
    
    const { error } = await _supabase
        .from('monitoreo')
        .upsert({ 
            cve_bus: cve_bus, 
            lat: lat, 
            long: long, 
            velocidad: velocidad,
            last_update: new Date().toISOString(),
            estado: 'En ruta'
        }, { onConflict: 'cve_bus' });

    if (error) {
        console.error("Error en Upsert:", error.message);
        // Si sale error de "Duplicate key", verifica que cve_bus sea la Primary Key en Supabase
    }
}

btnIniciar.addEventListener('click', () => {
    console.log("Botón iniciar presionado");
    
    if (!navigator.geolocation) {
        return alert("Tu navegador no soporta GPS.");
    }

    // Cambiamos el texto inmediatamente para saber que el botón respondió
    txtEstado.innerText = "Buscando señal GPS...";

    watchId = navigator.geolocation.watchPosition(
        (pos) => {
            console.log("Posición capturada!");
            const { latitude, longitude, speed } = pos.coords;
            
            enviarUbicacion(latitude, longitude, speed || 0);
            
            txtEstado.innerText = "Transmitiendo en vivo...";
            indicator.classList.add('online');
            btnIniciar.style.display = 'none';
            btnFinalizar.style.display = 'inline-block';
        },
        (err) => {
            console.error("Error de Geolocation:", err);
            txtEstado.innerText = "Error: " + err.message;
            
            if(err.code === 1) alert("Por favor, permite el acceso al GPS.");
            if(err.code === 3) alert("Tiempo de espera agotado buscando GPS.");
        },
        { 
            enableHighAccuracy: true, 
            timeout: 10000, // 10 segundos para encontrar señal
            maximumAge: 0 
        }
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