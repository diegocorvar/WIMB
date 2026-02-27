<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="styles/alex-styles.css">
    <link rel="stylesheet" href="./styles/normalize.css">
    <link rel="stylesheet" href="./styles/menu-styles.css">
    <style>
        /* Animación extra para el texto de bienvenida */
        .welcome-text {
            animation: slideUp 0.8s ease-out;
            color: var(--color5);
            letter-spacing: -1px;
        }

        .welcome-sub {
            animation: fadeIn 1.5s ease-in;
            color: #777;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .admin-icon-float {
            font-size: 3rem;
            margin-bottom: 10px;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

    <?php include 'user_admin.php'; ?>

    <div class="container">
        <div class="card profile-card" style="margin-top: 80px;">
            <div class="profile-top">
                <div class="admin-icon-float">🛡️</div>
                <h1 class="welcome-text">¡BIENVENIDO ADMINISTRADOR!</h1>
                <p class="welcome-sub">Has ingresado al panel de control del sistema.</p>
                <br>
                <span class="role-badge">Sesión Activa: Nivel Total</span>
            </div>

            <div class="profile-info">
                <div class="info-item">
                    <span>Estado del servidor:</span>
                    <strong style="color: var(--color3);">● En línea</strong>
                </div>
                <div class="info-item">
                    <span>Fecha de acceso:</span>
                    <strong id="current-date"></strong>
                </div>
            </div>

            <div class="dashboard-grid">
                <button class="btn-primary" onclick="location.href='usuarios.php'">
                    Gestionar Usuarios
                </button>
                <button class="btn-secondary" onclick="location.href='rutas.php'">
                    Ver Rutas
                </button>
            </div>
        </div>
    </div>

    <script>
        // Mostrar fecha actual formateada
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('es-ES', options);
        
        // Efecto de confeti visual simple al entrar (opcional)
        console.log("Bienvenido al sistema, Administrador.");
    </script>
</body>
</html>