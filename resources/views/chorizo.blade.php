<?php


    // Parámetros de la conexión
    $host = 'localhost';
    $db = 'neering';  // Aquí debes poner el nombre de tu base de datos
    $user = 'root';  // Usuario
    $pass = '';  // Contraseña vacía
    $charset = 'utf8mb4';  // Codificación de caracteres recomendada

    // Configuración de DSN
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

    // Opciones de conexión PDO
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Modo de error: excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Modo de fetch: asociativo
        PDO::ATTR_EMULATE_PREPARES => false,  // Desactivar emulación de prepared statements
    ];

    // Crear la conexión
    $pdo = new PDO($dsn, $user, $pass, $options);

    echo "Conexión exitosa!";


$sql = "SELECT image FROM products WHERE id = :id";


$stmt = $pdo->prepare($sql);


$imagenes = array(139);

foreach($imagenes as $imagen_id)
{
    $stmt->bindParam(':id', $imagen_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Obtener el resultado (la imagen como Blob)
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $imagen_blob = $row['image'];

        // Convertir la imagen a Base64
        $imagen_base64 = base64_encode($imagen_blob);

        // Mostrar la imagen en una etiqueta <img>
        echo '<img src="data:image/jpeg;base64,' . $imagen_base64 . '" alt="Imagen desde Base64">';
    }
}




/* $imagen_blob = file_get_contents($imagen_path);

// Convertir la imagen a Base64
$imagen_base64 = base64_encode($imagen_blob);

// Mostrar la imagen en formato Base64 en una etiqueta <img>
echo '<img src="data:image/jpeg;base64,' . $imagen_base64 . '" alt="Imagen Base64">'; */

?>