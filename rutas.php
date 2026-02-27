<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Rutas</title>
    <link rel="stylesheet" href="styles/alex-styles.css">
    <link rel="stylesheet" href="./styles/normalize.css">
    <link rel="stylesheet" href="./styles/menu-styles.css">
</head>
<body>

    <?php include 'user_admin.php'; ?>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <button class="btn-primary" onclick="abrirModalCrear()">+ Nueva Ruta</button>
        </div>

        <div class="card">
                <h1>Gestión de Usuarios</h1>
            <table class="table" id="tablaRutas">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>Precio</th>
                        <th>Horario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>

    <div id="modalRuta" class="google-overlay">
        <div class="google-panel">
            <div class="google-header">
                <span id="modalTitulo">Nueva Ruta</span>
                <button class="close-btn" onclick="cerrarModal()">×</button>
            </div>
            
            <div class="google-user">
                <div class="google-avatar" style="background: var(--color3);">R</div>
                <h2 id="modalSubtitulo">Configuración de Ruta</h2>
            </div>

            <form id="formRuta" class="form-modern">
                <input type="hidden" id="rutaId">
                
                <div class="form-group">
                    <label>Punto de Origen</label>
                    <input type="text" id="origen" placeholder="Ej. Terminal Norte" required>
                </div>

                <div class="form-group">
                    <label>Punto de Destino</label>
                    <input type="text" id="destino" placeholder="Ej. Centro Histórico" required>
                </div>

                <div style="display: flex; gap: 15px;">
                    <div class="form-group" style="flex: 1;">
                        <label>Precio ($)</label>
                        <input type="number" id="precio" placeholder="0.00" step="0.01" required>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label>Horario</label>
                        <input type="time" id="horario" required>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 10px;">
                    <button type="submit" class="btn-primary btn-full">Guardar Ruta</button>
                    <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Datos de ejemplo para Rutas
        let rutas = [
            { id: 101, origen: 'CDMX', destino: 'Puebla', precio: 250.00, horario: '08:00' },
            { id: 102, origen: 'Monterrey', destino: 'Saltillo', precio: 120.00, horario: '10:30' },
            { id: 103, origen: 'Guadalajara', destino: 'Zapopan', precio: 35.50, horario: '14:00' }
        ];

        const tablaBody = document.querySelector('#tablaRutas tbody');
        const modal = document.getElementById('modalRuta');
        const formRuta = document.getElementById('formRuta');

        function cargarRutas() {
            tablaBody.innerHTML = '';
            rutas.forEach(ruta => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>#${ruta.id}</strong></td>
                    <td>${ruta.origen}</td>
                    <td>${ruta.destino}</td>
                    <td>$${ruta.precio.toFixed(2)}</td>
                    <td>${ruta.horario} hrs</td>
                    <td>
                        <button class="btn-secondary" onclick="prepararEdicion(${ruta.id})">Editar</button>
                        <button class="btn-danger" onclick="eliminarRuta(${ruta.id})">Eliminar</button>
                    </td>
                `;
                tablaBody.appendChild(tr);
            });
        }

        // Abrir modal para crear
        function abrirModalCrear() {
            formRuta.reset();
            document.getElementById('rutaId').value = '';
            document.getElementById('modalTitulo').innerText = 'Nueva Ruta';
            modal.classList.add('show');
        }

        // Preparar modal para editar
        function prepararEdicion(id) {
            const ruta = rutas.find(r => r.id === id);
            if (!ruta) return;

            document.getElementById('rutaId').value = ruta.id;
            document.getElementById('origen').value = ruta.origen;
            document.getElementById('destino').value = ruta.destino;
            document.getElementById('precio').value = ruta.precio;
            document.getElementById('horario').value = ruta.horario;

            document.getElementById('modalTitulo').innerText = 'Editar Ruta';
            modal.classList.add('show');
        }

        function cerrarModal() {
            modal.classList.remove('show');
        }

        function eliminarRuta(id) {
            if (confirm('¿Deseas eliminar esta ruta permanentemente?')) {
                rutas = rutas.filter(r => r.id !== id);
                cargarRutas();
            }
        }

        // Manejar el envío del formulario (Crear y Editar)
        formRuta.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('rutaId').value;
            const nuevaRuta = {
                origen: document.getElementById('origen').value,
                destino: document.getElementById('destino').value,
                precio: parseFloat(document.getElementById('precio').value),
                horario: document.getElementById('horario').value
            };

            if (id) {
                // Editar existente
                const index = rutas.findIndex(r => r.id == id);
                rutas[index] = { id: parseInt(id), ...nuevaRuta };
            } else {
                // Crear nuevo
                const nuevoId = rutas.length > 0 ? Math.max(...rutas.map(r => r.id)) + 1 : 101;
                rutas.push({ id: nuevoId, ...nuevaRuta });
            }

            cargarRutas();
            cerrarModal();
        });

        // Inicializar
        cargarRutas();
    </script>
</body>
</html>