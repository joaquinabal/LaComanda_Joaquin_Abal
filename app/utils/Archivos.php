<?php

class Archivos
{
    static public function darAltaImagen($imagen, $nombre_imagen, $ruta_imagen)
    {
        if (isset($imagen) && $imagen['error'] === UPLOAD_ERR_OK) {
            $extension_imagen = explode(".", strtolower($imagen['name']))[1];

            $destino_imagen = $ruta_imagen . $nombre_imagen . "." . $extension_imagen;
            if (!move_uploaded_file($imagen["tmp_name"], $destino_imagen)) {
                echo "Error al subir la imagen.";
                return null;
            } else {
                echo "Imagen subida correctamente.";
                return $destino_imagen;
            }
        }
    }

    public static function descargarCSV($datos)
    {

        $nombreArchivo = "exportacion_" . date("Y-m-d_H-i-s") . ".csv";

        // Configura las cabeceras para la descarga del archivo
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $nombreArchivo);

        $rutaArchivo =  dirname(__DIR__) . '/csv/' . $nombreArchivo; // Cambia 'carpeta_deseada' por la ruta de tu carpeta

        // Asegúrate de que la carpeta existe
        if (!file_exists(dirname($rutaArchivo))) {
            mkdir(dirname($rutaArchivo), 0777, true);
        }

        // Abre un flujo de escritura para el archivo CSV
        $salida = fopen($rutaArchivo, 'w');
        // Escribe la cabecera del CSV (nombres de las columnas)
        $cabecera = array_keys($datos[0]);
        fputcsv($salida, $cabecera);

        // Escribe cada fila en el CSV
        foreach ($datos as $fila) {
            fputcsv($salida, $fila);
        }

        // Cierra el flujo de salida
        fclose($salida);
        exit;
    }
}
