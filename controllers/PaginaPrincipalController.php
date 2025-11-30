<?php
require_once '../models/modelsDAO/PaginaPrincipalDAO.php';

class PaginaPrincipalController
{
    private $dao;

    public function __construct()
    {
        $this->dao = new PaginaPrincipalDAO();
    }

    public function obtenerDatos()
    {
        return $this->dao->obtenerDatos();
    }

    public function actualizarHistoria($historia)
    {
        return $this->dao->actualizarCampo('historia', $historia);
    }

    public function actualizarMision($mision)
    {
        return $this->dao->actualizarCampo('mision', $mision);
    }

    public function actualizarVision($vision)
    {
        return $this->dao->actualizarCampo('vision', $vision);
    }

    public function actualizarPortada($archivo)
    {
        $datosActuales = $this->obtenerDatos();
        $rutaAnterior = $datosActuales ? $datosActuales->getPortada() : '';
        $campo = 'portada';

        // Subir el nuevo archivo.
        $rutaArchivo = $this->dao->subirArchivo($archivo, $campo);
        
        if (!$rutaArchivo) {
            return false;
        }

        // Actualizar la base de datos
        $resultado = $this->dao->actualizarCampo($campo, $rutaArchivo);

        if ($resultado) {
            // Eliminar el archivo anterior si la actualización fue exitosa
            if ($rutaAnterior) {
                $this->eliminarArchivoAnterior($rutaAnterior);
            }
        } else {
            // Si falla la BD, eliminar el archivo recién subido
            $this->eliminarArchivoAnterior($rutaArchivo);
        }
        
        return $resultado;
    }

    public function actualizarLogo($archivo)
    {
        $datosActuales = $this->obtenerDatos();
        $rutaAnterior = $datosActuales ? $datosActuales->getLogo() : '';
        $campo = 'logo';

        // Subir el nuevo archivo.
        $rutaArchivo = $this->dao->subirArchivo($archivo, $campo);
        
        if (!$rutaArchivo) {
            return false;
        }

        // Actualizar la base de datos
        $resultado = $this->dao->actualizarCampo($campo, $rutaArchivo);

        if ($resultado) {
            // Eliminar el archivo anterior si la actualización fue exitosa
            if ($rutaAnterior) {
                $this->eliminarArchivoAnterior($rutaAnterior);
            }
        } else {
            // Si falla la BD, eliminar el archivo recién subido
            $this->eliminarArchivoAnterior($rutaArchivo);
        }
        
        return $resultado;
    }

    public function actualizarFotoGaleria($archivo, $numeroFoto)
    {
        $datosActuales = $this->obtenerDatos();
        $rutaAnterior = '';

        switch ($numeroFoto) {
            case 1:
                $campo = 'primerafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getPrimerafotogaleria() : '';
                break;
            case 2:
                $campo = 'segundafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getSegundafotogaleria() : '';
                break;
            case 3:
                $campo = 'tercerafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getTercerafotogaleria() : '';
                break;
            case 4:
                $campo = 'cuartafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getCuartafotogaleria() : '';
                break;
            default:
                return false;
        }

        // Subir el nuevo archivo.
        $rutaArchivo = $this->dao->subirArchivo($archivo, 'foto_galeria_' . $numeroFoto);
        
        if (!$rutaArchivo) {
            return false;
        }

        // Actualizar la base de datos
        $result = $this->dao->actualizarCampo($campo, $rutaArchivo);
        
        if ($result) {
            // Eliminar el archivo anterior si la actualización fue exitosa
            if ($rutaAnterior) {
                $this->eliminarArchivoAnterior($rutaAnterior);
            }
        } else {
            // Si falla la BD, eliminar el archivo recién subido
            $this->eliminarArchivoAnterior($rutaArchivo);
        }
            
        return $result;
    }

    public function actualizarInformacionEmpresa($datos)
    {
        $success = true;
        $success = $success && $this->dao->actualizarCampo('telefono', $datos['telefono']);
        $success = $success && $this->dao->actualizarCampo('direccion', $datos['direccion']);
        $success = $success && $this->dao->actualizarCampo('horarios', $datos['horarios']);
        $success = $success && $this->dao->actualizarCampo('celular', $datos['celular']);
        $success = $success && $this->dao->actualizarCampo('facebook', $datos['facebook']);
        $success = $success && $this->dao->actualizarCampo('instagram', $datos['instagram']);

        return $success;
    }

    public function eliminarFotoGaleria($numeroFoto)
    {
        $datosActuales = $this->obtenerDatos();
        $rutaAnterior = '';
        $campo = '';

        switch ($numeroFoto) {
            case 1:
                $campo = 'primerafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getPrimerafotogaleria() : '';
                break;
            case 2:
                $campo = 'segundafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getSegundafotogaleria() : '';
                break;
            case 3:
                $campo = 'tercerafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getTercerafotogaleria() : '';
                break;
            case 4:
                $campo = 'cuartafotogaleria';
                $rutaAnterior = $datosActuales ? $datosActuales->getCuartafotogaleria() : '';
                break;
            default:
                return false;
        }

        // Actualizar el campo en la BD para el camino default
        $success = $this->dao->actualizarCampo($campo, '../assets/img/CasaSolidaria/defaultLogo.png');
        
        if ($success) {
            // Eliminar el archivo físico anterior
            $this->eliminarArchivoAnterior($rutaAnterior);
        }
        
        return $success;
    }

