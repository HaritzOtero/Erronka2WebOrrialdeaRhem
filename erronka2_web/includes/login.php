<?php
session_start();
require_once 'mysql.php'; // Archivo que maneja la conexión a la base de datos

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
    echo "<div align=center><h5>Logeatuta</h5></div>";
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
        // Verificar token CSRF
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            die("CSRF token inválido.");
        }

        // Prevenir inyección SQL utilizando sentencias preparadas
        $stmt = $conx->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $_POST['username']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Verificar contraseña usando password_verify()
            if (password_verify($_POST['password'], $user['password'])) {
                // Inicio de sesión exitoso
                $_SESSION['username'] = $user['izena'];
                $_SESSION['izena'] = $user['izena'];
                // Redirigir a la misma página (login exitoso)
                echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "';</script>";
                exit;
            } else {
                // Contraseña incorrecta
                $_SESSION['error_message'] = 'Errorea loginean';
            }
        } else {
            // Usuario no encontrado
            $_SESSION['error_message'] = 'Errorea loginean';
        }
    }
    ?>

    <div align=center>
        <fieldset style="width:300px;">
            <legend><b>Login</b></legend>
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=login"; ?>" method="post">
                <br>
                <label>Username/Email:</label>
                <input type="text" name="username" required pattern="[A-Za-z0-9@._-]+"><br>
                <label>Password:</label>
                <input type="password" name="password" required minlength="8"><br>
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
                <br>
                <input type="submit" name="submit" value="Login"><br>
            </form>

            <?php
            // Mostrar mensaje de error si existe
            if (isset($_SESSION['error_message'])) {
                echo "<div style='color: red;'>" . $_SESSION['error_message'] . "</div>";
                // Limpiar el mensaje de error después de mostrarlo
                unset($_SESSION['error_message']);
            }
            ?>
        </fieldset>
    </div>

    <?php
}
?>
