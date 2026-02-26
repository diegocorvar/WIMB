<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Iniciar Sesión</title>

    <!-- Normalización de estilos -->
    <link rel="stylesheet" href="./styles/normalize.css">

    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="./styles/diego-styles.css">
</head>

<body>

    <!-- Contenedor principal del login -->
    <main class="contenedor-inicio-sesion">

        <!-- Encabezado -->
        <header>
            <a href="./index.php" class="logo-link">
                <img 
                    class="logo-inicio-sesion" 
                    src="./assets/images/logos/logo-verde-oscuro.svg"
                    alt="Logo de la aplicación"
                >
            </a>

            <h1 class="titulo-inicio-sesion">Regístrate</h1>
        </header>

        <!-- Contenedor del formulario -->
        <div class="contenedor-datos-inicio-sesion">

            <!-- Formulario de registro -->
            <form action="" method="POST">

                <!-- Campo usuario -->
                <label for="username" class="inicio-sesion">
                    Usuario:
                </label>

                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    placeholder="Nombre de usuario"
                    required
                >

                <!-- Campo contraseña -->
                <label for="password" class="inicio-sesion">
                    Contraseña:
                </label>

                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Contraseña"
                    required
                >

                <!-- Botón -->
                <button type="submit" class="inicio-sesion-btn">
                    Registrarse
                </button>

            </form>
        </div>

        <!-- Enlace a registro -->
        <p class="ir-a-registro">
            ¿Ya tienes una cuenta?
            <a href="./inicio_sesion.php">Inicia sesión</a>
        </p>

    </main>

</body>
</html>
