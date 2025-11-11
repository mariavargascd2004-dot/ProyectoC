<?php
require_once '../models/modelsDAO/EventoDAO.php';

class EventoController
{
    private $dao;
    private $defaultImage = '../assets/img/CasaSolidaria/defaultLogo.png';


    public function __construct()
    {
        $this->dao = new EventoDAO();
    }

    public function obtenerTodos()
    {
        $eventos = $this->dao->obtenerTodos();
        $eventosArray = array_map(function ($evento) {
            return $evento->toArray();
        }, $eventos);
        return ['success' => true, 'data' => $eventosArray];
    }

    public function crear($datos, $archivo)
    {
        $rutaArchivo = $this->defaultImage;
        if (isset($archivo) && $archivo['error'] === UPLOAD_ERR_OK) {
            $rutaSubida = $this->dao->subirArchivo($archivo, 'evento_img');
            if ($rutaSubida) {
                $rutaArchivo = $rutaSubida;
            } else {
                return ['success' => false, 'message' => 'Error ao subir a imagem.'];
            }
        }

        $success = $this->dao->crear($datos, $rutaArchivo);
        return [
            'success' => $success,
            'message' => $success ? 'Evento criado com sucesso!' : 'Erro ao criar evento.'
        ];
    }

    public function actualizar($datos, $archivo)
    {
        $eventoActual = $this->dao->obtenerPorId($datos['id']);
        if (!$eventoActual) {
            return ['success' => false, 'message' => 'Evento não encontrado.'];
        }

        $rutaArchivo = $eventoActual->getImagen();

        if (isset($archivo) && $archivo['error'] === UPLOAD_ERR_OK) {
            $rutaSubida = $this->dao->subirArchivo($archivo, 'evento_img');
            if ($rutaSubida) {
                $this->eliminarArchivoAnterior($rutaArchivo);
                $rutaArchivo = $rutaSubida;
            } else {
                return ['success' => false, 'message' => 'Error ao subir a nova imagem.'];
            }
        }

        $success = $this->dao->actualizar($datos, $rutaArchivo);
        return [
            'success' => $success,
            'message' => $success ? 'Evento atualizado com sucesso!' : 'Erro ao atualizar evento.'
        ];
    }

    public function eliminar($id)
    {
        $eventoActual = $this->dao->obtenerPorId($id);
        if (!$eventoActual) {
            return ['success' => false, 'message' => 'Evento não encontrado.'];
        }

        $rutaImagen = $eventoActual->getImagen();
        $success = $this->dao->eliminar($id);

        if ($success) {
            $this->eliminarArchivoAnterior($rutaImagen);
        }

        return [
            'success' => $success,
            'message' => $success ? 'Evento eliminado com sucesso!' : 'Erro ao eliminar evento.'
        ];
    }

    private function eliminarArchivoAnterior($rutaRelativa)
    {
        if (!$rutaRelativa || str_contains($rutaRelativa, 'defaultLogo.png') || str_contains($rutaRelativa, 'defaultProduct.png') || str_contains($rutaRelativa, 'defaultPooster.png')) {
            return true;
        }

        if (str_starts_with($rutaRelativa, '../')) {
            $rutaCorreta = substr($rutaRelativa, 3);
        } else {
            $rutaCorreta = $rutaRelativa;
        }

        $rutaAbsoluta = realpath(__DIR__ . '/../' . $rutaCorreta);

        if ($rutaAbsoluta === false || !file_exists($rutaAbsoluta)) {
            $rutaAbsolutaManual = __DIR__ . '/../' . $rutaCorreta;
            if (file_exists($rutaAbsolutaManual)) {
                $rutaAbsoluta = $rutaAbsolutaManual;
            } else {
                return true;
            }
        }

        if (is_file($rutaAbsoluta)) {
            return unlink($rutaAbsoluta);
        }

        return true;
    }
}

$controller = new EventoController();
$response = ['success' => false, 'message' => 'Ação desconhecida'];

if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'obtener_todos':
            $response = $controller->obtenerTodos();
            break;

        case 'crear':
            $response = $controller->crear($_POST, $_FILES['imagem'] ?? null);
            break;

        case 'actualizar':
            $response = $controller->actualizar($_POST, $_FILES['imagem'] ?? null);
            break;

        case 'eliminar':
            if (isset($_POST['id'])) {
                $response = $controller->eliminar($_POST['id']);
            } else {
                $response = ['success' => false, 'message' => 'ID do evento não fornecido.'];
            }
            break;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
exit;