    private function eliminarArchivoAnterior($rutaRelativa)
    {
        $rutaDefault = '../assets/img/CasaSolidaria/defaultLogo.png';

        // Si no hay archivo anterior o es el default, consideramos éxito
        if (!$rutaRelativa || $rutaRelativa === $rutaDefault) {
            return true;
        }

        // CORRECCIÓN DE RUTA: Eliminar el prefijo '../' si existe (soluciona el warning de ltrim)
        if (str_starts_with($rutaRelativa, '../')) {
            $rutaCorreta = substr($rutaRelativa, 3);
        } else {
            $rutaCorreta = $rutaRelativa;
        }

        // Construir ruta absoluta
        $rutaAbsoluta = realpath(__DIR__ . '/../' . $rutaCorreta);

        // Fallback para rutas donde realpath falla
        if ($rutaAbsoluta === false || !file_exists($rutaAbsoluta)) {
            $rutaAbsolutaManual = __DIR__ . '/../' . $rutaCorreta;
            
            if (file_exists($rutaAbsolutaManual)) {
                 $rutaAbsoluta = $rutaAbsolutaManual;
            } else {
                return true; // Ya no existe (éxito en la eliminación lógica)
            }
        }

        // Verificar si es un archivo y intentar eliminar
        if (is_file($rutaAbsoluta)) {
            if (unlink($rutaAbsoluta)) {
                return true; // Éxito
            } else {
                return false; // Error de permisos o desconocido
            }
        }
        
        return true; // La ruta ya no existe o no es un archivo, consideramos éxito
    }
}

// Procesar las solicitudes
$controller = new PaginaPrincipalController();
$response = ['success' => false, 'message' => ''];

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'actualizar_historia':
            if (isset($_POST['historia'])) {
                $success = $controller->actualizarHistoria($_POST['historia']);
                $response = ['success' => $success, 'message' => $success ? 'Historia actualizada correctamente' : 'Error al actualizar historia'];
            }
            break;

        case 'actualizar_mision':
            if (isset($_POST['mision'])) {
                $success = $controller->actualizarMision($_POST['mision']);
                $response = ['success' => $success, 'message' => $success ? 'Misión actualizada correctamente' : 'Error al actualizar misión'];
            }
            break;

        case 'actualizar_vision':
            if (isset($_POST['vision'])) {
                $success = $controller->actualizarVision($_POST['vision']);
                $response = ['success' => $success, 'message' => $success ? 'Visión actualizada correctamente' : 'Error al actualizar visión'];
            }
            break;

        case 'actualizar_portada':
            if (isset($_FILES['portada'])) {
                $success = $controller->actualizarPortada($_FILES['portada']);
                $response = ['success' => $success, 'message' => $success ? 'Portada actualizada correctamente' : 'Error al actualizar portada'];
            }
            break;

        case 'actualizar_logo':
            if (isset($_FILES['logo'])) {
                $success = $controller->actualizarLogo($_FILES['logo']);
                $response = ['success' => $success, 'message' => $success ? 'Logo actualizado correctamente' : 'Error al actualizar logo'];
            }
            break;

        case 'actualizar_foto_galeria':
            if (isset($_FILES['foto']) && isset($_POST['numero_foto'])) {
                $success = $controller->actualizarFotoGaleria($_FILES['foto'], (int)$_POST['numero_foto']);
                $response = ['success' => $success, 'message' => $success ? 'Foto de galería actualizada correctamente' : 'Error al actualizar foto'];
            }
            break;

        case 'eliminar_foto_galeria':
            if (isset($_POST['numero_foto'])) {
                $success = $controller->eliminarFotoGaleria((int)$_POST['numero_foto']);
                $response = ['success' => $success, 'message' => $success ? 'Foto eliminada correctamente' : 'Error al eliminar foto'];
            }
            break;

        case 'actualizar_informacion_empresa':
            $datos = [
                'telefono' => $_POST['telefono'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'horarios' => $_POST['horarios'] ?? '',
                'celular' => $_POST['celular'] ?? '',
                'facebook' => $_POST['facebook'] ?? '',
                'instagram' => $_POST['instagram'] ?? ''
            ];
            $success = $controller->actualizarInformacionEmpresa($datos);
            $response = ['success' => $success, 'message' => $success ? 'Información actualizada correctamente' : 'Error al actualizar información'];
            break;
            
        default:
            $response = ['success' => false, 'message' => 'Acción desconocida'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}