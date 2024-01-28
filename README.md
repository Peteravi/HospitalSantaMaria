Requisitos Previos
Asegúrate de tener instalado lo siguiente:

Servidor Web (por ejemplo, Apache o Nginx): Necesitarás un servidor web configurado en tu entorno.

PHP: Asegúrate de tener PHP instalado en tu sistema. Puedes verificar la instalación ejecutando php -v en la línea de comandos.

MySQL: Necesitarás un servidor de base de datos MySQL. Asegúrate de tener las credenciales de acceso (nombre de usuario y contraseña).


Uso de la Aplicación
La aplicación te permite registrar la asistencia de empleados, generar informes semanales y registrar nuevos empleados.

Puedes utilizar los formularios proporcionados en la interfaz web para interactuar con la aplicación.

Ten en cuenta que las funciones de registro de empleados y asistencia están vinculadas, por lo que se registrarán automáticamente nuevos empleados al registrar la asistencia si el empleado no existe previamente.

Configuración de la Base de Datos:

Accede a tu servidor MySQL y crea una nueva base de datos. Puedes hacerlo desde la línea de comandos o mediante una herramienta como phpMyAdmin.

CREATE DATABASE santamariabd;

Configuración del Archivo de Conexión (app.php):

Abre el archivo app.php en un editor de texto.
Actualiza las variables de conexión con los detalles de tu base de datos (hostname, nombre de usuario, contraseña y nombre de la base de datos).
Inicia tu Servidor Web:

Inicia tu servidor web. Por ejemplo, si estás utilizando PHP incorporado, ejecuta el siguiente comando desde la carpeta del proyecto:
php -S localhost:8000
Accede a la Aplicación:

Abre un navegador web y visita http://localhost:8000 (o el puerto que estés utilizando).
Deberías ver la interfaz de la aplicación de gestión de asistencia.
