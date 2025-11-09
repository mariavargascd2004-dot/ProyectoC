<?php
session_start();
require_once '../config/database.php';
require_once '../models/modelsDAO/UsuarioDAO.php';
require_once '../models/modelsDAO/EmprendimentoDAO.php';
require_once '../models/modelsDAO/AdminAssociadoDAO.php';
require_once '../models/modelsDAO/ImagemFabricacaoDAO.php';
require_once '../models/modelsDAO/ImagemGaleriaDAO.php';
require_once '../models/Emprendimento.php';
require_once '../models/ImagemFabricacao.php';
require_once '../models/ImagemGaleria.php';

$db = (new Database())->getConnection();
$emprendimentoDAO = new EmprendimentoDAO($db);
$adminAssociadoDAO = new AdminAssociadoDAO($db);
$imagemFabricacaoDAO = new ImagemFabricacaoDAO($db);
$imagemGaleriaDAO = new ImagemGaleriaDAO($db);

$accion = $_POST['accion'] ?? '';

$idUsuarioLogado = $_SESSION['user']['id'] ?? null;
$tipoUsuarioLogado = $_SESSION['user']['tipo'] ?? null;

switch ($accion) {
    case "buscar":

        $data = $emprendimentoDAO->listarTodosComAdmin();
        if ($data === null) {
            echo json_encode(["data" => null, "mensaje" => "No se encontraron registros"]);
        } else {
            echo json_encode(["data" => $data]);
        }
        break;
    case 'registrar':

        // Validar datos básicos
        $adminAssociado_idUsuario = $_POST['adminAssociado_idUsuario'] ?? '';
        $nome = $_POST['nome'] ?? '';
        $corPrincipal = $_POST['corPrincipal'] ?? '';
        $corSecundaria = $_POST['corSecundaria'] ?? '';
        $historia = $_POST['historia'] ?? '';
        $processoFabricacao = $_POST['processoFabricacao'] ?? '';
        $celular = $_POST['celular'] ?? '';
        $horarios = $_POST['horarios'] ?? '';
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
            $corPrincipal,
            $corSecundaria,
            $historia,
            $processoFabricacao,
            $celular,
            $horarios,
            $ubicacao
        );

        // Manejar logo si fue enviado
        if (isset($_FILES['logoEmprendimento']) && $_FILES['logoEmprendimento']['error'] === UPLOAD_ERR_OK) {
            $logo = guardarImagem($_FILES['logoEmprendimento'], 'logos');
            $emprendimento->setLogo($logo);
        }
        // Manejar pooster si fue enviado
        if (isset($_FILES['poosterEmprendimento']) && $_FILES['poosterEmprendimento']['error'] === UPLOAD_ERR_OK) {
            $pooster = guardarImagem($_FILES['poosterEmprendimento'], 'poosters');
            $emprendimento->setPooster($pooster);
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

                foreach ($imagensSalvas as $caminho) {
                    $proximaOrdem = $imagemFabricacaoDAO->obterProximaOrdemDisponivel($idEmprendimento);

                    $imagem = new ImagemFabricacao($idEmprendimento, $caminho, $proximaOrdem);
                    $imagemFabricacaoDAO->inserir($imagem);
                }
            }

            if (isset($_FILES['imagemsGaleria']) && is_array($_FILES['imagemsGaleria']['name'])) {
                $imagensSalvas = guardarMultiplasImagens($_FILES['imagemsGaleria'], 'galeria', 10);

                foreach ($imagensSalvas as $caminho) {
                    $proximaOrdem = $imagemGaleriaDAO->obterProximaOrdemDisponivel($idEmprendimento);

                    $imagem = new ImagemGaleria($idEmprendimento, $caminho, $proximaOrdem);
                    $imagemGaleriaDAO->inserir($imagem);
                }
            }

            echo json_encode(['status' => 'ok', 'message' => 'Empreendimento registrado com sucesso']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao registrar o empreendimento']);
        }
        break;
    case "aprovar":
        $idEmprendimento = $_POST['id'];
        $data = $emprendimentoDAO->atualizarAprovacao($idEmprendimento, true);

        if ($data) {
            echo json_encode(["status" => "ok", "message" => "Empreendedor ativado com sucesso!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Houve um problema ao processar a ativação."]);
        }
        break;
    case "actualizarHistoria":
        header('Content-Type: application/json');

        if (!$idUsuarioLogado || $tipoUsuarioLogado !== 'associado') {
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $idEmprendimento = $_POST['idEmprendimento'] ?? 0;
        $historia = $_POST['historia'] ?? '';

        if (empty($idEmprendimento) || $historia === '') {
            echo json_encode(['status' => 'error', 'message' => 'Dados incompletos. A história não pode estar vazia.']);
            exit;
        }

        try {
            $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);
            if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
                echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para editar este empreendimento.']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao verificar permissões.']);
            exit;
        }

        $success = $emprendimentoDAO->atualizarHistoria($idEmprendimento, $historia);

        if ($success) {
            echo json_encode(['status' => 'ok', 'message' => 'História atualizada com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar a história no banco de dados.']);
        }
        break;
    case "actualizarProcesso":
        header('Content-Type: application/json');

        if (!$idUsuarioLogado || $tipoUsuarioLogado !== 'associado') {
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $idEmprendimento = $_POST['idEmprendimento'] ?? 0;
        $processo = $_POST['processoFabricacao'] ?? '';

        if (empty($idEmprendimento) || $processo === '') {
            echo json_encode(['status' => 'error', 'message' => 'O processo de fabricação não pode estar vazio.']);
            exit;
        }

        try {
            $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);
            if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
                echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para editar este empreendimento.']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao verificar permissões.']);
            exit;
        }

        $success = $emprendimentoDAO->atualizarProcesso($idEmprendimento, $processo);

        if ($success) {
            echo json_encode(['status' => 'ok', 'message' => 'Processo de fabricação atualizado com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar o processo no banco de dados.']);
        }
        break;
    case "actualizarCores":
        header('Content-Type: application/json');

        if (!$idUsuarioLogado || $tipoUsuarioLogado !== 'associado') {
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $idEmprendimento = $_POST['idEmprendimento'] ?? 0;
        $corPrincipal = $_POST['corPrincipal'] ?? '';
        $corSecundaria = $_POST['corSecundaria'] ?? '';

        if (empty($idEmprendimento) || empty($corPrincipal) || empty($corSecundaria)) {
            echo json_encode(['status' => 'error', 'message' => 'Ambas as cores são obrigatórias.']);
            exit;
        }

        try {
            $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);
            if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
                echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para editar este empreendimento.']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao verificar permissões.']);
            exit;
        }

        $success = $emprendimentoDAO->atualizarCores($idEmprendimento, $corPrincipal, $corSecundaria);

        if ($success) {
            echo json_encode(['status' => 'ok', 'message' => 'Cores atualizadas com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar as cores no banco de dados.']);
        }
        break;
    case "actualizarInfoEmprendimento":
        header('Content-Type: application/json');

        if (!$idUsuarioLogado || $tipoUsuarioLogado !== 'associado') {
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $idEmprendimento = $_POST['idEmprendimento'] ?? 0;
        $dados = [
            'telefone' => $_POST['telefone'] ?? null,
            'celular' => $_POST['celular'] ?? '',
            'ubicacao' => $_POST['ubicacao'] ?? '',
            'horarios' => $_POST['horarios'] ?? '',
            'facebook' => $_POST['facebook'] ?? null,
            'instagram' => $_POST['instagram'] ?? null
        ];

        if (empty($idEmprendimento) || empty($dados['celular']) || empty($dados['ubicacao']) || empty($dados['horarios'])) {
            echo json_encode(['status' => 'error', 'message' => 'Campos obrigatórios (Celular, Endereço, Horários) não podem estar vazios.']);
            exit;
        }

        try {
            $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);
            if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
                echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para editar este empreendimento.']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao verificar permissões.']);
            exit;
        }

        // 5. Chamar o DAO
        $success = $emprendimentoDAO->atualizarInfoEmprendimento($idEmprendimento, $dados);

        if ($success) {
            echo json_encode(['status' => 'ok', 'message' => 'Informações atualizadas com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar as informações no banco de dados.']);
        }
        break;
    case "subirImagensFabricacao":
    case "subirImagensGaleria":
        header('Content-Type: application/json');

        if (!$idUsuarioLogado || $tipoUsuarioLogado !== 'associado') {
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $idEmprendimento = $_POST['idEmprendimento'] ?? 0;

        $tipo = ($accion === 'subirImagensFabricacao') ? 'fabricacao' : 'galeria';
        $limite = ($tipo === 'fabricacao') ? 4 : 10;
        $files = ($tipo === 'fabricacao') ? ($_FILES['novasImagensFabricacao'] ?? null) : ($_FILES['novasImagensGaleria'] ?? null);
        $imagemDAO = ($tipo === 'fabricacao') ? $imagemFabricacaoDAO : $imagemGaleriaDAO;

        if (empty($idEmprendimento) || !$files || $files['error'][0] === UPLOAD_ERR_NO_FILE) {
            echo json_encode(['status' => 'error', 'message' => 'Nenhuma imagem foi enviada.']);
            exit;
        }

        try {
            $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);
            if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
                echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para editar este empreendimento.']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao verificar permissões.']);
            exit;
        }

        $contagemAtual = $imagemDAO->contarPorEmprendimento($idEmprendimento);
        $arquivosEnviados = count($files['name']);
        $vagasRestantes = $limite - $contagemAtual;

        if ($arquivosEnviados > $vagasRestantes) {
            echo json_encode(['status' => 'error', 'message' => "Limite de $limite imagens excedido. Você pode enviar apenas mais $vagasRestantes imagens."]);
            exit;
        }

        $imagensSalvas = guardarMultiplasImagens($files, $tipo, $vagasRestantes);

        if (empty($imagensSalvas)) {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao salvar as imagens no servidor.']);
            exit;
        }

        $novasImagens = [];
        foreach ($imagensSalvas as $caminho) {
            $proximaOrdem = $imagemDAO->obterProximaOrdemDisponivel($idEmprendimento);

            $novaImagem = null;
            if ($tipo === 'fabricacao') {
                $novaImagem = new ImagemFabricacao($idEmprendimento, $caminho, $proximaOrdem);
            } else {
                $novaImagem = new ImagemGaleria($idEmprendimento, $caminho, $proximaOrdem);
            }

            $novoId = $imagemDAO->inserir($novaImagem);

            if ($novoId) {
                $novasImagens[] = ['id' => $novoId, 'caminho' => $caminho, 'ordem' => $proximaOrdem];
            }
        }

        echo json_encode([
            'status' => 'ok',
            'message' => 'Imagens enviadas com sucesso!',
            'novasImagens' => $novasImagens
        ]);
        break;
    case "eliminarImagem":
        header('Content-Type: application/json');

        if (!$idUsuarioLogado || $tipoUsuarioLogado !== 'associado') {
            echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
            exit;
        }

        $idImagem = $_POST['id'] ?? 0;
        $tipoImagem = $_POST['tipo'] ?? '';

        if (empty($idImagem) || empty($tipoImagem)) {
            echo json_encode(['status' => 'error', 'message' => 'Dados incompletos.']);
            exit;
        }

        $imagemDAO = null;
        $imagem = null;

        try {
            // Seleciona o DAO correto
            if ($tipoImagem === 'fabricacao') {
                $imagemDAO = $imagemFabricacaoDAO;
                $imagem = $imagemDAO->obterPorId($idImagem);
            } elseif ($tipoImagem === 'galeria') {
                $imagemDAO = $imagemGaleriaDAO;
                $imagem = $imagemDAO->obterPorId($idImagem);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Tipo de imagem inválido.']);
                exit;
            }

            if (!$imagem) {
                echo json_encode(['status' => 'error', 'message' => 'Imagem não encontrada.']);
                exit;
            }

            $idEmprendimento = $imagem['emprendimento_id'];
            $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);

            if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
                echo json_encode(['status' => 'error', 'message' => 'Você não tem permissão para eliminar esta imagem.']);
                exit;
            }

            $caminhoArquivo = "../" . $imagem['caminho_imagem'];
            if (file_exists($caminhoArquivo)) {
                unlink($caminhoArquivo);
            }

            $success = $imagemDAO->excluirPorId($idImagem);

            if ($success) {
                echo json_encode(['status' => 'ok', 'message' => 'Imagem eliminada com sucesso!']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao eliminar a imagem do banco de dados.']);
            }
        } catch (Exception $e) {
            error_log("Erro ao eliminar imagem: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => 'Ocorreu um erro no servidor.']);
        }
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'accion no valida']);
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
