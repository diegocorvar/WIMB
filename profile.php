<?php ?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="styles/alex-styles.css">
<meta charset="UTF-8">
<title>Perfil</title>
</head>
<body>

<header class="header-profile">
    <h2>Mi Perfil</h2>
    <nav>
        <a href="#">Inicio</a>
        <a href="#">Pagos</a>
        <a href="#" class="logout">Cerrar sesión</a>
    </nav>
</header>

<div class="container">

    <div class="card profile-card">

        <div class="profile-top">
            <div class="avatar">
                AC
            </div>
            <h3>Alexis Cortes</h3>
            <span class="role-badge">Pasajero</span>
        </div>

        <div class="profile-info">
            <div class="info-item">
                <span>Usuario</span>
                <strong>alexis123</strong>
            </div>

            <div class="info-item">
                <span>Rol</span>
                <strong>Pasajero / Chofer / Admin</strong>
            </div>
        </div>

        <button class="btn-primary btn-full">
            Editar Perfil
        </button>

    </div>

</div>

</body>
</html>