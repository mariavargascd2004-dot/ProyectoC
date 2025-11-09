<?php
session_start();
require_once '../config/database.php';
require_once '../models/modelsDAO/UsuarioDAO.php';
require_once '../models/AdminAssociado.php';
require_once '../models/modelsDAO/AdminAssociadoDAO.php';

$db = (new Database())->getConnection();
$adminDAO = new AdminAssociadoDAO($db);
$usuarioDAO = new UsuarioDAO($db);

$accion = $_POST['accion'] ?? '';

$idUsuarioLogado = $_SESSION['user']['id'] ?? null;

switch ($accion) {
    case 'registrarAdmin':


        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $tipo =  $_POST['tipo'] ?? 'associado';

        // Datos específicos del admin
        $apellido = $_POST['apellido'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';

        $fotoPerfil = '';
        if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === 0) {
            $uploadDir = '../assets/img/perfil/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['fotoPerfil']['name'], PATHINFO_EXTENSION);
            $nuevoNombre = uniqid('admin_') . '.' . $ext;
            $rutaCompleta = $uploadDir . $nuevoNombre;

            if (move_uploaded_file($_FILES['fotoPerfil']['tmp_name'], $rutaCompleta)) {
                $fotoPerfil = '../assets/img/perfil/' . $nuevoNombre;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error al subir la imagen']);
                exit;
            }
        }

        // Validar Correo
        if ($usuarioDAO->existeEmail($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email ya registrado']);
            exit;
        }

        // Crear objeto AdminAssociado
        $admin = new AdminAssociado(
            $nombre,
            $email,
            $password,
            $tipo,
            $apellido,
            $descripcion,
            $fotoPerfil,
        );

        // Registrar admin (inserta en usuarios y adminAssociado)
        $idUsuario = $adminDAO->registrar($admin);

        if ($idUsuario) {
            echo json_encode(['status' => 'ok', 'idUsuario' => $idUsuario, 'message' => 'Admin registrado correctamente']);
        } else {
            error_log("Error al registrar admin. ID Usuario: " . $idUsuario);
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar admin']);
        }
        break;

    case "actualizarPerfil":
        header('Content-Type: application/json');

        $idUsuarioForm = $_POST['idUsuario'] ?? 0;

        if (!$idUsuarioLogado || $idUsuarioLogado != $idUsuarioForm) {
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $nome = $_POST['nombre'] ?? '';
        $dadosAdmin = [
            'apellido' => $_POST['apellido'] ?? '',
            'descripcion' => $_POST['descripcion'] ?? ''
        ];
        $newFotoPath = null;

        if (empty($nome) || empty($dadosAdmin['apellido']) || empty($dadosAdmin['descripcion'])) {
            echo json_encode(['status' => 'error', 'message' => 'Nome, Sobrenome e Biografia são obrigatórios.']);
            exit;
        }

        if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/img/perfil/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($_FILES['fotoPerfil']['name'], PATHINFO_EXTENSION);
            $novoNombre = uniqid('admin_') . '.' . $ext;
            $rutaCompleta = $uploadDir . $novoNombre;

            if (move_uploaded_file($_FILES['fotoPerfil']['tmp_name'], $rutaCompleta)) {
                $newFotoPath = '../assets/img/perfil/' . $novoNombre;
                $dadosAdmin['fotoPerfil'] = $newFotoPath;

                $infoAntiga = $adminDAO->obtenerPorId($idUsuarioLogado);

                if ($infoAntiga && !empty($infoAntiga['FotoPerfilAssociado']) && file_exists($infoAntiga['FotoPerfilAssociado'])) {
                    if (basename($infoAntiga['FotoPerfilAssociado']) != 'default.png') {
                        unlink($infoAntiga['FotoPerfilAssociado']);
                    }
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao mover a nova imagem de perfil.']);
                exit;
            }
        }

        $db->beginTransaction();
        try {
            $successUsuario = $usuarioDAO->atualizarNome($idUsuarioLogado, $nome);

            $successAdmin = $adminDAO->atualizarPerfil($idUsuarioLogado, $dadosAdmin);

            if ($successUsuario && $successAdmin) {
                $db->commit();
                $_SESSION['user']['nome'] = $nome;

                echo json_encode([
                    'status' => 'ok',
                    'message' => 'Perfil atualizado com sucesso!',
                    'newFotoPath' => $newFotoPath
                ]);
            } else {
                $db->rollBack();
                echo json_encode(['status' => 'error', 'message' => 'Não foi possível atualizar o perfil.']);
            }
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Erro na transação de atualizarPerfil: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Erro fatal no servidor.']);
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

error_log("Acción recibida: " . $accion);
error_log("Datos POST: " . print_r($_POST, true));
error_log("Archivos: " . print_r($_FILES, true));
