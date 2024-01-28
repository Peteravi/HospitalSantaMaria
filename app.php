<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$port = 3306;
$username = "root";
$password = "piteravi07";
$dbname = "santamariabd";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para registrar asistencia
function registrarAsistencia($empleado_id, $fecha, $hora_entrada, $hora_salida, $turno) {
    global $conn;

    // Verificar si el empleado existe
    $stmt_verificar = $conn->prepare("SELECT * FROM empleados WHERE empleado_id = ?");
    $stmt_verificar->bind_param("i", $empleado_id);
    $stmt_verificar->execute();
    $result_verificar = $stmt_verificar->get_result();

    if ($result_verificar->num_rows > 0) {
        // El empleado existe, proceder con la inserción
        $stmt = $conn->prepare("INSERT INTO registros_asistencia (empleado_id, fecha, hora_entrada, hora_salida, turno) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issss", $empleado_id, $fecha, $hora_entrada, $hora_salida, $turno);

        if ($stmt->execute()) {
            echo "Asistencia registrada exitosamente";
        } else {
            echo "Error al registrar la asistencia: " . $stmt->error;
        }

        $stmt->close();
    } else {
        // El empleado no existe, registrar al empleado y luego la asistencia
        registrarEmpleado("Nombre Default", "Apellido Default", "Cargo Default", "Especialidad Default");

        // Luego, intentar nuevamente registrar la asistencia
        registrarAsistencia($empleado_id, $fecha, $hora_entrada, $hora_salida, $turno);
    }

    $stmt_verificar->close();
}

// Función para registrar empleados
function registrarEmpleado($nombre, $apellido, $cargo, $especialidad) {
    global $conn;

    // Proceder con la inserción sin verificar si el empleado existe
    $stmt = $conn->prepare("INSERT INTO empleados (nombre, apellido, cargo, especialidad) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $apellido, $cargo, $especialidad);

    if ($stmt->execute()) {
        // No imprimir mensaje de éxito aquí
    } else {
        // Puedes agregar algún manejo de error si lo deseas
        echo "Error al registrar el empleado: " . $stmt->error;
    }

    $stmt->close();
}

// Función para obtener asistencia diaria
function obtenerAsistenciaDiaria($fecha) {
    global $conn;

    $sql = "SELECT * FROM registros_asistencia WHERE fecha = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $fecha);
    $stmt->execute();
    $result = $stmt->get_result();

    $asistencia = array();

    while ($row = $result->fetch_assoc()) {
        $asistencia[] = $row;
    }

    return $asistencia;
}

// // Procesar el formulario de registro de asistencia
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $empleado_id = $_POST['empleado_id'];
    $fecha = $_POST['fecha'];
    $hora_entrada = $_POST['hora_entrada'];
    $hora_salida = $_POST['hora_salida'];
    $turno = $_POST['turno'];

    registrarAsistencia($empleado_id, $fecha, $hora_entrada, $hora_salida, $turno);
}

// Procesar el formulario de registro de empleados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_empleado'])) {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $cargo = $_POST['cargo'];
    $especialidad = $_POST['especialidad'];
    
    // Llama a la función registrarEmpleado(), no a registrar_empleado()
    registrarEmpleado($nombre, $apellido, $cargo, $especialidad);
}

// Generar reporte semanal
if (isset($_GET['generar_reporte'])) {
    $reporte = obtenerReporteSemanal();
    echo json_encode($reporte);
}

