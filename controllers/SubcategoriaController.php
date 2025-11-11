<?php
session_start();
require_once "../config/database.php";
require_once "../models/modelsDAO/SubCateogriaDAO.php";
require_once "../models/modelsDAO/CategoriaDAO.php";
require_once "../models/modelsDAO/ProdutoDAO.php";
require_once "../models/modelsDAO/EmprendimentoDAO.php";

header('Content-Type: application/json');

$db  = (new Database())->getConnection();
$subcategoriaDAO = new SubCateogriaDAO($db);
$categoriaDAO = new CategoriaDAO($db);
$produtoDAO = new ProdutoDAO($db);
$emprendimentoDAO = new EmprendimentoDAO($db);

$idUsuarioLogado = $_SESSION["user"]['id'] ?? null;
if (!$idUsuarioLogado) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

$accion = $_POST["accion"] ?? "";

function verificarPermissaoSubcat($categoriaDAO, $emprendimentoDAO, $idUsuarioLogado, $idCategoria) {
    $categoria = $categoriaDAO->obterPorId($idCategoria);
    if (!$categoria) return false;
    
    $emprendimento = $emprendimentoDAO->obterPorId($categoria['emprendimiento_id']);
    if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
        return false;
    }
    return true;
}

switch ($accion) {
    case "subcategoriasConIdCategoria":
        $idCategoria = $_POST["idCategoria"];
        $subcategorias = $subcategoriaDAO->trearSubCategorias($idCategoria);

        if ($subcategorias && count($subcategorias) > 0) {
            echo json_encode(["status" => "ok", "subcategorias" => $subcategorias]);
        } else {
            echo json_encode(["status" => "vazio", "message" => "Nenhuma subcategoria encontrada."]);
        }
        break;

    case "criar":
        $nome = $_POST['nome'] ?? '';
        $idCategoria = $_POST['idCategoria'] ?? 0;

        if (empty($nome) || empty($idCategoria)) {
            echo json_encode(['status' => 'error', 'message' => 'Nome e ID da Categoria são obrigatórios.']);
            exit;
        }
        if (!verificarPermissaoSubcat($categoriaDAO, $emprendimentoDAO, $idUsuarioLogado, $idCategoria)) {
            echo json_encode(['status' => 'error', 'message' => 'Permissão negada.']);
            exit;
        }

        $novoId = $subcategoriaDAO->inserir($nome, $idCategoria);
        if ($novoId) {
            $novaSubcat = $subcategoriaDAO->obterPorId($novoId);
            echo json_encode(['status' => 'ok', 'message' => 'Subcategoria criada com sucesso!', 'subcategoria' => $novaSubcat]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao criar subcategoria.']);
        }
        break;

    case "atualizar":
        $nome = $_POST['nome'] ?? '';
        $idSubcategoria = $_POST['idSubcategoria'] ?? 0;

        $subcat = $subcategoriaDAO->obterPorId($idSubcategoria);
        if (!$subcat || !verificarPermissaoSubcat($categoriaDAO, $emprendimentoDAO, $idUsuarioLogado, $subcat['categoria_id'])) {
             echo json_encode(['status' => 'error', 'message' => 'Permissão negada.']);
             exit;
        }

        if ($subcategoriaDAO->atualizar($idSubcategoria, $nome)) {
            echo json_encode(['status' => 'ok', 'message' => 'Subcategoria atualizada com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar subcategoria.']);
        }
        break;

    case "excluir":
        $idSubcategoria = $_POST['idSubcategoria'] ?? 0;
        
        $subcat = $subcategoriaDAO->obterPorId($idSubcategoria);
        if (!$subcat || !verificarPermissaoSubcat($categoriaDAO, $emprendimentoDAO, $idUsuarioLogado, $subcat['categoria_id'])) {
             echo json_encode(['status' => 'error', 'message' => 'Permissão negada.']);
             exit;
        }

        $produtosCount = $produtoDAO->contarPorSubcategoria($idSubcategoria);
        if ($produtosCount > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Não é possível excluir. Esta subcategoria está sendo usada por ' . $produtosCount . ' produtos.']);
            exit;
        }

        if ($subcategoriaDAO->excluir($idSubcategoria)) {
            echo json_encode(['status' => 'ok', 'message' => 'Subcategoria excluída com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir subcategoria.']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}