<?php




?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="./styles/diego-styles.css"/>
    <link rel="stylesheet" href="./styles/normilize.css"/>
</head>
<body style="background-color: #f3e1b6">
    <main class="contenedor-inicio-sesion">
        <header>
            <a><img class="logo-inicio-sesion" src="./assets/images/logos/logo-verde-oscuro.svg"/></a>
            <h1 class="titulo-inicio-sesion">Inicia Sesión</h1>
        </header>
        <div class="contenedor-datos-inicio-sesion">
            <form>
                <label for="username" class="inicio-sesion">Usuario:</label>
                <input type="text" id="username" name="username" placeholder="Nombre de usuario"/>

                <label for="user-password" class="inicio-sesion">Contraseña:</label>
                <input type="password" id="user-password" name="user-password" placeholder="Contraseña"/>

                <button type="submit" class="inicio-sesion-btn">Iniciar Sesión</button>
            </form>
        </div>
    </main>
</body>
</html>