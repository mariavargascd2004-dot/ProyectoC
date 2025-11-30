<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once "../config/database.php";
require_once "../models/Produto.php";
require_once "../models/modelsDAO/ProdutoDAO.php";
require_once "../models/modelsDAO/ImagemProdutoDAO.php";

$db  = (new Database())->getConnection();
$produtoDAO = new ProdutoDAO($db);
$imagemDAO = new ImagemProdutoDAO($db);

$accion = $_POST["accion"] ?? "";

switch ($accion) {
    case "GuardarProduto":
        $titulo = $_POST["Titulo"] ?? '';
        $descripcion = $_POST["Descripcion"] ?? '';
        $precio = $_POST["Precio"] ?? 0;
        $color = $_POST["Color"] ?? '';
        $tamanho = $_POST["Tamanho"] ?? '';
        $idCategoria = $_POST["Categoria"] ?? '';
        $idSubcategoria = $_POST["Subcategoria"] ?? '';
        $idEmprendimiento = $_POST["idEmprendimiento"] ?? null;


        $produtoId = $produtoDAO->guardarProduto([
            "titulo" => $titulo,
            "descripcion" => $descripcion,
            "precio" => $precio,
            "color" => $color,
            "tamano" => $tamanho,
            "producto_idCategoria" => $idCategoria,
            "producto_idSubcategoria" => $idSubcategoria,
            "emprendimiento_id" => $idEmprendimiento
        ]);

        if (!$produtoId) {
            echo json_encode(["status" => "error", "message" => "Erro ao cadastrar o produto."]);
            exit;
        }

        if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {

            $uploadDir = "../assets/img/produtos/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $files = $_FILES["images"];
            $totalFiles = count($files["name"]);

            for ($i = 0; $i < $totalFiles; $i++) {

                if ($files["error"][$i] === UPLOAD_ERR_OK) {

                    $tmpName = $files["tmp_name"][$i];
                    $originalName = $files["name"][$i];

                    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                    $newName = uniqid("produto_") . "." . $ext;
                    $fullPath = $uploadDir . $newName;

                    if (move_uploaded_file($tmpName, $fullPath)) {
                        $caminhoImagem = "../assets/img/produtos/" . $newName;

                        $ordem = $imagemDAO->obterProximaOrdemDisponivel($produtoId);

                        $imagem = new ImagemProduto($produtoId, $caminhoImagem, $ordem);
                        $imagemDAO->inserir($imagem);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Erro ao mover a imagem: " . htmlspecialchars($originalName)]);
                        exit;
                    }
                } else if ($files["error"][$i] !== UPLOAD_ERR_NO_FILE) {
                    echo json_encode(["status" => "error", "message" => "Erro no upload da imagem: " . htmlspecialchars($originalName)]);
                    exit;
                }
            }
        }

        echo json_encode([
            "status" => "ok",
            "message" => "Produto cadastrado com sucesso!"
        ]);

        break;
    case "obtenerProducto":
        $idProducto = $_POST["idProducto"] ?? 0;
        if (!$idProducto) {
            echo json_encode(["status" => "error", "message" => "ID de produto não fornecido."]);
            exit;
        }

        $producto = $produtoDAO->obtenerProductoPorId($idProducto);

        if (!$producto) {
            echo json_encode(["status" => "error", "message" => "Produto não encontrado."]);
            exit;
        }

        $imagenes = $imagemDAO->listarPorProduto($idProducto);
        $producto['imagenes'] = $imagenes;

        echo json_encode(["status" => "ok", "producto" => $producto]);
        break;

    case "actualizarProducto":
        $idProducto = $_POST["idProducto"] ?? 0;

        if (!$idProducto) {
            echo json_encode(["status" => "error", "message" => "ID do produto inválido."]);
            exit;
        }

        $dados = [
            "titulo" => $_POST["Titulo"] ?? '',
            "descripcion" => $_POST["Descripcion"] ?? '',
            "precio" => $_POST["Precio"] ?? 0,
            "color" => $_POST["Color"] ?? '',
            "tamano" => $_POST["Tamanho"] ?? '',
            "producto_idCategoria" => $_POST["Categoria"] ?? '',
            "producto_idSubcategoria" => $_POST["Subcategoria"] ?? ''
        ];

        $actualizado = $produtoDAO->actualizarProducto($idProducto, $dados);

        if (!$actualizado) {
            echo json_encode(["status" => "error", "message" => "Erro ao atualizar os dados do produto."]);
            exit;
        }

        if (isset($_POST['imagenesAEliminar']) && is_array($_POST['imagenesAEliminar'])) {
            foreach ($_POST['imagenesAEliminar'] as $idImagen) {
                $idImagenInt = intval($idImagen);

                $imagem = $imagemDAO->obterPorId($idImagenInt);

                if ($imagem) {

                    $dbPath = $imagem['caminho_imagem'];
                    $serverPath = str_replace("../", "", $dbPath);

                    $fullServerPath = "../" . $serverPath;

                    if (file_exists($fullServerPath)) {
                        unlink($fullServerPath);
                    }

                    $imagemDAO->excluirPorId($idImagenInt);
                }
            }
        }

        //Agregar nuevas imagenes
        if (isset($_FILES["images"]) && !empty($_FILES["images"]["name"][0])) {
            $uploadDir = "../assets/img/produtos/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $files = $_FILES["images"];
            $totalFiles = count($files["name"]);

            for ($i = 0; $i < $totalFiles; $i++) {
                if ($files["error"][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $files["tmp_name"][$i];
                    $originalName = $files["name"][$i];
                    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
                    $newName = uniqid("produto_") . "." . $ext;
                    $fullPath = $uploadDir . $newName;

                    if (move_uploaded_file($tmpName, $fullPath)) {
                        $caminhoImagem = "../assets/img/produtos/" . $newName;
                        $ordem = $imagemDAO->obterProximaOrdemDisponivel($idProducto);
                        $imagem = new ImagemProduto($idProducto, $caminhoImagem, $ordem);
                        $imagemDAO->inserir($imagem);
                    } else {
                        echo json_encode(["status" => "error", "message" => "Erro ao mover a nova imagem: " . htmlspecialchars($originalName)]);
                        exit;
                    }
                } else if ($files["error"][$i] !== UPLOAD_ERR_NO_FILE) {
                    echo json_encode(["status" => "error", "message" => "Erro no upload da nova imagem: " . htmlspecialchars($originalName)]);
                    exit;
                }
            }
        }

        $productoActualizado = $produtoDAO->obtenerProductoPorId($idProducto);
        $imagenes = $imagemDAO->listarPorProduto($idProducto);

        $productoActualizado['imagen_principal'] = !empty($imagenes) ? $imagenes[0]['caminho_imagem'] : '../assets/img/produtos/default.png';

        echo json_encode([
            "status" => "ok",
            "message" => "Produto atualizado com sucesso!",
            "producto" => $productoActualizado
        ]);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida']);
}
