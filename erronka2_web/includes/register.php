<?php
// assign defaults
$data = array('email' 		=> 'email',
              'firstname' 	=> 'nombre',
              'lastname' 	=> 'apellidos',
              'postcode' 	=> 'codigo postal',
              'city' 		=> 'ciudad',
              'stateProv' 	=> 'provincia',
              'country'		=> 'pais',
              'telephone' 	=> 'telefono',
              'password' 	=> 'contraseña',
              'password2' 	=> 'repetir contraseña',
              'imagen'      => ''
);
$error = array('email' 	  => '',
               'firstname' => '',
               'lastname'  => '',
               'city'	  => '',
               'stateProv' => '',
               'country'	  => '',
               'postcode'  => '',
               'telephone' => '',
               'password'  => '',
);

if (isset($_POST['data'])) {
    $data = $_POST['data'];

    // Validar la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
        
        // Comprobar tipo de archivo
        if (!in_array($file_extension, $valid_extensions)) {
            $error['imagen'] = "Formato de imagen no permitido.";
        }

        // Comprobar tamaño máximo de la imagen
        if ($_FILES['imagen']['size'] > 5000000) {  // 5MB máximo
            $error['imagen'] = "El archivo es demasiado grande.";
        }

        if (empty($error['imagen'])) {
            $path = "perfiles/" . basename($_FILES['imagen']['name']);
            move_uploaded_file($_FILES['imagen']['tmp_name'], $path);
            $data['imagen'] = basename($_FILES['imagen']['name']);
        }
    }

    // Validación de campos
    if (empty($data['email'])) {
        $error['email'] = "El email es obligatorio.";
    }

    if ($data['password'] != $data['password2']) {
        $error['password'] = "Las contraseñas no coinciden.";
    }

    if (empty($error['email']) && empty($error['password'])) {
        // Usar password_hash para almacenar contraseñas
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Sentencia preparada para evitar inyección SQL
        $sql = "INSERT INTO users (username, password, izena, abizena, hiria, lurraldea, herrialdea, postakodea, telefonoa, irudia) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conx, $sql);
        mysqli_stmt_bind_param($stmt, "ssssssssss", 
                               $data['email'], 
                               $hashed_password, 
                               $data['firstname'], 
                               $data['lastname'], 
                               $data['city'], 
                               $data['stateProv'], 
                               $data['country'], 
                               $data['postcode'], 
                               $data['telephone'], 
                               $data['imagen']);
        
        if (!mysqli_stmt_execute($stmt)) {
            die('Error: ' . mysqli_error($conx));
        } else {
            echo '<meta http-equiv="refresh" content="0;url=index.php">';
            exit;
        }
    }
}
?>

<!-- Formulario de registro -->
<div class="content">
    <br/>
    <div class="register">
        <h2>Erregistroa egin</h2>
        <br/>
        <b>Introduce la información.</b>
        <br/>
        <form action="<?php echo $_SERVER['PHP_SELF']."?action=register"; ?>" method="POST" enctype="multipart/form-data">
            <p>
                <label>Email/username: </label>
                <input type="text" name="data[email]" value="<?php echo $data['email']; ?>" />
                <?php if ($error['email']) echo '<p>', $error['email']; ?>
            </p>
            <p>
                <label>Izena: </label>
                <input type="text" name="data[firstname]" value="<?php echo $data['firstname']; ?>" />
                <?php if ($error['firstname']) echo '<p>', $error['firstname']; ?>
            </p>
            <p>
                <label>Abizena: </label>
                <input type="text" name="data[lastname]" value="<?php echo $data['lastname']; ?>" />
                <?php if ($error['lastname']) echo '<p>', $error['lastname']; ?>
            </p>
            <p>
                <label>Hiria: </label>
                <input type="text" name="data[city]" value="<?php echo $data['city']; ?>" />
                <?php if ($error['city']) echo '<p>', $error['city']; ?>
            </p>
            <p>
                <label>Lurraldea: </label>
                <input type="text" name="data[stateProv]" value="<?php echo $data['stateProv']; ?>" />
                <?php if ($error['stateProv']) echo '<p>', $error['stateProv']; ?>
            </p>
            <p>
                <label>Herrialdea: </label>
                <input type="text" name="data[country]" value="<?php echo $data['country']; ?>" />
                <?php if ($error['country']) echo '<p>', $error['country']; ?>
            </p>
            <p>
                <label>Postakodea: </label>
                <input type="text" name="data[postcode]" value="<?php echo $data['postcode']; ?>" />
                <?php if ($error['postcode']) echo '<p>', $error['postcode']; ?>
            </p>
            <p>
                <label>Telefonoa: </label>
                <input type="text" name="data[telephone]" value="<?php echo $data['telephone']; ?>" />
                <?php if ($error['telephone']) echo '<p>', $error['telephone']; ?>
            </p>
            <p>
                <label>Pasahitza: </label>
                <input type="password" name="data[password]" value="<?php echo $data['password']; ?>" />
                <?php if ($error['password']) echo '<p>', $error['password']; ?>
            </p>
            <p>
                <label>Pasahitza errepikatu: </label>
                <input type="password" name="data[password2]" value="<?php echo $data['password2']; ?>" />
            </p>
            <p>
                <label>Irudia aukeratu:</label>
                <input name="imagen" type="file" />
                <?php if ($error['imagen']) echo '<p>', $error['imagen']; ?>
            </p>
            <p>
                <input type="reset" name="data[clear]" value="Clear" class="button"/>
                <input type="submit" name="data[submit]" value="Submit" class="button marL10"/>
            </p>
        </form>
    </div>
</div>
