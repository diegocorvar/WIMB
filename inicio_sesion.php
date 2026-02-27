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

            <h1 class="titulo-inicio-sesion">Inicia Sesión</h1>
        </header>

        <!-- Contenedor del formulario -->
        <div class="contenedor-datos-inicio-sesion">

            <!-- Formulario -->
            <form id="loginForm">

                <label for="username" class="inicio-sesion">
                    Usuario:
                </label>

                <input 
                    type="text" 
                    id="username" 
                    placeholder="Nombre de usuario"
                    required
                >

                <label for="password" class="inicio-sesion">
                    Contraseña:
                </label>

                <input 
                    type="password" 
                    id="password" 
                    placeholder="Contraseña"
                    required
                >

                <button type="submit" class="inicio-sesion-btn">
                    Iniciar Sesión
                </button>

            </form>
        </div>

        <!-- Enlace a registro -->
        <p class="ir-a-registro">
            ¿No tienes una cuenta?
            <a href="./registrar_usuario.php">Regístrate</a>
        </p>

    </main>

    <!-- Supabase SDK -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {

        console.log("JS cargado correctamente");

        const SUPABASE_URL = 'https://kdvkzyoecsqzuguohfmk.supabase.co';
        const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imtkdmt6eW9lY3NxenVndW9oZm1rIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzIwODU4MzEsImV4cCI6MjA4NzY2MTgzMX0.iNSOIKGpdS_sjEh0fgaFmhQHsmwgtAzUuGE1DsU1Ck4'; // ⚠️ Usa tu anon public key real

        const { createClient } = supabase;
        const supabaseClient = createClient(SUPABASE_URL, SUPABASE_KEY);

        const form = document.getElementById("loginForm");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            console.log("Formulario interceptado");

            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!username || !password) {
                mostrarMensaje("Completa todos los campos", false);
                return;
            }

            try {

                // Encriptar contraseña SHA-256
                const hashedPassword = await crypto.subtle.digest(
                    "SHA-256",
                    new TextEncoder().encode(password)
                );

                const hashArray = Array.from(new Uint8Array(hashedPassword));
                const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

                const { data, error } = await supabaseClient
                    .from('usuarios')
                    .select("*")
                    .eq("nombre_usuario", username)
                    .eq("password", hashHex)
                    .single();

                if (error || !data) {
                    mostrarMensaje("Usuario o contraseña incorrectos", false);
                } else {

                    // Guardar sesión simple
                    localStorage.setItem("usuario", data.nombre_usuario);
                    localStorage.setItem("rol", data.rol);

                    mostrarMensaje("Inicio de sesión exitoso ✔", true);

                    setTimeout(() => {
                        window.location.href = "index.php";
                    }, 1500);
                }

            } catch (err) {
                console.error(err);
                mostrarMensaje("Error inesperado", false);
            }
        });

        function mostrarMensaje(texto, exito) {

            const mensaje = document.createElement("div");
            mensaje.textContent = texto;

            mensaje.style.position = "fixed";
            mensaje.style.top = "20px";
            mensaje.style.left = "50%";
            mensaje.style.transform = "translateX(-50%)";
            mensaje.style.padding = "15px 25px";
            mensaje.style.borderRadius = "8px";
            mensaje.style.color = "white";
            mensaje.style.fontWeight = "bold";
            mensaje.style.zIndex = "9999";
            mensaje.style.backgroundColor = exito ? "#2ecc71" : "#e74c3c";

            document.body.appendChild(mensaje);

            setTimeout(() => {
                mensaje.remove();
            }, 2000);
        }

    });
    </script>

</body>
</html>