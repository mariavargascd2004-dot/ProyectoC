<?php
require_once '../config/database.php';
require_once '../models/PaginaPrincipal.php';

class PaginaPrincipalDAO
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Obtener todos los datos de la página principal
    public function obtenerDatos()
    {
        $query = "SELECT * FROM pagina_principal ORDER BY id DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return new PaginaPrincipal(
                $row['portada'],
                $row['historia'],
                $row['mision'],
                $row['vision'],
                $row['primerafotogaleria'],
                $row['segundafotogaleria'],
                $row['tercerafotogaleria'],
                $row['cuartafotogaleria'],
                $row['telefono'],
                $row['direccion'],
                $row['horarios'],
                $row['celular'],
                $row['facebook'],
                $row['instagram']
            );
        }
        return null;
    }

    // Actualizar datos de la página principal
    public function actualizarDatos($datos)
    {
        $query = "UPDATE pagina_principal SET 
                  portada = :portada,
                  historia = :historia,
                  mision = :mision,
                  vision = :vision,
                  primerafotogaleria = :primerafotogaleria,
                  segundafotogaleria = :segundafotogaleria,
                  tercerafotogaleria = :tercerafotogaleria,
                  cuartafotogaleria = :cuartafotogaleria,
                  telefono = :telefono,
                  direccion = :direccion,
                  horarios = :horarios,
                  celular = :celular,
                  facebook = :facebook,
                  instagram = :instagram
                  WHERE id = (SELECT id FROM (SELECT id FROM pagina_principal ORDER BY id DESC LIMIT 1) as temp)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':portada', $datos['portada']);
        $stmt->bindParam(':historia', $datos['historia']);
        $stmt->bindParam(':mision', $datos['mision']);
        $stmt->bindParam(':vision', $datos['vision']);
        $stmt->bindParam(':primerafotogaleria', $datos['primerafotogaleria']);
        $stmt->bindParam(':segundafotogaleria', $datos['segundafotogaleria']);
        $stmt->bindParam(':tercerafotogaleria', $datos['tercerafotogaleria']);
        $stmt->bindParam(':cuartafotogaleria', $datos['cuartafotogaleria']);
        $stmt->bindParam(':telefono', $datos['telefono']);
        $stmt->bindParam(':direccion', $datos['direccion']);
        $stmt->bindParam(':horarios', $datos['horarios']);
        $stmt->bindParam(':celular', $datos['celular']);
        $stmt->bindParam(':facebook', $datos['facebook']);
        $stmt->bindParam(':instagram', $datos['instagram']);

        return $stmt->execute();
    }

    // Actualizar solo un campo específico
    public function actualizarCampo($campo, $valor)
    {
        $query = "UPDATE pagina_principal SET $campo = :valor 
                  WHERE id = (SELECT id FROM (SELECT id FROM pagina_principal ORDER BY id DESC LIMIT 1) as temp)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':valor', $valor);

        return $stmt->execute();
    }

    // Subir archivo y retornar la ruta
    public function subirArchivo($archivo, $nombreCampo)
    {
        error_log("=== SUBIR ARCHIVO ===");
        error_log("Nombre campo: " . $nombreCampo);
        error_log("Datos archivo: " . print_r($archivo, true));


        $directorio = "../assets/img/CasaSolidaria/";
        error_log("Directorio destino: " . $directorio);


        if (!is_dir($directorio)) {
            error_log("Creando directorio...");
            mkdir($directorio, 0777, true);
        }

        // Validar que el archivo se subió correctamente
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            error_log("❌ Error en upload: " . $archivo['error']);
            return false;
        }

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedTypes)) {
            return false;
        }

        $nombreArchivo = $nombreCampo . '_' . time() . '_' . uniqid() . '.' . pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $rutaArchivo = $directorio . $nombreArchivo;

        error_log("Nombre archivo final: " . $nombreArchivo);
        error_log("Ruta archivo final: " . $rutaArchivo);

        if (move_uploaded_file($archivo['tmp_name'], $rutaArchivo)) {
            error_log("✅ Archivo movido exitosamente");
            return $rutaArchivo;
        } else {
            error_log("❌ Error al mover archivo");
            error_log("tmp_name: " . $archivo['tmp_name']);
            error_log("destino: " . $rutaArchivo);
            return false;
        }
    }
}
