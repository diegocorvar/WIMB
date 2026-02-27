<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Administración de Choferes</title>
    <link rel="stylesheet" href="styles/alex-styles.css">
    <link rel="stylesheet" href="./styles/normalize.css">
    <link rel="stylesheet" href="./styles/menu-styles.css">
</head>
<body>

<?php include 'user_admin.php'; ?>

<header>
  </header>

<div class="container">
    <div class="card">
        <h3>Registrar Nuevo Chofer</h3>
        <input type="text" placeholder="Nombre">
        <input type="text" placeholder="Usuario">
        <input type="text" placeholder="Placa Autobús">
        <button class="btn-primary">Guardar</button>
    </div>

    <div class="card">
        <h3>Lista de Choferes</h3>
        <table class="table">
            <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Acciones</th>
            </tr>
            <tr>
                <td>Juan Pérez</td>
                <td>juan123</td>
                <td>
                    <button class="btn-secondary">Editar</button>
                    <button class="btn-danger">Eliminar</button>
                </td>
            </tr>
        </table>
    </div>
</div>

<script>
    const botonCerrar = document.getElementById("cerrarMenu");
    const botonAbrir = document.getElementById("abrirMenu");
    const menu = document.querySelector(".nav-container");

    botonAbrir.addEventListener("click", () => {
        menu.style.display = "block";
        setTimeout(() => {
            menu.classList.add("menu-activo");
        }, 10);
        botonAbrir.classList.add("oculto");
    });

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