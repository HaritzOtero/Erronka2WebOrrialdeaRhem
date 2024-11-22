<?php
// Definir las credenciales de conexión
$mysql_host = 'db';  // El nombre del servicio en Docker
$mysql_user = 'rhemAdminer';  // El usuario de la base de datos
$mysql_pass = "jS*5i9Q9Z9ox/_4lLVik'*z2KJNt<(1ZPo9Sr#ZiGJMW/1Br4yeJ%`bBzl5<'S+&";  // La contraseña del usuario (usando comillas dobles)
$mysql_db = 'rhem';  // El nombre de la base de datos

// Conectar a la base de datos
$conx = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, $mysql_db);

// Comprobar si la conexión fue exitosa
if (!$conx) {
    die("Connection failed: " . mysqli_connect_error());
}

// Seleccionar la base de datos
mysqli_select_db($conx, $mysql_db);
?>
