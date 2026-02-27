<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="styles/alex-styles.css">
    <link rel="stylesheet" href="./styles/normalize.css">
    <link rel="stylesheet" href="./styles/menu-styles.css">
</head>
<body>

    <?php include 'user_admin.php'; ?>

    <div class="container">
        <div class="card">
            <h1>Gestión de Usuarios</h1>
            <h3>Listado de Usuarios</h3>
            
            <table class="table" id="tablaUsuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Teléfono</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>

    <div id="modal" class="google-overlay">
        <div class="google-panel">
            <div class="google-header">
                <span>Editar Información</span>
                <button class="close-btn" onclick="cerrarModal()">×</button>
            </div>
            
            <div class="google-user">
                <div class="google-avatar">U</div>
                <h2>Editar Usuario</h2>
            </div>

            <form id="formEditar" class="form-modern">
                <input type="hidden" id="editId">
                
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" id="editNombre" placeholder="Ej. Juan Pérez">
                </div>

                <div class="form-group">
                    <label>Correo Electrónico</label>
                    <input type="email" id="editEmail" placeholder="correo@ejemplo.com">
                </div>

                <div class="form-group">
                    <label>Teléfono</label>
                    <input type="text" id="editTelefono" placeholder="555-0000">
                </div>

                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn-primary btn-full">Guardar Cambios</button>
                    <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Datos de ejemplo
        let usuarios = [
            { id: 1, nombre: 'Juan Pérez', email: 'juan@example.com', telefono: '555-1234' },
            { id: 2, nombre: 'María García', email: 'maria@example.com', telefono: '555-5678' },
            { id: 3, nombre: 'Carlos López', email: 'carlos@example.com', telefono: '555-9012' },
            { id: 4, nombre: 'Ana Martínez', email: 'ana@example.com', telefono: '555-3456' }
        ];

        function cargarTabla() {
            const tbody = document.querySelector('#tablaUsuarios tbody');
            tbody.innerHTML = '';
            usuarios.forEach(usuario => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${usuario.id}</td>
                    <td>${usuario.nombre}</td>
                    <td>${usuario.email}</td>
                    <td>${usuario.telefono}</td>
                    <td>
                        <button class="btn-secondary" onclick="editarUsuario(${usuario.id})">Editar</button>
                        <button class="btn-danger" onclick="eliminarUsuario(${usuario.id})">Eliminar</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        function editarUsuario(id) {
            const usuario = usuarios.find(u => u.id === id);
            document.getElementById('editId').value = usuario.id;
            document.getElementById('editNombre').value = usuario.nombre;
            document.getElementById('editEmail').value = usuario.email;
            document.getElementById('editTelefono').value = usuario.telefono;
            
            // Aplicamos la clase 'show' que definiste en tu CSS para el modal
            document.getElementById('modal').classList.add('show');
        }

        function cerrarModal() {
            document.getElementById('modal').classList.remove('show');
        }

        function eliminarUsuario(id) {
            if (confirm('¿Estás seguro de eliminar este usuario?')) {
                usuarios = usuarios.filter(u => u.id !== id);
                cargarTabla();
            }
        }

        document.getElementById('formEditar').addEventListener('submit', function(e) {
            e.preventDefault();
            const id = parseInt(document.getElementById('editId').value);
            const usuario = usuarios.find(u => u.id === id);
            if (usuario) {
                usuario.nombre = document.getElementById('editNombre').value;
                usuario.email = document.getElementById('editEmail').value;
                usuario.telefono = document.getElementById('editTelefono').value;
                cargarTabla();
                cerrarModal();
            }
        });

        // Inicializar
        cargarTabla();
    </script>
</body>
</html>