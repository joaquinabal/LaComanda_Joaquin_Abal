<?php
    /*private $ruta;

    public function __construct()
    {
        $this->ruta = "../log.txt";
    }

    public function log($mensaje)
    {
        $fecha = new DateTime();
        $registro = $fecha->format('Y-m-d H:i:s') . " - " . $mensaje . PHP_EOL;
        file_put_contents($this->ruta, $registro, FILE_APPEND);
    }*/



class Logger
{
    private $ruta;

    public function __construct()
    {
        $this->ruta = "./log.json";
    }

    public function log($accion, $method, $id, $usuario, $nombre, $rol_empleado)
    {
        // Crear el timestamp
        $fecha = new DateTime();
        $registro = [
            'fecha' => $fecha->format('Y-m-d H:i:s'),
            'method' => $method,
            'accion' => $accion,
            'id_usuario' => $id,
            'usuario' => $usuario,
            'nombre' => $nombre,
            'rol_empleado' => $rol_empleado
        ];

        // Leer el contenido actual del archivo JSON
        $registros = [];
        if (file_exists($this->ruta)) {
            $contenido = file_get_contents($this->ruta);
            if ($contenido) {
                $registros = json_decode($contenido, true);
            }
        }

        // Agregar el nuevo registro al array de registros
        $registros[] = $registro;

        // Escribir el array de registros actualizado al archivo JSON
        file_put_contents($this->ruta, json_encode($registros, JSON_PRETTY_PRINT));
    }
}

