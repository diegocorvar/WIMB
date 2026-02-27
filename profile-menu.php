<link rel="stylesheet" href="styles/profile-menu.css">
<link rel="stylesheet" href="styles/alex-styles.css">

<style>
    .profile-card-fixed {
    display: none; /* Se activa con JS */
    position: fixed;
    top: 12%;
    right: 5%;
    z-index: 3000;
    width: 350px;
    margin: 0; /* Quitamos el margin auto para que no se centre */
    animation: slideIn 0.3s ease-out;
}

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(50px); }
        to { opacity: 1; transform: translateX(0); }
    }

</style>

<div class="profile-menu-trigger" id="profileTrigger">
    <img src="./assets/images/icons/perfil-icon.png" alt="Perfil">
    
    <div class="dropdown-content" id="dropdownPerfil">
        <a href="javascript:void(0)" onclick="abrirCardPerfil()">Mi perfil</a>
        <a href="logout.php" class="logout">Cerrar sesión</a>
    </div>
</div>

<div class="card profile-card profile-card-fixed" id="card-perfil">
    <button class="btn-cerrar" onclick="cerrarCardPerfil()">&times;</button>
    
    <div class="profile-top">
        <div class="avatar">AC</div>
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

<script>
    const trigger = document.getElementById('profileTrigger');
    const dropdown = document.getElementById('dropdownPerfil');
    const card = document.getElementById('card-perfil');

    // Manejo del Dropdown
    trigger.addEventListener('click', (e) => {
        e.stopPropagation();
        dropdown.classList.toggle('active');
    });

    document.addEventListener('click', () => {
        dropdown.classList.remove('active');
    });

    // Manejo de la Card
    function abrirCardPerfil() {
        card.style.display = 'block';
        dropdown.classList.remove('active');
    }

    function cerrarCardPerfil() {
        card.style.display = 'none';
    }
</script>