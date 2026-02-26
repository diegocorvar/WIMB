<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasajero - Seguimiento en Vivo</title>
    
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map { height: 600px; width: 100%; border-radius: 15px; }
        .info-panel { padding: 10px; background: white; position: absolute; top: 10px; right: 10px; z-index: 1000; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.2); }
        .legend { font-size: 12px; }
        .dot { height: 10px; width: 10px; display: inline-block; border-radius: 50%; }
    </style>
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

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/@supabase/supabase-js@2"></script>
    <script src="js/pasajero.js"></script>
</body>
</html>