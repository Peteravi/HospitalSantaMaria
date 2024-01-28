<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Nuevo Empleado</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        // Agrega un script que cierra la ventana emergente después de enviar el formulario
        window.onload = function() {
            // Lógica para cerrar la ventana después de enviar el formulario
            function cerrarVentana() {
                if (window.opener && !window.opener.closed) {
                    window.opener.location.reload(); // Puedes recargar la página principal si es necesario
                    window.close();
                }
            }

            // Agrega un evento al formulario para cerrar la ventana después de enviar
            document.getElementById('formularioEmpleado').addEventListener('submit', function(event) {
                event.preventDefault(); // Evita el envío del formulario por defecto
                registrarEmpleado(); // Llama a la función para enviar el formulario con AJAX
                cerrarVentana(); // Cierra la ventana después de agregar el nuevo empleado
            });
        };

        // Función para enviar el formulario con AJAX (si es necesario)
        function registrarEmpleado() {
            var xhr = new XMLHttpRequest();
            var formulario = document.getElementById('formularioEmpleado');
            xhr.open('POST', formulario.action, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // Muestra la respuesta del servidor (puede ser un mensaje de éxito)
                    console.log(xhr.responseText);
                    // Puedes agregar lógica adicional si es necesario
                } else {
                    console.error('Error al registrar el empleado:', xhr.statusText);
                }
            };

            xhr.send(new URLSearchParams(new FormData(formulario)));
        }
    </script>
</head>
<body>
    <h2>Registrar Nuevo Empleado</h2>
    
    <!-- Formulario para registrar nuevo empleado -->
    <form id="formularioEmpleado" method="post" action="app.php">
        <label for="empleado_id">ID Empleado:</label>
        <input type="text" name="empleado_id" required>
        <br>

        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required>
        <br>

        <label for="apellido">Apellido:</label>
        <input type="text" name="apellido" required>
        <br>

        <label for="cargo">Cargo:</label>
        <input type="text" name="cargo" required>
        <br>

        <label for="especialidad">Especialidad:</label>
        <input type="text" name="especialidad" required>
        <br>

        <button type="submit" name="registrar_empleado">Registrar Empleado</button>
    </form>
</body>
</html>