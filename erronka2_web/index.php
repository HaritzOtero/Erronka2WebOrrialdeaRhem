<?php
session_start();
include("conf.php");
include("includes/mysql.php");

// Generar un token CSRF si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $enpresa; ?></title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<div align="center">
<table cellspacing="5" cellpadding="5">
<tr>
<td>
<img src="logo.png" height="110" width="115" border="0">
</td>
<td>
<hr>
<h1><?php echo $enpresa; ?></h1><br>
<hr>
</td>
</tr>
</table>
<hr width="1000" size="5">

<?php
if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1)) {
    echo "<h3>Ongi Etorri " . htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
    if ($_SESSION['username'] == "admin@bdweb.com") {
        echo " | <a href=" . $_SERVER['PHP_SELF'] . "?action=account>Kontua</a> | <a href=" . $_SERVER['PHP_SELF'] . "?action=updel>Igo/Ezabatu</a> | <a href=" . $_SERVER['PHP_SELF'] . "?action=logout>Saioa itxi</a> | <a href=" . $_SERVER['PHP_SELF'] . ">Hasiera</a></h3>";
    } else {
        echo " | <a href=" . $_SERVER['PHP_SELF'] . "?action=account>Kontua</a> | <a href=" . $_SERVER['PHP_SELF'] . "?action=logout>Saioa itxi</a> | <a href=" . $_SERVER['PHP_SELF'] . ">Hasiera</a></h3>";
    }
} else {
    echo "<h3><a href=" . $_SERVER['PHP_SELF'] . "?action=register>Erregistratu</a> | <a href=" . $_SERVER['PHP_SELF'] . "?action=login>Saioa hasi</a> | <a href=" . $_SERVER['PHP_SELF'] . ">Hasiera</a></h3>";
}
?>
<form name="search" method="get" action="<?php echo $_SERVER['PHP_SELF'] . '?action=search'; ?>" id="search">
    <input type="text" value="" name="keyword" pattern="[A-Za-z0-9\s]+" title="Bakarrik letrak idatzi ahal dira"/>
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>" />
    <input type="submit" name="search" value="Bilatu"/>
</form>

<hr width="1000" size="5">
<br>

<?php
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case "updel":
            if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1) && ($_SESSION['username'] == 'admin@bdweb.com')) {
                include("includes/updel.php");
            } else {
                echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['PHP_SELF'] . '">';
                exit; 
            }
            break;
        case "register":
            include("includes/register.php");
            break;
        case "login":
            include("includes/login.php");
            break;
        case "description":
            include("includes/description.php");
            break;
        case "search":
            include("includes/main.php");
            break;
        case "account":
            if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1)) {
                include("includes/account.php");
            } else {
                echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['PHP_SELF'] . '">';
                exit;
            }
            break;
        case "logout":
            session_destroy();
            echo '<meta http-equiv="refresh" content="0;url=' . $_SERVER['PHP_SELF'] . '">';
            exit;
            break;
        default:
            include("includes/" . $_GET['action'] . ".php");
    }
} else {
    include("includes/main.php");
}
?>
</div>
<br>
<hr width="1000" size="5">
<div align="center">
    <b>Proiektua <?php echo $version; ?></b> <br>
</div>
</body>
</html>
