<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Conductor - WIMB</title>
    <link rel="stylesheet" href="styles/alex-styles.css">
</head>
<body>

<div class="container">
    <div class="card status-card">
        <h2>Panel de Control del Chofer</h2>
        <p>Estado: <span id="txtEstado">Desconectado</span> <span id="indicator" class="indicator"></span></p>
        
        <hr>
        
        <button class="btn-secondary" id="btnIniciar">Iniciar Monitoreo</button>
        <button class="btn-danger" id="btnFinalizar" style="display:none;">Terminar de compartir</button>
    </div>
</div>

<script src="https://unpkg.com/@supabase/supabase-js@2"></script>
<script src="js/conductor.js"></script>
</body>
</html>