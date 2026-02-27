<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Menu</title>

    <link rel="stylesheet" href="./styles/normalize.css">
    <link rel="stylesheet" href="./styles/profile-menu.css">
</head>

<body>

<main>

    <div class="profile-menu" id="profileMenu">

        <img src="./assets/images/icons/perfil-icon.png" alt="Perfil">

        <!-- Dropdown -->
        <div class="dropdown" id="dropdownMenu">
            <a href="./profile.php">Mi perfil</a>
            <a href="#" id="logout">Cerrar sesión</a>
        </div>

    </div>

</main>

<script>
document.addEventListener("DOMContentLoaded", () => {

    const profileMenu = document.getElementById("profileMenu");
    const dropdown = document.getElementById("dropdownMenu");

    profileMenu.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.classList.toggle("active");
    });

    document.addEventListener("click", () => {
        dropdown.classList.remove("active");
    });

});
</script>

</body>
</html>