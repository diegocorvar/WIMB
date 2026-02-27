<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Conductor - WIMB</title>
    <link rel="stylesheet" href="styles/alex-styles.css">
</head>
<body>

<?php include 'driver_menu.php'; ?>
<?php include 'profile-menu.php'; ?>

<div class="container">
    <div class="card status-card">
        <h2>Panel de Control del Chofer</h2>
        <p>Estado: <span id="txtEstado">Desconectado</span> <span id="indicator" class="indicator"></span></p>

        <!-- Selector de ruta -->
        <div class="form-group" id="select-rutas">
            <label for="selectRuta">Seleccionar Ruta</label>
            <select id="selectRuta">
                <option value="">-- Selecciona una ruta --</option>
                <option value="1">Ruta 1</option>
                <option value="2">Ruta 2</option>
                <option value="3">Ruta 3</option>
            </select>
        </div>
        
        <button class="btn-estado-viaje" id="btnIniciar">
            <img src="./assets/images/icons/autobus.png"/>
            <span>Iniciar Monitoreo</span>
        </button>
        <button class="btn-estado-viaje" id="btnFinalizar" style="display:none;">
            <img src="./assets/images/icons/finalizar.png"/>
            <span>Terminar de compartir</span>
        </button>
    </div>
</div>

<script src="https://unpkg.com/@supabase/supabase-js@2"></script>
<script src="js/conductor.js"></script>
</body>
</html>