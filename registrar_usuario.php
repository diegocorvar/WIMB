<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regístrate</title>

    <link rel="stylesheet" href="./styles/normalize.css">
    <link rel="stylesheet" href="./styles/diego-styles.css">
</head>

<body>

    <main class="contenedor-inicio-sesion">

        <header>
            <a href="./index.php" class="logo-link">
                <img 
                    class="logo-inicio-sesion" 
                    src="./assets/images/logos/logo-verde-oscuro.svg"
                    alt="Logo"
                >
            </a>

            <h1 class="titulo-inicio-sesion">Regístrate</h1>
        </header>

        <div class="contenedor-datos-inicio-sesion">

            <form id="registerForm">

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
                    Registrarse
                </button>

            </form>

        </div>

        <p class="ir-a-registro">
            ¿Ya tienes una cuenta?
            <a href="./inicio_sesion.php">Inicia sesión</a>
        </p>

    </main>

    <!-- Supabase SDK -->
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <script>
    document.addEventListener("DOMContentLoaded", () => {

        const SUPABASE_URL = 'https://kdvkzyoecsqzuguohfmk.supabase.co';
        const SUPABASE_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Imtkdmt6eW9lY3NxenVndW9oZm1rIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzIwODU4MzEsImV4cCI6MjA4NzY2MTgzMX0.iNSOIKGpdS_sjEh0fgaFmhQHsmwgtAzUuGE1DsU1Ck4'; // ← tu anon key real

        const { createClient } = supabase;
        const supabaseClient = createClient(SUPABASE_URL, SUPABASE_KEY);

        const form = document.getElementById("registerForm");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value.trim();

            if (!username || !password) {
                mostrarMensaje("Completa todos los campos", false);
                return;
            }

            try {

                // Verificar si el usuario ya existe
                const { data: usuarioExistente } = await supabaseClient
                    .from("usuarios")
                    .select("id")
                    .eq("nombre_usuario", username)
                    .single();

                if (usuarioExistente) {
                    mostrarMensaje("Ese usuario ya existe", false);
                    return;
                }

                // Encriptar contraseña SHA-256
                const hashedPassword = await crypto.subtle.digest(
                    "SHA-256",
                    new TextEncoder().encode(password)
                );

                const hashArray = Array.from(new Uint8Array(hashedPassword));
                const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');

                // Insertar nuevo usuario
                const { error } = await supabaseClient
                    .from("usuarios")
                    .insert([
                        {
                            nombre_usuario: username,
                            password: hashHex,
                            rol: 1
                        }
                    ]);

                if (error) {
                    mostrarMensaje("Error al registrar", false);
                } else {

                    mostrarMensaje("Usuario registrado correctamente ✔", true);

                    setTimeout(() => {
                        window.location.href = "inicio_sesion.php";
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

            setTimeout(() => mensaje.remove(), 2000);
        }

    });
    </script>

</body>
</html>