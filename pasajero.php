<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasajero - Seguimiento en Vivo</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="styles/pris-styles.css">
    <link rel="stylesheet" href="styles/uriel-styles.css">
</head>
<body>

    <div class="info-panel">
        <h4>Estado del Bus</h4>
        <div id="status">Esperando señal...</div>
        <div class="legend">
            <span class="dot" style="background:green"></span> Rápido 
            <span class="dot" style="background:yellow"></span> Moderado 
            <span class="dot" style="background:red"></span> Lento
        </div>
    </div>

    <?php include 'user_menu.php'; ?>

    <?php $mostrarBusqueda = (isset($_GET['accion']) && $_GET['accion'] == 'buscar'); ?>

    <div class="top" style="<?php echo $mostrarBusqueda ? 'display:flex;' : 'display:none;'; ?>">
        <input id="busqueda" placeholder="Buscar rutas...">
        <button onclick="buscar()">🔍</button>
    </div>

    <div id="resultado-busqueda">
        <span id="direccion-resultado"></span>
    </div>

    <div class="list" id="lista"></div>

    <div id="map"></div> 

    <button id="btnCentrar" class="btn-location">
        <img src="https://cdn-icons-png.flaticon.com/512/2838/2838912.png" width="25" alt="Centrar">
    </button>

    <?php       
        include 'profile-menu.php';  
    ?>

    <div id="panel-ruta-detalle" class="panel-lateral">
        <div class="header-detalle">
            <h2 id="det-nombre-ruta">Detalles de Ruta</h2>
            <button onclick="cerrarDetalles()">✖</button>
        </div>
        <div class="cuerpo-detalle">
            <div class="dato"><strong>Tarifa:</strong> <span id="det-tarifa">$10.00</span></div>
            <div class="dato"><strong>Frecuencia:</strong> <span id="det-frecuencia">Cada 10 min</span></div>
            <div class="dato"><strong>Horarios:</strong> <span id="det-horario">06:30 AM - 09:00 PM</span></div>
            
            <div class="acciones-detalle">
                <button id="btn-seguir-viaje" onclick="activarSeguimientoReal()">
                    Seguir Viaje en Vivo
                </button>
                <button class="btn-accion btn-guardar" onclick="guardarRutaFavorita()">
                    Guardar Ruta
                </button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/@supabase/supabase-js@2"></script>
    <script src="js/pasajero.js"></script>
</body>
</html>