<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú</title>

    <!-- Normalización de estilos -->
    <link rel="stylesheet" href="./styles/normalize.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="./styles/menu-styles.css">
</head>

<body>

    <!-- =============================
         MENÚ LATERAL / SUPERIOR
    ============================== -->
    <header class="nav-container">

        <!-- Barra superior del menú -->
        <div class="top-nav">
            <a href="./pasajero.php">
                <img 
                    class="logo-menu" 
                    src="./assets/images/logos/logo-blanco.svg" 
                    alt="Logo principal">
            </a>

            <img 
                id="cerrarMenu" 
                class="cerrar-btn" 
                src="./assets/images/icons/cruz.png" 
                alt="Cerrar menú">
        </div>

        <hr>

        <!-- Opciones de navegación -->
        <nav>
            <ul class="menu-options">
                <li><a href="./admin_crud.php" class="menu-option">Choferes</a></li>
                <li><a href="./usuarios.php" class="menu-option">Usuarios</a></li>
                <li><a href="./rutas.php" class="menu-option">Rutas</a></li>
            </ul>
        </nav>

    </header>

    <!-- Botón para abrir menú -->
    <div id="abrirMenu" class="menu-btn">
        <img 
            class="abrir-menu-icon" 
            src="./assets/images/icons/menu.png" 
            alt="Abrir menú">
    </div>

    <main>
        <!-- Contenido principal -->
    </main>

    <!-- =============================
         SCRIPT DEL MENÚ
    ============================== -->
    <script>
        const botonCerrar = document.getElementById("cerrarMenu");
        const botonAbrir = document.getElementById("abrirMenu");
        const menu = document.querySelector(".nav-container");

        // Abrir menú
        botonAbrir.addEventListener("click", () => {
            menu.style.display = "block";

            setTimeout(() => {
                menu.classList.add("menu-activo");
            }, 10);

            botonAbrir.classList.add("oculto");
        });

        // Cerrar menú
        botonCerrar.addEventListener("click", () => {
            menu.classList.remove("menu-activo");

            setTimeout(() => {
                menu.style.display = "none";
            }, 300);

            botonAbrir.classList.remove("oculto");
        });
    </script>

</body>
</html>