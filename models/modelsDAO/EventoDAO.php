<?php
require_once '../config/database.php';
require_once '../models/Eventos.php';

class EventoDAO
{
    private $conn;
    private $defaultImage = '../assets/img/CasaSolidaria/defaultLogo.png';


    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los eventos
    public function obtenerTodos()
    {
        $query = "SELECT * FROM evento ORDER BY fechaInicio DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $eventos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventos[] = new Evento(
                $row['idEvento'],
                $row['titulo'],
                $row['descripcion'],
                $row['fechaInicio'],
                $row['fechaFinal'],
                $row['ubicacion'],
                $row['estado'],
                $row['imagen']
            );
        }
        return $eventos;
    }

    // Obtener un evento por ID
    public function obtenerPorId($id)
    {
        $query = "SELECT * FROM evento WHERE idEvento = :idEvento";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idEvento', $id);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new Evento(
                $row['idEvento'],
                $row['titulo'],
                $row['descripcion'],
                $row['fechaInicio'],
                $row['fechaFinal'],
                $row['ubicacion'],
                $row['estado'],
                $row['imagen']
            );
        }
        return null;
    }

    // Crear un nuevo evento
    public function crear($datos, $rutaImagen)
    {
        $query = "INSERT INTO evento (titulo, descripcion, imagen, fechaInicio, fechaFinal, ubicacion, estado) 
                  VALUES (:titulo, :descripcion, :imagen, :fechaInicio, :fechaFinal, :ubicacion, :estado)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titulo', $datos['titulo']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':imagen', $rutaImagen);
        $stmt->bindParam(':fechaInicio', $datos['fechaInicio']);
        $stmt->bindParam(':fechaFinal', $datos['fechaFinal']);
        $stmt->bindParam(':ubicacion', $datos['ubicacion']);
        $stmt->bindParam(':estado', $datos['estado']);

        return $stmt->execute();
    }

    // Actualizar un evento existente
    public function actualizar($datos, $rutaImagen)
    {
        $query = "UPDATE evento SET 
                  titulo = :titulo,
                  descripcion = :descripcion,
                  imagen = :imagen,
                  fechaInicio = :fechaInicio,
                  fechaFinal = :fechaFinal,
                  ubicacion = :ubicacion,
                  estado = :estado
                  WHERE idEvento = :idEvento";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':idEvento', $datos['id']);
        $stmt->bindParam(':titulo', $datos['titulo']);
        $stmt->bindParam(':descripcion', $datos['descripcion']);
        $stmt->bindParam(':imagen', $rutaImagen);
        $stmt->bindParam(':fechaInicio', $datos['fechaInicio']);
        $stmt->bindParam(':fechaFinal', $datos['fechaFinal']);
        $stmt->bindParam(':ubicacion', $datos['ubicacion']);
        $stmt->bindParam(':estado', $datos['estado']);

        return $stmt->execute();
    }

    public function eliminar($id)
    {
        $query = "DELETE FROM evento WHERE idEvento = :idEvento";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':idEvento', $id);
        return $stmt->execute();
    }

    public function subirArchivo($archivo, $nombreCampo)
    {
        $directorio = "../assets/img/Eventos/";

        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }

        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        $nombreArchivo = $nombreCampo . '_' . time() . '_' . uniqid() . '.' . pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $rutaArchivo = $directorio . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
            return $rutaArchivo;
        } else {
            return false;
        }
    }

    public function obtenerEventosPublicos()
    {
        $query = "SELECT * FROM evento 
                  WHERE estado = 'activo' AND fechaFinal >= CURDATE() 
                  ORDER BY fechaInicio ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $eventos = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $eventos[] = new Evento(
                $row['idEvento'],
                $row['titulo'],
                $row['descripcion'],
                $row['fechaInicio'],
                $row['fechaFinal'],
                $row['ubicacion'],
                $row['estado'],
                $row['imagen']
            );
        }
        return $eventos;
    }
}
