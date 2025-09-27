```php
<?php
// ================= CONEXIÓN ===================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "productos_php";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Carpeta imágenes
$upload_dir_rel = "imagenes_productos/";
$upload_dir_abs = __DIR__ . DIRECTORY_SEPARATOR . $upload_dir_rel;
if (!is_dir($upload_dir_abs)) {
    die("Error: La carpeta de imágenes no existe en: " . $upload_dir_abs);
}

// ================= CRUD ===================
// Crear producto
if (isset($_POST['crear'])) {
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $cantidad = $_POST['cantidad'] ?? 0;
    $imagen_rel = null;

    if (!empty($_FILES['imagen']['name'])) {
        $basename = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES["imagen"]["name"]));
        $archivo_abs = $upload_dir_abs . $basename;
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivo_abs)) {
            $imagen_rel = $upload_dir_rel . $basename;
        }
    }

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, imagen, precio, cantidad, fecha_creacion, fecha_actualizacion) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
    $stmt->bind_param("sssdi", $nombre, $descripcion, $imagen_rel, $precio, $cantidad);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Editar producto
if (isset($_POST['editar'])) {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $precio = $_POST['precio'] ?? 0;
    $cantidad = $_POST['cantidad'] ?? 0;
    $imagen_rel = null;

    if (!empty($_FILES['imagen']['name'])) {
        $basename = time() . "_" . preg_replace('/[^A-Za-z0-9_\-\.]/', '_', basename($_FILES["imagen"]["name"]));
        $archivo_abs = $upload_dir_abs . $basename;
        if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $archivo_abs)) {
            $imagen_rel = $upload_dir_rel . $basename;

            // borrar vieja
            $res = $conn->prepare("SELECT imagen FROM productos WHERE id = ?");
            $res->bind_param("i", $id);
            $res->execute();
            $res->bind_result($old_img);
            if ($res->fetch()) {
                if ($old_img && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $old_img)) {
                    @unlink(__DIR__ . DIRECTORY_SEPARATOR . $old_img);
                }
            }
            $res->close();
        }
    }

    if ($imagen_rel !== null) {
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, imagen=?, precio=?, cantidad=?, fecha_actualizacion=NOW() WHERE id=?");
        $stmt->bind_param("sssdis", $nombre, $descripcion, $imagen_rel, $precio, $cantidad, $id);
    } else {
        $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, cantidad=?, fecha_actualizacion=NOW() WHERE id=?");
        $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $cantidad, $id);
    }

    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Eliminar producto
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $res = $conn->prepare("SELECT imagen FROM productos WHERE id=?");
    $res->bind_param("i", $id);
    $res->execute();
    $res->bind_result($img_to_delete);
    if ($res->fetch()) {
        if ($img_to_delete && file_exists(__DIR__ . DIRECTORY_SEPARATOR . $img_to_delete)) {
            @unlink(__DIR__ . DIRECTORY_SEPARATOR . $img_to_delete);
        }
    }
    $res->close();

    $stmt = $conn->prepare("DELETE FROM productos WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Consultar productos
$result = $conn->query("SELECT * FROM productos ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>CRUD Productos - ADSO30</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
:root {
  --rosa:#87CEFA;
  --accent:#1890ff;
  --danger:#ff4d4f;
  --muted:#666;
}
body {font-family:sans-serif;margin:20px;background:#fdfdfd;}
h1 {margin:0 0 20px}
.container {max-width:1200px;margin:0 auto;}
.card {background:#fff;padding:16px;border-radius:8px;box-shadow:0 2px 6px rgba(0,0,0,.1);margin-bottom:20px;}
label {display:block;margin-top:8px;font-size:14px;}
input, textarea {width:100%;padding:8px;margin-top:4px;border:1px solid #ccc;border-radius:6px;}
.btn {background:var(--accent);color:#fff;padding:8px 14px;border:none;border-radius:6px;cursor:pointer;}
.btn.danger {background:var(--danger);}
table {width:100%;border-collapse:collapse;}
th, td {padding:10px;border-bottom:1px solid #eee;text-align:center;}
thead th {background:var(--rosa);}
img.thumb {width:60px;height:60px;object-fit:cover;border-radius:6px;}
/* Modal */
.modal {display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);justify-content:center;align-items:center;}
.modal-content {background:#fff;padding:20px;border-radius:8px;width:400px;max-width:90%;}
.modal-content h3 {margin-top:0;}
.close {float:right;font-size:18px;cursor:pointer;}
</style>
</head>
<body>
<div class="container">
  <h1>Gestión de Productos</h1>

  <div class="card">
    <h3>Agregar Nuevo Producto</h3>
    <form method="POST" enctype="multipart/form-data">
      <label>Nombre</label>
      <input type="text" name="nombre" required>
      <label>Descripción</label>
      <textarea name="descripcion" required></textarea>
      <label>Imagen</label>
      <input type="file" name="imagen" accept="image/*" required>
      <label>Precio</label>
      <input type="number" step="0.01" name="precio" required>
      <label>Cantidad</label>
      <input type="number" name="cantidad" required>
      <br>
      <button class="btn" type="submit" name="crear">Guardar producto</button>
    </form>
  </div>

  <div class="card">
    <h3>Lista de Productos</h3>
    <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Imagen</th>
          <th>Nombre</th>
          <th>Descripción</th>
          <th>Precio</th>
          <th>Cantidad</th>
          <th>Creado</th>
          <th>Actualizado</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $row['id'] ?></td>
          <td><?php if ($row['imagen']): ?><img class="thumb" src="<?= htmlspecialchars($row['imagen']) ?>"><?php endif; ?></td>
          <td><?= htmlspecialchars($row['nombre']) ?></td>
          <td><?= htmlspecialchars($row['descripcion']) ?></td>
          <td><?= "$" . number_format($row['precio'], 0, ',', '.') ?></td>
          <td><?= htmlspecialchars($row['cantidad']) ?></td>
          <td><?= htmlspecialchars($row['fecha_creacion']) ?></td>
          <td><?= htmlspecialchars($row['fecha_actualizacion']) ?></td>
          <td>
            <button class="btn" onclick="abrirModal(<?= $row['id'] ?>,'<?= htmlspecialchars($row['nombre']) ?>','<?= htmlspecialchars($row['descripcion']) ?>','<?= htmlspecialchars($row['precio']) ?>','<?= htmlspecialchars($row['cantidad']) ?>')">Editar</button>
            <a class="btn danger" href="?eliminar=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este producto?')">Eliminar</a>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
    <?php else: ?>
      <p>No hay productos aún.</p>
    <?php endif; ?>
  </div>
</div>

<!-- Modal Editar -->
<div id="modalEditar" class="modal">
  <div class="modal-content">
    <span class="close" onclick="cerrarModal()">&times;</span>
    <h3>Editar Producto</h3>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" id="edit_id">
      <label>Nombre</label>
      <input type="text" name="nombre" id="edit_nombre" required>
      <label>Descripción</label>
      <textarea name="descripcion" id="edit_descripcion" required></textarea>
      <label>Imagen (opcional)</label>
      <input type="file" name="imagen" accept="image/*">
      <label>Precio</label>
      <input type="number" step="0.01" name="precio" id="edit_precio" required>
      <label>Cantidad</label>
      <input type="number" name="cantidad" id="edit_cantidad" required>
      <br>
      <button class="btn" type="submit" name="editar">Guardar cambios</button>
    </form>
  </div>
</div>

<script>
function abrirModal(id,nombre,descripcion,precio,cantidad){
  document.getElementById("modalEditar").style.display="flex";
  document.getElementById("edit_id").value=id;
  document.getElementById("edit_nombre").value=nombre;
  document.getElementById("edit_descripcion").value=descripcion;
  document.getElementById("edit_precio").value=precio;
  document.getElementById("edit_cantidad").value=cantidad;
}
function cerrarModal(){
  document.getElementById("modalEditar").style.display="none";
}
</script>
</body>
</html>
```
