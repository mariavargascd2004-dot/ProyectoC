<?php
session_start();
require_once '../config/database.php';
require_once '../models/modelsDAO/UsuarioDAO.php';
require_once '../models/Usuario.php';

$db = (new Database())->getConnection();
$usuarioDAO = new UsuarioDAO($db);

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'registrar':
        $nombre = $_POST['nombre'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $tipo = $_POST['tipo'];

        if ($usuarioDAO->existeEmail($email)) {
            echo json_encode(['status' => 'error', 'message' => 'Email ya registrado']);
            exit;
        }

        $usuario = new Usuario($nombre, $email, $password, $tipo);
        $idUsuario = $usuarioDAO->registrar($usuario);
        echo json_encode(['status' => 'ok', 'message' => 'Usuario registrado']);

        $_SESSION['user'] = [
            "id" => $idUsuario,
            "tipo" => $tipo,
            "nome" => $nombre,
            "alerta" => true
        ];


        break;

    case 'login':
        $email = $_POST['email'];
        $password = $_POST['password'];

        $user = $usuarioDAO->login($email, $password);
        if ($user) {
            $_SESSION['user'] = [
                "id" => $user['idUsuario'],
                "tipo" => $user['tipo'],
                "nome" => $user['nombre'],
                "alerta" => true

            ];
            echo json_encode(['status' => 'ok', "message" => "Bem vindo/a de novo!"]);
        } else {
            echo json_encode(['status' => 'incorrect_Credencial', 'message' => 'Credenciales incorrectas']);
        }
        break;
    case 'eliminar':
        $id = $_POST['id'];

        $user = $usuarioDAO->eliminarUsuario($id);

        if ($user) {
            echo json_encode([
                'status' => 'ok',
                'message' => 'O empreendedor e todos os seus registros foram eliminados com sucesso.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Não foi possível eliminar o registro.'
            ]);
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
