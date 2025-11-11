<?php
session_start();
require_once "../config/database.php";
require_once "../models/modelsDAO/CategoriaDAO.php";
require_once "../models/modelsDAO/SubCateogriaDAO.php";
require_once "../models/modelsDAO/ProdutoDAO.php";
require_once "../models/modelsDAO/EmprendimentoDAO.php";

header('Content-Type: application/json');

$db  = (new Database())->getConnection();
$categoriaDAO = new CategoriaDAO($db);
$subcategoriaDAO = new SubCateogriaDAO($db);
$produtoDAO = new ProdutoDAO($db);
$emprendimentoDAO = new EmprendimentoDAO($db);

$idUsuarioLogado = $_SESSION["user"]['id'] ?? null;
if (!$idUsuarioLogado) {
    echo json_encode(['status' => 'error', 'message' => 'Acesso não autorizado.']);
    exit;
}

$accion = $_POST["accion"] ?? "";

switch ($accion) {
    case "listarTudo":
        $idEmprendimento = $_POST['idEmprendimento'] ?? 0;

        $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);
        if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
            echo json_encode(['status' => 'error', 'message' => 'Permissão negada.']);
            exit;
        }

        $categorias = $categoriaDAO->trearCategorias($idEmprendimento);
        $resultado = [];

        foreach ($categorias as $cat) {
            $cat['subcategorias'] = $subcategoriaDAO->trearSubCategorias($cat['idCategoria']);
            $resultado[] = $cat;
        }

        echo json_encode(['status' => 'ok', 'categorias' => $resultado]);
        break;

    case "criar":
        $nome = $_POST['nome'] ?? '';
        $idEmprendimento = $_POST['idEmprendimento'] ?? 0;

        if (empty($nome) || empty($idEmprendimento)) {
            echo json_encode(['status' => 'error', 'message' => 'Nome e ID do Empreendimento são obrigatórios.']);
            exit;
        }

        $emprendimento = $emprendimentoDAO->obterPorId($idEmprendimento);
        if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
            echo json_encode(['status' => 'error', 'message' => 'Permissão negada.']);
            exit;
        }

        $novoId = $categoriaDAO->inserir($nome, $idEmprendimento);
        if ($novoId) {
            $novaCategoria = $categoriaDAO->obterPorId($novoId);
            $novaCategoria['subcategorias'] = [];
            echo json_encode(['status' => 'ok', 'message' => 'Categoria criada com sucesso!', 'categoria' => $novaCategoria]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao criar categoria.']);
        }
        break;

    case "atualizar":
        $nome = $_POST['nome'] ?? '';
        $idCategoria = $_POST['idCategoria'] ?? 0;

        if (empty($nome) || empty($idCategoria)) {
            echo json_encode(['status' => 'error', 'message' => 'Nome e ID da Categoria são obrigatórios.']);
            exit;
        }

        $categoria = $categoriaDAO->obterPorId($idCategoria);
        if (!$categoria) {
            echo json_encode(['status' => 'error', 'message' => 'Categoria não encontrada.']);
            exit;
        }

        $emprendimento = $emprendimentoDAO->obterPorId($categoria['emprendimiento_id']);

        if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
            echo json_encode(['status' => 'error', 'message' => 'Permissão negada.']);
            exit;
        }

        if ($categoriaDAO->atualizar($idCategoria, $nome)) {
            echo json_encode(['status' => 'ok', 'message' => 'Categoria atualizada com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao atualizar categoria.']);
        }
        break;

    case "excluir":
        $idCategoria = $_POST['idCategoria'] ?? 0;

        if (empty($idCategoria)) {
            echo json_encode(['status' => 'error', 'message' => 'ID da Categoria é obrigatório.']);
            exit;
        }

        $categoria = $categoriaDAO->obterPorId($idCategoria);
        if (!$categoria) {
            echo json_encode(['status' => 'error', 'message' => 'Categoria não encontrada.']);
            exit;
        }

        $emprendimento = $emprendimentoDAO->obterPorId($categoria['emprendimiento_id']);
        if (!$emprendimento || $emprendimento['adminAssociado_idUsuario'] != $idUsuarioLogado) {
            echo json_encode(['status' => 'error', 'message' => 'Permissão negada.']);
            exit;
        }

        $subcategoriasCount = $subcategoriaDAO->contarPorCategoria($idCategoria);
        if ($subcategoriasCount > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Não é possível excluir. Esta categoria possui ' . $subcategoriasCount . ' subcategorias. Exclua-as primeiro.']);
            exit;
        }

        $produtosCount = $produtoDAO->contarPorCategoria($idCategoria);
        if ($produtosCount > 0) {
            echo json_encode(['status' => 'error', 'message' => 'Não é possível excluir. Esta categoria está sendo usada por ' . $produtosCount . ' produtos.']);
            exit;
        }

        if ($categoriaDAO->excluir($idCategoria)) {
            echo json_encode(['status' => 'ok', 'message' => 'Categoria excluída com sucesso!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir categoria.']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
