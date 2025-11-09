<?php
session_start();
require_once '../config/database.php';
require_once '../models/modelsDAO/UsuarioDAO.php';
require_once '../models/modelsDAO/EmprendimentoDAO.php';
require_once '../models/modelsDAO/AdminAssociadoDAO.php';
require_once '../models/modelsDAO/ImagemFabricacaoDAO.php';
require_once '../models/modelsDAO/ImagemGaleriaDAO.php';
require_once '../models/Usuario.php';

$db = (new Database())->getConnection();
$usuarioDAO = new UsuarioDAO($db);
$adminAssociadoDAO = new AdminAssociadoDAO($db);
$emprendimentoDAO = new EmprendimentoDAO($db);
$imagemFabricacaoDAO = new ImagemFabricacaoDAO($db);
$imagemGaleriaDAO = new ImagemGaleriaDAO($db);
$accion = $_POST['accion'] ?? '';

$idUsuarioLogado = $_SESSION['user']['id'] ?? null;
$tipoUsuarioLogado = $_SESSION['user']['tipo'] ?? null;

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

            //verificar si es Associado y si lo es verificar que este activado
            if ($user["tipo"] == "associado") {
                $emprendimiento = $emprendimentoDAO->obterPorIdAssociado($user['idUsuario']);
                if (!$emprendimiento) {
                    echo json_encode(['status' => 'error', "message" => "Houve um erro ao encontrar o Emprendimento, por favor tente novamente."]);
                    exit;
                }

                //se nao esta ativo nao permitir o login
                if ($emprendimiento['aprovado'] == 0) {
                    echo json_encode(['status' => 'warning', "message" => "Sua conta ainda não foi ativada pelo administrador. Aguarde a aprovação para poder acessar o sistema."]);
                    exit;
                }
            }
            $_SESSION['user'] = [
                "id" => $user['idUsuario'],
                "tipo" => $user['tipo'],
                "nome" => $user['nombre'],
                "alerta" => true

            ];

            //login com sucesso
            if ($user["tipo"] == "associado") {
                echo json_encode([
                    'status' => 'ok',
                    "message" => "Bem-vindo(a) de volta! Seu painel de empreendedor está pronto para você continuar gerenciando seu negócio.",
                    "idEmprendimento" => $emprendimiento['idEmprendimento'],
                    "tipo" => $user['tipo']
                ]);
            } else {
                echo json_encode([
                    'status' => 'ok',
                    "message" => "Bem-vindo(a) novamente! Que bom ter você por aqui — aproveite nossas novidades e ofertas.",
                    "tipo" => $user['tipo']
                ]);
            }
        } else {
            echo json_encode(['status' => 'incorrect_Credencial', 'message' => 'Credenciales incorrectas']);
        }
        break;
    case 'AdmineliminarAssociado':
        $id = $_POST['id'];

        $user = $usuarioDAO->eliminarUsuario($id);

        if ($user) {
            echo json_encode([
                'status' => 'ok',
                'message' => 'O empreendedor e todos os seus registros foram excluídos com sucesso.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Não foi possível eliminar o registro.'
            ]);
        }
        break;
    case 'eliminarConta':
        header('Content-Type: application/json');

        $idUsuarioForm = $_POST['idUsuario'] ?? 0;

        if (!$idUsuarioLogado || !$tipoUsuarioLogado) {
            echo json_encode(['status' => 'error', 'message' => 'Sessão expirada ou inválida. Faça login novamente.']);
            exit;
        }

        $isSelfDelete = ($idUsuarioLogado == $idUsuarioForm);
        $isAdminDelete = (!$isSelfDelete && $tipoUsuarioLogado === 'adminGeneral');

        if (!$isSelfDelete && !$isAdminDelete) {
            echo json_encode(['status' => 'error', 'message' => 'Erro de permissão. Você não pode eliminar esta conta.']);
            exit;
        }


        try {

            $idUsuarioParaEliminar = $idUsuarioForm;
            $tipoUsuarioParaEliminar = null;

            if ($isSelfDelete) {
                $tipoUsuarioParaEliminar = $tipoUsuarioLogado;
            } else if ($isAdminDelete) {
                $tipoUsuarioParaEliminar = $usuarioDAO->obterTipoPorId($idUsuarioParaEliminar);
                if (!$tipoUsuarioParaEliminar) {
                    echo json_encode(['status' => 'error', 'message' => 'Não foi possível encontrar o usuário-alvo para eliminar.']);
                    exit;
                }
            }

            if ($tipoUsuarioParaEliminar === 'associado') {

                $adminInfo = $adminAssociadoDAO->obtenerPorId($idUsuarioParaEliminar);
                $fotoPerfil = ($adminInfo) ? ($adminInfo['FotoPerfilAssociado'] ?? null) : null;

                $emprendimento = $emprendimentoDAO->obterPorIdAssociado($idUsuarioParaEliminar);
                $logo = null;
                $pooster = null;
                $imgsFabricacao = [];
                $imgsGaleria = [];

                if ($emprendimento) {
                    $idEmprendimento = $emprendimento['idEmprendimento'];
                    $logo = $emprendimento['logo'] ?? null;
                    $pooster = $emprendimento['pooster'] ?? null;

                    $listaFabricacao = $imagemFabricacaoDAO->listarPorEmprendimento($idEmprendimento);
                    if ($listaFabricacao) {
                        $imgsFabricacao = array_column($listaFabricacao, 'caminho_imagem');
                    }

                    $listaGaleria = $imagemGaleriaDAO->listarPorEmprendimento($idEmprendimento);
                    if ($listaGaleria) {
                        $imgsGaleria = array_column($listaGaleria, 'caminho_imagem');
                    }
                }

                // 2. EXCLUIR ARQUIVOS DO SERVIDOR
                excluirArquivoServidor($fotoPerfil);
                excluirArquivoServidor($logo);
                excluirArquivoServidor($pooster);

                foreach ($imgsFabricacao as $caminho) {
                    excluirArquivoServidor($caminho);
                }
                foreach ($imgsGaleria as $caminho) {
                    excluirArquivoServidor($caminho);
                }
            }

            $success = $usuarioDAO->eliminarUsuario($idUsuarioParaEliminar);

            if ($success) {
                $message = '';
                if ($isSelfDelete) {
                    session_unset();
                    session_destroy();
                    $message = 'Sua conta e todos os dados associados foram eliminados com sucesso.';
                } else {
                    $message = 'A conta do usuário foi eliminada com sucesso.';
                }
                echo json_encode(['status' => 'ok', 'message' => $message]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro ao tentar eliminar a conta no banco de dados.']);
            }
        } catch (Exception $e) {
            error_log("Erro ao eliminar conta (Alvo: $idUsuarioParaEliminar, Admin: $idUsuarioLogado): " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro no servidor durante a eliminação.']);
        }

        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'A ação não é valida']);
}

function excluirArquivoServidor($caminhoRelativo)
{
    if (empty($caminhoRelativo)) {
        return; 
    }

    if (strpos($caminhoRelativo, 'default.png') !== false || strpos($caminhoRelativo, 'default.jpg') !== false) {
        return;
    }

    $caminhoCompleto = "../" . $caminhoRelativo;

    if (file_exists($caminhoCompleto) && is_file($caminhoCompleto)) {
        @unlink($caminhoCompleto);
    }
    else if(file_exists($caminhoRelativo) && is_file($caminhoRelativo)) {
        @unlink($caminhoRelativo);
    }
}
?>