// Función para obtener el reporte semanal
function obtenerReporteSemanal() {
    global $conn;

    // Lógica para obtener el reporte semanal, puedes personalizar según tus necesidades
    // Aquí simplemente selecciono todos los registros de asistencia de la última semana

    $fecha_actual = date("Y-m-d");
    $fecha_inicio_semana = date('Y-m-d', strtotime('-1 week', strtotime($fecha_actual)));

    $sql = "SELECT * FROM registros_asistencia WHERE fecha BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $fecha_inicio_semana, $fecha_actual);
    $stmt->execute();
    $result = $stmt->get_result();

    $reporte = array();

    while ($row = $result->fetch_assoc()) {
        $reporte[] = $row;
    }

    return $reporte;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Asistencia</title>
	<link rel="stylesheet" href="styles.css">
    <script>
        function abrirFormularioEmpleado() {
            // Abre una nueva pestaña con el formulario de registro de empleado
            var nuevaPestana = window.open('formulario_empleado.php', '_blank');
            
            // Agrega un evento onload para centrar la pestaña después de cargar el contenido
            nuevaPestana.onload = function() {
                var left = (screen.width - nuevaPestana.outerWidth) / 2;
                var top = (screen.height - nuevaPestana.outerHeight) / 2;
                nuevaPestana.moveTo(left, top);
            };
        }

        function generarReporte() {
            // Lógica para generar el reporte semanal usando AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'app.php?generar_reporte=true', true);
            
            // Define la función a ejecutar cuando la solicitud AJAX se complete
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // Muestra la respuesta del servidor (reporte en formato JSON)
                    var reporte = JSON.parse(xhr.responseText);
                    console.log(reporte);
                    // Aquí puedes agregar la lógica para mostrar o procesar el reporte
                } else {
                    console.error('Error al generar el reporte:', xhr.statusText);
                }
            };

            xhr.send();
        }
    </script>
</head>
<body>

    <h1>Gestión de Asistencia</h1>

    <!-- Formulario para registrar entrada/salida -->
    <form method="post" action="">
        <label for="empleado_id">ID Empleado:</label>
        <input type="text" name="empleado_id" required>
        <br>

        <label for="fecha">Fecha:</label>
        <input type="date" name="fecha" required>
        <br>

        <label for="hora_entrada">Hora Entrada:</label>
        <input type="time" name="hora_entrada" required>
        <br>
        
        <label for="hora_salida">Hora Salida:</label>
        <input type="time" name="hora_salida" required>
        <br>

        <label for="turno">Turno:</label>
        <input type="text" name="turno" required>
        <br>

        <button type="submit" name="registrar">Registrar Asistencia</button>
    </form>

    <!-- Lista de asistencia del día actual -->
    <div id="listaAsistencia">
        <?php
        $fecha_actual = date("Y-m-d");
        $asistencia_diaria = obtenerAsistenciaDiaria($fecha_actual);

        if (!empty($asistencia_diaria)) {
            echo "<h2>Asistencia del $fecha_actual</h2>";
            echo "<ul>";
            foreach ($asistencia_diaria as $registro) {
                echo "<li>{$registro['empleado_id']} - {$registro['hora_entrada']} - {$registro['hora_salida']} - {$registro['turno']}</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No hay registros de asistencia para el $fecha_actual.</p>";
        }
        ?>
    </div>

    <!-- Opción para generar reporte semanal -->
    <button type="button" onclick="generarReporte()">Generar Reporte Semanal</button>

   <!-- Botón para abrir el formulario de registro de empleado -->
    <button type="button" onclick="abrirFormularioEmpleado()">Registrar Nuevo Empleado</button>
    <!-- Agrega el formulario para el nuevo empleado (elimina el iframe) -->
    <div id="formularioEmpleadoContainer"></div>

    <script>
        function abrirFormularioEmpleado() {
            // Abre una nueva pestaña con el formulario de registro de empleado
            var nuevaPestana = window.open('formulario_empleado.php', '_blank');
            
            // Agrega un evento onload para centrar la pestaña después de cargar el contenido
            nuevaPestana.onload = function() {
                var left = (screen.width - nuevaPestana.outerWidth) / 2;
                var top = (screen.height - nuevaPestana.outerHeight) / 2;
                nuevaPestana.moveTo(left, top);
            };
        }

        function generarReporte() {
            // Lógica para generar el reporte semanal usando AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('GET', 'app.php?generar_reporte=true', true);
            
            // Define la función a ejecutar cuando la solicitud AJAX se complete
            xhr.onload = function() {
                if (xhr.status >= 200 && xhr.status < 400) {
                    // Muestra la respuesta del servidor (reporte en formato JSON)
                    var reporte = JSON.parse(xhr.responseText);
                    console.log(reporte);
                    // Aquí puedes agregar la lógica para mostrar o procesar el reporte
                } else {
                    console.error('Error al generar el reporte:', xhr.statusText);
                }
            };

            xhr.send();
        }
    </script>
</body>
</html>