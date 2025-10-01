<?php
session_start();

/**
 * Si no hay sesión activa, enviar al login
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/**
 * Conexión BD
 */
function conectarBD() {
    $conn = new mysqli("localhost", "root", "", "usuario_php");
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }
    return $conn;
}

/**
 * Cerrar sesión
 */
if (isset($_GET['logout'])) {
    $conn = conectarBD();
    $user_id = $_SESSION['user_id'];
    $update = $conn->prepare("UPDATE login_user SET last_logout = NOW() WHERE id=?");
    $update->bind_param("i", $user_id);
    $update->execute();

    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

/**
 * Consultar usuarios
 */
function consultarBD() {
    $conn = conectarBD();
    $sql = "SELECT id, username, password, created_at, updated_at, last_login, last_logout, session_id, login_count 
            FROM login_user";
    $result = $conn->query($sql);
    return $result;
}

$result = consultarBD();

$editar_usuario = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_id'])) {
    $conn = conectarBD();
    $id = intval($_POST['editar_id']);
    $sql = "SELECT * FROM login_user WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editar_usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Usuarios</title>
    <link rel="stylesheet" href="estilo_nuevo.css">
</head>
<body>

<!-- Botón de cerrar sesión -->
<p>
    Usuario conectado: <b><?php echo $_SESSION['username']; ?></b> |
    <a href="?logout=true" onclick="return confirm('¿Seguro que quieres cerrar sesión?');">Cerrar Sesión</a>
</p>

<h2><?php echo $editar_usuario ? "Editar Usuario" : "Formulario de Registro"; ?></h2>
<form action="conexionBD_leer_registrar_eliminar_editar_css.php" method="post">
    <?php if ($editar_usuario): ?>
        <input type="hidden" name="id" value="<?php echo $editar_usuario['id']; ?>">
    <?php endif; ?>
    <label for="user">Usuario</label><br>
    <input type="text" name="user" placeholder="Usuario" value="<?php echo $editar_usuario ? htmlspecialchars($editar_usuario['username']) : ''; ?>"><br><br>
    <label for="password">Contraseña</label><br>
    <input type="password" name="password" placeholder="Contraseña" value=""><br><br>
    <input type="submit" name="<?php echo $editar_usuario ? 'actualizar' : 'registrar'; ?>" value="<?php echo $editar_usuario ? 'Actualizar' : 'Registrar'; ?>">
    <?php if ($editar_usuario): ?>
        <a href="conexionBD_leer_registrar_eliminar_editar_css.php">Cancelar</a>
    <?php endif; ?>
</form>

<?php
$conn = conectarBD();

// -------- Registrar usuario --------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar'])) {
    $user = $_POST["user"];
    $password = $_POST["password"];

    $sql_check = "SELECT id FROM login_user WHERE username=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $user);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<h3 style='color:red;'>El nombre de usuario ya existe.</h3>";
    } else {
        $sql = "INSERT INTO login_user (username, password, created_at, updated_at, login_count) VALUES (?, ?, NOW(), NOW(), 0)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user, $password);
        if ($stmt->execute()) {
            echo "<h3 style='color:green;'>Usuario registrado.</h3>";
        }
    }
    $stmt_check->close();
    $result = consultarBD();
}

// -------- Actualizar usuario --------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar'])) {
    $id = intval($_POST['id']);
    $user = $_POST["user"];
    $password = $_POST["password"];

    if (!empty($password)) {
        $sql = "UPDATE login_user SET username=?, password=?, updated_at=NOW() WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $user, $password, $id);
    } else {
        $sql = "UPDATE login_user SET username=?, updated_at=NOW() WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $user, $id);
    }
    $stmt->execute();
    echo "<h3 style='color:green;'>Usuario actualizado.</h3>";
    $result = consultarBD();
}

// -------- Eliminar usuario --------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_id'])) {
    $id = intval($_POST['eliminar_id']);
    $sql = "DELETE FROM login_user WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    echo "<h3 style='color:red;'>Usuario eliminado.</h3>";
    $result = consultarBD();
}

$result = consultarBD();
?>

<!-- Tabla de usuarios -->
<table border="1" cellpadding="5" cellspacing="0" style="margin-top:30px; width:100%;">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Contraseña</th>
            <th>Creado</th>
            <th>Actualizado</th>
            <th>Último Inicio</th>
            <th>Último Cierre</th>
            <th>Sesión ID</th>
            <th>Conexiones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['password']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                    <td><?php echo $row['last_login'] ? htmlspecialchars($row['last_login']) : '-'; ?></td>
                    <td><?php echo $row['last_logout'] ? htmlspecialchars($row['last_logout']) : '-'; ?></td>
                    <td><?php echo $row['session_id'] ? htmlspecialchars($row['session_id']) : '-'; ?></td>
                    <td><?php echo htmlspecialchars($row['login_count']); ?></td>
                    <td>
                        <form method="post" style="display:inline;" onsubmit="return confirm('¿Eliminar este usuario?');">
                            <input type="hidden" name="eliminar_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Eliminar">
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="editar_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Editar">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="10" style="text-align:center;">No hay usuarios registrados</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
