<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        # Move the uploaded image to the current directory / Mueve la imagen subida al directorio actual
        move_uploaded_file($_FILES['file']['tmp_name'], './' . $_FILES['file']['name']);
        echo "Imagen recibida correctamente.";
    } else {
        echo "Error al recibir la imagen.";
    }
} else {
    echo "Metodo no soportado.";
}
?>
