<?php
session_start();
require_once '../config/database.php';
require_once '../models/UsuarioDAO.php';
require_once '../models/EmprendimentoDAO.php';
require_once '../models/Emprendimento.php';
require_once '../models/AdminAssociadoDAO.php';
require_once '../models/ImagemFabricacaoDAO.php';
require_once '../models/ImagemGaleriaDAO.php';
require_once '../models/ImagemFabricacao.php';
require_once '../models/ImagemGaleria.php';

$db = (new Database())->getConnection();
$emprendimentoDAO = new EmprendimentoDAO($db);
$adminAssociadoDAO = new AdminAssociadoDAO($db);
$imagemFabricacaoDAO = new ImagemFabricacaoDAO($db);
$imagemGaleriaDAO = new ImagemGaleriaDAO($db);

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'registrar':

        // Validar datos básicos
        $adminAssociado_idUsuario = $_POST['adminAssociado_idUsuario'] ?? '';
        $nome = $_POST['nome'] ?? '';
        $historia = $_POST['historia'] ?? '';
        $processoFabricacao = $_POST['processoFabricacao'] ?? '';
        $celular = $_POST['celular'] ?? '';
        $ubicacao = $_POST['ubicacao'] ?? '';

        if (empty($nome) || empty($historia) || empty($processoFabricacao) || empty($celular) || empty($ubicacao)) {
            echo json_encode(['status' => 'error', 'message' => 'Todos los campos obligatorios deben ser completados']);
            exit;
        }

        // Verificar si ya existe un emprendimiento con este nombre
        if ($emprendimentoDAO->existeNome($nome)) {
            echo json_encode(['status' => 'error', 'message' => 'Já existe um empreendimento com este nome']);
            exit;
        }

        // Crear el emprendimiento
        $emprendimento = new Emprendimento(
            $adminAssociado_idUsuario,
            $nome,
            $historia,
            $processoFabricacao,
            $celular,
            $ubicacao
        );

        // Manejar logo si fue enviado
        if (isset($_FILES['logoEmprendimento']) && $_FILES['logoEmprendimento']['error'] === UPLOAD_ERR_OK) {
            $logo = guardarImagem($_FILES['logoEmprendimento'], 'logos');
            $emprendimento->setLogo($logo);
        }

        // Setear campos opcionales
        $telefone = $_POST['telefone'] ?? null;
        $instagram = $_POST['instagram'] ?? null;
        $facebook = $_POST['facebook'] ?? null;

        if ($telefone) $emprendimento->setTelefone($telefone);
        if ($instagram) $emprendimento->setInstagram($instagram);
        if ($facebook) $emprendimento->setFacebook($facebook);

        // Registrar emprendimiento
        $idEmprendimento = $emprendimentoDAO->registrar($emprendimento);

        if ($idEmprendimento) {
            if (isset($_FILES['imagemsFabricacao']) && is_array($_FILES['imagemsFabricacao']['name'])) {
                $imagensSalvas = guardarMultiplasImagens($_FILES['imagemsFabricacao'], 'fabricacao', 4);

                foreach ($imagensSalvas as $ordem => $caminho) {
                    $imagem = new ImagemFabricacao($idEmprendimento, $caminho, $ordem);
                    $imagemFabricacaoDAO->inserir($imagem);
                }
            }

            if (isset($_FILES['imagemsGaleria']) && is_array($_FILES['imagemsGaleria']['name'])) {
                $imagensSalvas = guardarMultiplasImagens($_FILES['imagemsGaleria'], 'galeria', 10);

                foreach ($imagensSalvas as $ordem => $caminho) {
                    $imagem = new ImagemGaleria($idEmprendimento, $caminho, $ordem);
                    $imagemGaleriaDAO->inserir($imagem);
                }
            }

            echo json_encode(['status' => 'ok', 'message' => 'Empreendimento registrado com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao registrar o empreendimento']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Ação não válida']);
}

function guardarImagem($arquivo, $pasta)
{
    $diretorio = "../assets/img/$pasta/";
    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
    $nomeArquivo = uniqid() . '.' . $extensao;
    $rotaCompleta = $diretorio . $nomeArquivo;

    if (move_uploaded_file($arquivo['tmp_name'], $rotaCompleta)) {
        return "assets/img/$pasta/" . $nomeArquivo;
    }
    return null;
}

function guardarMultiplasImagens($arquivos, $pasta, $limite = null)
{
    $imagensSalvas = [];
    $diretorio = "../assets/img/$pasta/";

    if (!is_dir($diretorio)) {
        mkdir($diretorio, 0777, true);
    }

    $totalArquivos = count($arquivos['name']);

    // Aplicar límite si existe
    if ($limite && $totalArquivos > $limite) {
        $totalArquivos = $limite;
    }

    for ($i = 0; $i < $totalArquivos; $i++) {
        if ($arquivos['name'][$i] && $arquivos['error'][$i] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($arquivos['name'][$i], PATHINFO_EXTENSION);
            $nomeArquivo = uniqid() . '_' . $i . '.' . $extensao;
            $rotaCompleta = $diretorio . $nomeArquivo;

            if (move_uploaded_file($arquivos['tmp_name'][$i], $rotaCompleta)) {
                $imagensSalvas[] = "assets/img/$pasta/" . $nomeArquivo;
            } else {
                error_log("❌ Error moviendo archivo: " . $arquivos['name'][$i]);
            }
        }
    }

    return $imagensSalvas;
}
