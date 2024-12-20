<?php
// assign defaults
$data = array('email'       => '',
              'firstname'   => '',
              'lastname'    => '',
              'postcode'    => '',
              'city'        => '',
              'stateProv'   => '',
              'country'     => '',
              'telephone'   => '',
              'password'    => '',
              'password2'   => '',
              'imagen'      => ''
);
$error = array('email'     => '',
               'firstname' => '',
               'lastname'  => '',
               'city'      => '',
               'stateProv' => '',
               'country'   => '',
               'postcode'  => '',
               'telephone' => '',
               'password'  => '',
               'imagen'    => ''
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = filter_input_array(INPUT_POST, [
        'data' => [
            'filter' => FILTER_SANITIZE_STRING,
            'flags'  => FILTER_REQUIRE_ARRAY
        ]
    ])['data'];

    // Validar la imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == 0) {
        $valid_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));

        // Comprobar tipo de archivo
        if (!in_array($file_extension, $valid_extensions)) {
            $error['imagen'] = "Formato de imagen no permitido.";
        }

        // Comprobar tamaño máximo de la imagen
        if ($_FILES['imagen']['size'] > 5000000) {  // 5MB máximo
            $error['imagen'] = "El archivo es demasiado grande.";
        }

        if (empty($error['imagen'])) {
            $path = "perfiles/" . uniqid() . '.' . $file_extension; // Nombre único
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $path)) {
                $error['imagen'] = "Error al subir la imagen.";
            } else {
                $data['imagen'] = $path;
            }
        }
    }

    // Validación de campos
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $error['email'] = "Emailaren formatua ez da egokia.";
    }

    if ($data['password'] !== $data['password2']) {
        $error['password'] = "Pasahitzak ez dira berdinak.";
    }

    if (strlen($data['password']) < 8) {
        $error['password'] = "Pasahitza 8 karaktere edo gehiago izan behar ditu";
    }

    // Comprobar si el email ya existe en la base de datos
    if (empty($error['email'])) {
        $sql_check = "SELECT COUNT(*) FROM users WHERE username = ?";
        $stmt_check = $conx->prepare($sql_check);
        $stmt_check->bind_param("s", $data['email']);
        $stmt_check->execute();
        $stmt_check->bind_result($user_count);
        $stmt_check->fetch();
        $stmt_check->close();

        if ($user_count > 0) {
            $error['email'] = "Erabiltzailea jadanik existitzen da.";
        }
    }

    if (empty($error['email']) && empty($error['password']) && empty($error['imagen'])) {
        // Usar password_hash para almacenar contraseñas
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        // Sentencia preparada para evitar inyección SQL
        $sql = "INSERT INTO users (username, password, izena, abizena, hiria, lurraldea, herrialdea, postakodea, telefonoa, irudia) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conx->prepare($sql);
        $stmt->bind_param("ssssssssss", 
                          $data['email'], 
                          $hashed_password, 
                          htmlspecialchars($data['firstname'], ENT_QUOTES, 'UTF-8'), 
                          htmlspecialchars($data['lastname'], ENT_QUOTES, 'UTF-8'), 
                          htmlspecialchars($data['city'], ENT_QUOTES, 'UTF-8'), 
                          htmlspecialchars($data['stateProv'], ENT_QUOTES, 'UTF-8'), 
                          htmlspecialchars($data['country'], ENT_QUOTES, 'UTF-8'), 
                          htmlspecialchars($data['postcode'], ENT_QUOTES, 'UTF-8'), 
                          htmlspecialchars($data['telephone'], ENT_QUOTES, 'UTF-8'), 
                          $data['imagen']);

        if (!$stmt->execute()) {
            die('Error: ' . $stmt->error);
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

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . "?action=register"; ?>" method="POST" enctype="multipart/form-data">
            <p>
                <label>Email/username: </label>
                <input type="email" name="data[email]" value="<?php echo htmlspecialchars($data['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required />
                <?php if ($error['email']) echo '<p>' . htmlspecialchars($error['email'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Izena: </label>
                <input type="text" name="data[firstname]" value="<?php echo htmlspecialchars($data['firstname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="[A-Za-zÀ-ÿ\s]+" title=" Bakarrik letrak jarri ahal dira" />
                <?php if ($error['firstname']) echo '<p>' . htmlspecialchars($error['firstname'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Abizena: </label>
                <input type="text" name="data[lastname]" value="<?php echo htmlspecialchars($data['lastname'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="[A-Za-zÀ-ÿ\s]+" title=" Bakarrik letrak jarri ahal dira" />
                <?php if ($error['lastname']) echo '<p>' . htmlspecialchars($error['lastname'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Hiria: </label>
                <input type="text" name="data[city]" value="<?php echo htmlspecialchars($data['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="[A-Za-zÀ-ÿ\s]+" title=" Bakarrik letrak jarri ahal dira" />
                <?php if ($error['city']) echo '<p>' . htmlspecialchars($error['city'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Lurraldea: </label>
                <input type="text" name="data[stateProv]" value="<?php echo htmlspecialchars($data['stateProv'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="[A-Za-zÀ-ÿ\s]+" title=" Bakarrik letrak jarri ahal dira" />
                <?php if ($error['stateProv']) echo '<p>' . htmlspecialchars($error['stateProv'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Herrialdea: </label>
                <input type="text" name="data[country]" value="<?php echo htmlspecialchars($data['country'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="[A-Za-zÀ-ÿ\s]+" title=" Bakarrik letrak jarri ahal dira" />
                <?php if ($error['country']) echo '<p>' . htmlspecialchars($error['country'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Postakodea: </label>
                <input type="text" name="data[postcode]" value="<?php echo htmlspecialchars($data['postcode'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="\d{4,10}" title=" 4-10 digito bitartean eduki behar ditu" />
                <?php if ($error['postcode']) echo '<p>' . htmlspecialchars($error['postcode'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Telefonoa: </label>
                <input type="text" name="data[telephone]" value="<?php echo htmlspecialchars($data['telephone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required pattern="\+?\d{7,15}" title=" 7-15 digitoko zenbakia izan behar du eta nazioarteko prefijoa izan ahal du." />
                <?php if ($error['telephone']) echo '<p>' . htmlspecialchars($error['telephone'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Pasahitza: </label>
                <input type="password" name="data[password]" value="" required minlength="8" title=" Gutxienez 8 karaktere izan behar ditu" />
                <?php if ($error['password']) echo '<p>' . htmlspecialchars($error['password'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <label>Pasahitza errepikatu: </label>
                <input type="password" name="data[password2]" value="" required minlength="8" title=" Gutxienez 8 karaktere izan behar ditu" />
            </p>
            <p>
                <label>Irudia aukeratu:</label>
                <input name="imagen" type="file" accept="image/jpeg, image/png, image/gif" required/>
                <?php if ($error['imagen']) echo '<p>' . htmlspecialchars($error['imagen'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <p>
                <input type="reset" name="data[clear]" value="Clear" class="button" />
                <input type="submit" name="data[submit]" value="Submit" class="button marL10" />
            </p>
        </form>
    </div>
</div>

