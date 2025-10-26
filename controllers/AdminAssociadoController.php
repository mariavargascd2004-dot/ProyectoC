<?php
session_start();
require_once '../config/database.php';
require_once '../models/UsuarioDAO.php';
require_once '../models/AdminAssociado.php';
require_once '../models/AdminAssociadoDAO.php';

$db = (new Database())->getConnection();
$adminDAO = new AdminAssociadoDAO($db);
$usuarioDAO = new UsuarioDAO($db);

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'registrarAdmin':
        error_log("Tipo de usuario recibido: " . ($_POST['tipo'] ?? 'NO RECIBIDO'));
        error_log("Datos completos POST: " . print_r($_POST, true));

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
        $aprobado = 0;

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
            $aprobado
        );

        // Registrar admin (inserta en usuarios y adminAssociado)
        $idUsuario = $adminDAO->registrar($admin);

        if ($idUsuario) {
            $_SESSION['user'] = [
                "id" => $idUsuario,
                "tipo" => $tipo,
                "nome" => $nombre,
                "alerta" => true
            ];
            echo json_encode(['status' => 'ok', 'idUsuario' => $idUsuario, 'message' => 'Admin registrado correctamente']);
        } else {
            error_log("Error al registrar admin. ID Usuario: " . $idUsuario);
            echo json_encode(['status' => 'error', 'message' => 'Error al registrar admin']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
        break;
}

error_log("Acción recibida: " . $accion);
error_log("Datos POST: " . print_r($_POST, true));
error_log("Archivos: " . print_r($_FILES, true));
