<?php
session_start();
require_once "../config/database.php";
require_once "../models/AdminGeneral.php";
require_once "../models/modelsDAO/AdminGeneralDAO.php";
require_once "../models/modelsDAO/UsuarioDAO.php"; // Agregado para poder editar clientes y asociados

$db  = (new Database())->getConnection();
$adminGeneralDAO = new AdminGeneralDAO($db);
$usuarioDAO = new UsuarioDAO($db); // Instancia de UsuarioDAO
$adminTipo = "adminGeneral";

$accion = $_POST["accion"] ?? "";

// Verificar sesión para acciones protegidas (todas menos login)
if ($accion !== 'login') {
    if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'adminGeneral') {
        echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
        exit;
    }
}

switch ($accion) {
    case 'login':
        $usuario = $_POST['usuario']; 
        $password = $_POST['password'];

        $user = $adminGeneralDAO->login($usuario, $password);
        if ($user) {
            $_SESSION["user"] = [
                "id" => $user['idAdminGeneral'],
                "nome" => $user['nombre'],
                "tipo" => $adminTipo,
                "alerta" => true,
            ];
            echo json_encode(['status' => 'ok', "message" => "Bem vindo/a de novo Administrador!", "tipo" => $adminTipo]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas']);
        }
        break;

    case 'actualizarPassword':
        $idObjetivo = $_POST['idUsuario'] ?? null;
        $tipoObjetivo = $_POST['tipoUsuario'] ?? null; // Espera: 'cliente', 'associado' o 'adminGeneral'
        $nuevaPass = $_POST['nuevaPassword'] ?? '';

        if (!$idObjetivo || !$tipoObjetivo || empty($nuevaPass)) {
            echo json_encode(['status' => 'error', 'message' => 'Dados incompletos para atualizar a senha.']);
            exit;
        }

        $resultado = false;

        // Si el usuario a editar es Admin General (idTabla: admingeneral)
        if ($tipoObjetivo === 'adminGeneral') {
            $resultado = $adminGeneralDAO->actualizarPassword($idObjetivo, $nuevaPass);
        } 
        // Si el usuario es Cliente o Associado (idTabla: usuario)
        else {
            $resultado = $usuarioDAO->actualizarPassword($idObjetivo, $nuevaPass);
        }

        if ($resultado) {
            echo json_encode(['status' => 'ok', 'message' => 'Senha atualizada com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar a senha no banco de dados.']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}