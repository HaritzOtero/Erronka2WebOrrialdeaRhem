<?php

if (!isset($_SESSION['admin'])) {
    echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "';</script>";
    exit;
}

if (isset($_GET['changepass'])) {

    if ($_POST['newpass'] != $_POST['confnewpass']) {
        echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=account';</script>";
        exit;
    } else {
        $oldpass = $_SESSION['password'];
        $newpass = md5($_POST['newpass']);
        $stmt = $conx->prepare("UPDATE users SET password = ? WHERE password = ?");
        $stmt->bind_param("ss", $newpass, $oldpass);
        if ($stmt->execute()) {
            session_destroy();
            echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "';</script>";
            exit;
        } else {
            echo "Error al actualizar la contraseña: " . $stmt->error;
        }
    }

} elseif (isset($_GET['adduser']) && $_SESSION['username'] == 'admin') {

    if (empty($_POST['newuser']) || empty($_POST['newuserpass'])) {
        echo "El nombre de usuario y la contraseña no pueden estar vacíos.";
        exit;
    }

    $newuser = $_POST['newuser'];
    $newuserpass = md5($_POST['newuserpass']);

    // Usa una consulta preparada para evitar SQL Injection
    $stmt = $conx->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $newuser, $newuserpass);

    if ($stmt->execute()) {
        echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=account';</script>";
        exit;
    } else {
        echo "Error al agregar el usuario: " . $stmt->error;
    }

} elseif (isset($_GET['deleteuser']) && $_SESSION['username'] == 'admin') {

    if ($_GET['deleteuser'] == $_SESSION['username']) {
        echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=account';</script>";
        exit;
    } else {
        $stmt = $conx->prepare("DELETE FROM users WHERE username = ?");
        $stmt->bind_param("s", $_GET['deleteuser']);
        if ($stmt->execute()) {
            echo "<script>window.location.href='" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=account';</script>";
            exit;
        } else {
            echo "Error al eliminar el usuario: " . $stmt->error;
        }
    }

} else {
    ?>
    <div align=center>
        <table width=1000 cellpadding=10 cellspacing=10>
            <tr>
                <td valign=top align=right>
                    <fieldset style=width:300;>
                        <legend><b>Change Password</b></legend>
                        <form action=<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=account&changepass=1"; ?> method=POST>
                            New Password: <input type=password name=newpass required><br>
                            Confirm New Password: <input type=password name=confnewpass required><br>
                            <br>
                            <div align=center><input type=submit value=Change></div>
                        </form>
                    </fieldset>
                </td>
                <?php
                if ($_SESSION['username'] == 'admin') {
                    ?>
                    <td valign=top align=left>
                        <fieldset style=width:300;>
                            <legend><b>Add User</b></legend>
                            <form action=<?php echo htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=account&adduser=1"; ?> method=POST>
                                New user's username: <input type=text name=newuser required><br>
                                New user's password: <input type=text name=newuserpass required><br>
                                <br>
                                <div align=center><input type=submit value=Add></div>
                            </form>
                        </fieldset><br>

                        <fieldset style=width:300;>
                            <legend><b>Delete User</b></legend>
                            <table cellpadding=2 cellspacing=2 width=100%>
                                <?php
                                $users = mysqli_query($conx, "SELECT username FROM users");
                                while ($user = mysqli_fetch_array($users)) {
                                    echo "<tr>";
                                    echo "<td align=left class=box>";
                                    if ($user['username'] == $_SESSION['username']) {
                                        echo "<b>" . $user['username'] . "</b>";
                                    } else {
                                        echo $user['username'];
                                    }
                                    echo "</td>";
                                    echo "<td align=right class=box width=60>";
                                    if ($user['username'] == $_SESSION['username']) {
                                        echo "<del>[delete]</del>&nbsp;";
                                    } else {
                                        echo "<a href=" . htmlspecialchars($_SERVER['PHP_SELF'], ENT_QUOTES, 'UTF-8') . "?action=account&deleteuser=" . $user['username'] . ">[delete]</a>&nbsp;";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </table>
                        </fieldset>
                    </td>
                    <?php
                }
                ?>
            </tr>
        </table>
    </div>
    <?php
}

?>

