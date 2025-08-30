<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad de Formularios y Objetos - Versión Mejorada</title>
</head>
<body>

<?php
class Formulario {

    private $titulo;

    // Constructor para asignar el título al formulario
    public function __construct($titulo)
    {
        $this->titulo = $titulo;
    }

    // Formulario de login
    public function mostrarLogin()
    {
        echo <<<HTML
        <h2>{$this->titulo}</h2>
        <form action="#" method="post">
            <label for="usuario">Usuario:</label><br>
            <input type="text" id="usuario" name="usuario"><br><br>

            <label for="contrasena">Contraseña:</label><br>
            <input type="password" id="contrasena" name="contrasena"><br><br>

            <input type="submit" value="Iniciar sesión">
        </form>
        HTML;
    }

    // Formulario de registro
    public function mostrarRegistro()
    {
        echo <<<HTML
        <h2>{$this->titulo}</h2>
        <form action="#" method="post">
            <label for="nombre">Nombre:</label><br>
            <input type="text" id="nombre" name="nombre"><br><br>

            <label for="apellido">Apellido:</label><br>
            <input type="text" id="apellido" name="apellido"><br><br>

            <label for="fecha_nacimiento">Fecha de Nacimiento:</label><br>
            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"><br><br>

            <label>Sexo:</label><br>
            <input type="radio" id="masculino" name="sexo" value="masculino">
            <label for="masculino">Masculino</label><br>

            <input type="radio" id="femenino" name="sexo" value="femenino">
            <label for="femenino">Femenino</label><br>

            <input type="radio" id="otro" name="sexo" value="otro">
            <label for="otro">Otro</label><br><br>

            <input type="submit" value="Registrarse">
        </form>
        HTML;
    }
}

// Crear instancias
$login = new Formulario("Formulario de Inicio de Sesión");
$registro = new Formulario("Formulario de Registro de Usuario");

// Mostrar ambos formularios
echo "<h1>Objeto 1: Login</h1>";
$login->mostrarLogin();

echo "<hr>";

echo "<h1>Objeto 2: Registro</h1>";
$registro->mostrarRegistro();

?>

</body>
</html>
