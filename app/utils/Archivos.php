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

        // Abre un flujo de escritura para el archivo CSV
        $salida = fopen('php://output', 'w');
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

    public static function cargarArchivo($ruta){
        if(file_exists($ruta)){
            $archivo = fopen($ruta, 'r');
            $contenido = fread($archivo,500000);
            fclose($archivo);
            $array_archivo = json_decode($contenido,true);
            return $array_archivo;
        } else {
            return null;
        }
    }

    public static function cargarArchivoFiltrado($ruta, $key_filtro){
        if(file_exists($ruta)){
            $archivo = fopen($ruta, 'r');
            $contenido = fread($archivo,500000);
            fclose($archivo);
            $array_archivo = json_decode($contenido,true);
            $array_archivo_filtrado = [];
            foreach($array_archivo as $objeto){
                    array_push($array_archivo_filtrado, $objeto[$key_filtro]);
                
            }
            return $array_archivo_filtrado;
        }
    }
}
