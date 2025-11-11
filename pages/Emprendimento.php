<?php
session_start();

// Inicializar variables de sesión de forma segura
$idUsuario = $_SESSION["user"]['id'] ?? null;
$tipoUsuario = $_SESSION["user"]['tipo'] ?? null;
$nomeUsuairo = $_SESSION["user"]['nome'] ?? null;

// 1. Validación robusta del Token de entrada
if (!isset($_GET["token"]) || empty($_GET["token"])) {
    header("location:../");
    exit;
}

$idEmprendimientoDecoded = base64_decode($_GET['token'], true);

// Verificar si la decodificación falló o no es un número válido
if ($idEmprendimientoDecoded === false || !filter_var($idEmprendimientoDecoded, FILTER_VALIDATE_INT)) {
    header("location:../");
    exit;
}

$idEmprendimiento = intval($idEmprendimientoDecoded);

// Traer informaciones necesarias de la bdd
require_once '../helpers/emprendimientosHelper.php';
require_once '../helpers/adminsAssociadoHelper.php';
require_once '../helpers/imagemFabricacaoHelper.php';
require_once '../helpers/imagemGaleriaHelper.php';
require_once '../helpers/produtoHelper.php';
require_once '../helpers/imagemProdutoHelper.php';
require_once '../helpers/categoriaHelper.php';
require_once '../helpers/subcategoriaHelper.php';

try {
    $empHelper = new EmprendimientosHelper();
    $assHelper = new adminsAssociadoHelper();
    $FabricacaoHelper = new imagemFabricacaoHelper();
    $GaleriaHelper = new imagemGaleriaHelper();
    $produtoHelper = new produtoHelper();
    $produtoImagemHelper = new imagemProdutoHelper();
    $categoriaHelper = new categoriaHelper();
    $subcategoriaHelper = new subcategoriaHelper();

    $emprendimiento = $empHelper->obtenerEmprendimientoPorId($idEmprendimiento);

    if (!$emprendimiento) {
        header("location:../");
        exit;
    }

    $idAssociadoDelEmprendimiento = $emprendimiento['adminAssociado_idUsuario'];
    $infoAssociado = $assHelper->obtenerAssociadoPorId($idAssociadoDelEmprendimiento);

    if (!$infoAssociado) {
        header("location:../");
        exit;
    }

    $productos = $produtoHelper->obtenerProductosDelEmprendimiento($idEmprendimiento);

    //obtener solo la primera imagen del producto (solo es necesario mostrar una)
    if ($productos && count($productos) > 0) {
        foreach ($productos as &$producto) {
            $imagenes = $produtoImagemHelper->obterImagemsComIdProduto($producto['idProducto']);

            if ($imagenes && count($imagenes) > 0) {
                $producto['imagen_principal'] = $imagenes[0]['caminho_imagem']; //acá agregue la imagen como un elemento más en el array de cada producto
            }
        }
        unset($producto);
    }

    $imgemsFabricacao = $FabricacaoHelper->obterImagemsComIdEmprendimento($idEmprendimiento);
    $imgemsGaleria  = $GaleriaHelper->obterImagemsComIdEmprendimento($idEmprendimiento);
    $categorias = $categoriaHelper->obtenerCategoriasDelEmprendimiento($idEmprendimiento);
    //las subCategorias las obtengo más abajo recorriendo el array de categorias

} catch (Exception $e) {
    error_log("Error en menuEmprendimiento.php: " . $e->getMessage());
    header("location:../");
    exit;
}

function h($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

?>
<!doctype html>
<html lang="br">

<head>
    <title><?php echo h($emprendimiento['nome']); ?></title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <!-- FontaWesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- SweetAlrt2 CSS CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.min.css">


    <!-- Agregar Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Fonte Alegreya SC -->
    <link
        href="https://fonts.googleapis.com/css2?family=Alegreya+SC:ital,wght@0,400;0,500;0,700;0,800;0,900;1,400;1,500;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <!-- Fonte Works Sans -->
    <link
        href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Fonte Lora -->
    <link
        href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&display=swap"
        rel="stylesheet">

    <!-- My CSS -->
    <link rel="stylesheet" href="../assets/css/stylePrincipal.css">
    <link rel="stylesheet" href="../assets/css/styleMenuEmprendedores.css">
</head>

<body>

    <style>
        :root {
            /*Cores*/
            --cor-primaria: <?php echo h($emprendimiento['corPrincipal']) ?>;
            --cor-secundaria: <?php echo h($emprendimiento['corSecundaria']) ?>;
        }
    </style>

    <header>
        <nav id="navBar" class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <!-- Logo y enlaces -->
                <div class="navbar-nav d-flex align-items-center">
                    <a href="../">
                        <img src="../assets/img/CasaSolidaria/defaultLogo.png" alt="Logo Casa Solidaria" width="80px">
                    </a>
                    <img src="../<?php echo h($emprendimiento['logo']); ?>" alt="Logo de <?php echo h($emprendimiento['nome']); ?>" width="75">
                    <a class="nav-item nav-link titulo nav__titulo" href="#"><?php echo h($emprendimiento['nome']); ?></a>
                    <a class="nav-item nav-link" href="#Produtos">Produtos <i class="fa-solid fa-arrow-down"></i></a>
                </div>
                <!-- Botones alineados a la derecha -->
                <!-- Sin login -->
                <?php if ($idUsuario == null) { ?>
                    <div class="d-flex ms-auto gap-2">
                        <a href="Registro.html" class="btn btn--cinza">Registrarse <i
                                class="fa-solid fa-user-plus"></i></a href="#">
                        <a href="Login.html" class="btn btn--amarelo">Logar <i
                                class="fa-solid fa-right-to-bracket"></i></a>
                    </div>
                <?php } else { ?>
                    <div class="d-flex ms-auto gap-2">
                        <button href="#" id="cerrarSesion-boton" class="btn btn--amarelo"> Sair <i
                                class="fa-solid fa-right-to-bracket"></i></button>
                    </div>
                    <script>
                        const botonCerrar = document.getElementById("cerrarSesion-boton");

                        botonCerrar.addEventListener("click", function(e) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Confirmar saída',
                                text: "Você tem certeza que deseja sair da conta?",
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Sim, sair',
                                cancelButtonText: 'Cancelar',
                                background: '#B2442E',
                                color: '#FFFFFF',
                                confirmButtonColor: '#FDCB29',
                                cancelButtonColor: '#333333',
                                iconColor: '#FDCB29'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = "CerrarSesion.php";
                                }
                            });
                        });
                    </script>
                <?php } ?>
            </div>
        </nav>

    </header>
    <main>
        <?php if ($idUsuario && $idUsuario == $idAssociadoDelEmprendimiento) { ?>
            <!-- Boton Flotante para Ajustes (Solo visible para el dueño) -->
            <a href="AjustesEmprendedor.php?token=<?php echo h($_GET['token']); ?>" title="Ajustes Gerais" class="btn" id="btnFlotante--ajustesGerais"><i
                    class="fa-solid fa-gear"></i>Ajustes</a>

            <!-- Para Productos -->
            <button id="btnAgregarProducto" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProduto">
                <i class="fa-solid fa-cart-plus"></i> Produto
            </button>
            <button id="btnEditarProducto" class="btn btn--amarelo">
                <i class="fa-solid fa-pen-to-square"></i> Produto
            </button>

            <!-- Modal de Agregar Produto -->
            <div class="modal fade modal-produto" id="modalProduto" tabindex="-1" aria-hidden="true">
                <form id="formProduto" action="../controllers/ProdutoController.php" method="post">
                    <input type="hidden" name="accion" value="GuardarProduto">
                    <input type="hidden" name="idEmprendimiento" value="<?php echo $idEmprendimiento ?>">
                    <div class="modal-dialog modal-dialog-centered modal-lg modal-produto-dialog">
                        <div class="modal-content modal-produto-content p-4">
                            <div class="container-fluid">
                                <!-- Título -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="modal-title fw-bold">Agregar Producto</h5>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <!-- Columna izquierda -->
                                    <div class="col-md-5">
                                        <div class="card modal-produto-card p-3">
                                            <div class="mb-3">
                                                <input class="form-control modal-produto-input" type="file"
                                                    id="modalProdutoFile" name="images[]" multiple>
                                            </div>
                                            <div id="modalProdutoPreview"
                                                class="modal-produto-preview border bg-light d-flex flex-wrap justify-content-center align-items-center text-muted p-2"
                                                style="gap: 10px; min-height: 150px; max-height: 280px; overflow-y: auto;">
                                                Nenhuma imagem selecionada
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button id="modalProdutoRemove"
                                                    class="btn modal-produto-btn-remove px-4">Remover imagen</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Columna derecha -->
                                    <div class="col-md-7">
                                        <div class="card modal-produto-card p-3">

                                            <!-- Botones de acción -->
                                            <div class="d-flex justify-content-end mb-3 gap-2">
                                                <button class="btn modal-produto-btn-cancel"
                                                    data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn modal-produto-btn-confirm">Confirmar</button>
                                            </div>

                                            <!-- Título y categorías -->
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-12">
                                                    <input required type="text" class="form-control modal-produto-input"
                                                        placeholder="Titulo" name="Titulo">
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="Categoria" required id="selectCategoria" class="form-select modal-produto-select">
                                                        <option value="" selected>Categoria:</option>
                                                        <?php foreach ($categorias as $iCat => $cat): ?>
                                                            <option value="<?php echo h($cat['idCategoria']) ?>"><?php echo h($cat['nombre']) ?></option>
                                                        <?php endforeach; ?>

                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="Subcategoria" required id="selectSubcategoria" class="form-select modal-produto-select">
                                                        <option>Sub-Categoria</option>
                                                    </select>
                                                </div>
                                            </div>


                                            <!-- Descripción -->
                                            <div class="mb-3">
                                                <textarea required name="Descripcion" class="form-control modal-produto-textarea" rows="4"
                                                    placeholder="Descrição"></textarea>
                                            </div>

                                            <!-- tamaño y color -->
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <input name="Tamanho" type="text" class="form-control modal-produto-input"
                                                        placeholder="Tamanho">
                                                </div>
                                                <div class="col-md-6">
                                                    <input name="Color" type="text" class="form-control modal-produto-input"
                                                        placeholder="Cor">
                                                </div>
                                            </div>

                                            <!-- Precio -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <input name="Precio" required type="text" class="form-control modal-produto-input"
                                                        placeholder="Preço">
                                                </div>
                                            </div>

                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Modal de Editar Produto -->
            <div class="modal fade modal-produto" id="modalProdutoEditar" tabindex="-1" aria-hidden="true">
                <form id="formEditarProduto" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="accion" value="actualizarProducto">
                    <input type="hidden" name="idProducto" id="modalEditarIdProducto">

                    <div class="modal-dialog modal-dialog-centered modal-lg modal-produto-dialog">
                        <div class="modal-content modal-produto-content p-4">
                            <div class="container-fluid">
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h5 class="modal-title fw-bold">Editar Producto</h5>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <div class="col-md-5">
                                        <div class="card modal-produto-card p-3">
                                            <h6 class="fw-bold small">Imágenes Existentes</h6>
                                            <div id="modalEditarImagenesExistentes" class="d-flex flex-wrap p-2" style="gap: 10px; min-height: 50px; max-height: 150px; overflow-y: auto;">
                                            </div>

                                            <hr>
                                            <h6 class="fw-bold small">Agregar Nuevas Imágenes</h6>
                                            <div class="mb-3">
                                                <input name="images[]" class="form-control modal-produto-input" type="file" id="modalEditarProdutoFile" multiple>
                                            </div>
                                            <div id="modalEditarProdutoPreview"
                                                class="modal-produto-preview border bg-light d-flex justify-content-center align-items-center text-muted">
                                                Ninguna imagen seleccionada
                                            </div>
                                            <div class="d-flex justify-content-center mt-3">
                                                <button type="button" id="modalEditarProdutoRemove" class="btn modal-produto-btn-remove px-4">Remover nuevas</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-7">
                                        <div class="card modal-produto-card p-3">

                                            <div class="d-flex justify-content-end mb-3 gap-2">
                                                <button type="button" class="btn modal-produto-btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn modal-produto-btn-confirm">Confirmar Cambios</button>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-12">
                                                    <input required name="Titulo" id="modalEditarTitulo" type="text" class="form-control modal-produto-input" placeholder="Novo titulo">
                                                </div>
                                                <div class="col-md-6">
                                                    <select required name="Categoria" id="modalEditarCategoria" class="form-select modal-produto-select">
                                                        <option value="" selected>Categoria:</option>
                                                        <?php foreach ($categorias as $iCat => $cat): ?>
                                                            <option value="<?php echo h($cat['idCategoria']) ?>"><?php echo h($cat['nombre']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <select required name="Subcategoria" id="modalEditarSubcategoria" class="form-select modal-produto-select">
                                                        <option>Nova sub-Categoria</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <textarea required name="Descripcion" id="modalEditarDescripcion" class="form-control modal-produto-textarea" rows="4" placeholder="Nova descrição..."></textarea>
                                            </div>

                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <input name="Tamanho" id="modalEditarTamanho" type="text" class="form-control modal-produto-input" placeholder="Novo tamanho">
                                                </div>
                                                <div class="col-md-6">
                                                    <input name="Color" id="modalEditarColor" type="text" class="form-control modal-produto-input" placeholder="Nova cor">
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <input required name="Precio" id="modalEditarPrecio" type="text" class="form-control modal-produto-input" placeholder="Novo preço">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <?php } ?>

        <div class="container-fluid">
            <!-- Conteudo da Portada Principal -->
            <div id="portada" class="row portada">
                <div class="col-12 portada__conteudo-principal">
                    <img class="portada__imagem transiccionSuave" src="../<?php echo h($emprendimiento['pooster']); ?>"
                        alt="Foto de portada de <?php echo h($emprendimiento['nome']); ?>">
                    <div class="portada__conteudo-secundario mt-5 transiccionSuave">
                        <h1 class="subTitulo portada__subTitulo transiccionSuave">Historia</h1>
                        <p class="parrafo portada__parrafo transiccionSuave">
                            <?php echo h($emprendimiento['historia']); ?>
                        </p>
                        <button id="portada__botao" type="button" class="btn btn--vermelho portada__botao"> Ver mais
                        </button>
                    </div>
                    <div class="portada__conteudo-terceario mt-5">
                        <img src="<?php echo h($infoAssociado['FotoPerfilAssociado']); ?>" width="150" alt="Foto de perfil de <?php echo h($infoAssociado['NombreAssociado']); ?>">
                        <h3 class="subTitulo portada__subTitulo">
                            <?php echo h($infoAssociado['NombreAssociado']); ?>
                        </h3>
                        <p class="parrafo portada__parrafo portada_descResposavel">
                            <?php echo h($infoAssociado['DescripcionAssociado']); ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <!-- Conteudo da Processo de fabrição -->
            <?php if (!empty($imgemsFabricacao)): ?>
                <div class="row mt-5 fabricacao">
                    <!-- conteudo onde estam as imagems da Processo de fabrição -->
                    <div class="col-6 fabricacao__conteudo--principal">
                        <?php
                        $maxFabImages = min(count($imgemsFabricacao), 4);
                        for ($i = 0; $i < $maxFabImages; $i++): ?>
                            <img class="fabricacao__imagem" src="../<?php echo h($imgemsFabricacao[$i]["caminho_imagem"]); ?>"
                                alt="Imagen proceso fabricación <?php echo $i + 1; ?>">
                        <?php endfor; ?>
                    </div>
                    <!-- conteudo da informação da Processo de fabrição -->
                    <div class="col-6 fabricacao__conteudo--secundario contornoGris">
                        <h2 class="subTitulo m-3 text-center"> Processo de fabricação </h2>
                        <p class="parrafo fabricacao__conteudosecundario--parrafo">
                            <?php echo h($emprendimiento['processoFabricacao']); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <hr class="mt-5 mb-5">

            <!-- Galeria de fotos -->
            <?php if (!empty($imgemsGaleria)): ?>
                <div id="galeriaFotos" class="carousel slide" data-bs-ride="carousel">
                    <!-- Indicadores -->
                    <ol class="carousel-indicators">
                        <?php foreach ($imgemsGaleria as $index => $imagem): ?>
                            <li data-bs-target="#galeriaFotos"
                                data-bs-slide-to="<?php echo $index; ?>"
                                class="<?php echo $index === 0 ? 'active' : ''; ?>"
                                aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                aria-label="Slide <?php echo $index + 1; ?>">
                            </li>
                        <?php endforeach; ?>
                    </ol>

                    <!-- Imágenes del Carrusel -->
                    <div class="carousel-inner" role="listbox">
                        <?php foreach ($imgemsGaleria as $index => $imagem): ?>
                            <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                <img src="../<?php echo h($imagem['caminho_imagem']); ?>"
                                    class="w-100 d-block galeria__imagem"
                                    alt="Imagen Galería <?php echo $index + 1; ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Controles -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#galeriaFotos" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#galeriaFotos" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            <?php endif; ?>

            <?php if (!empty($productos)): ?>
                <div class="row mt-5" id="Produtos">
                    <div class="col-12 text-center">
                        <h3 class="subTitulo">Produtos</h3>
                    </div>
                </div>

                <div id="filtroContainer" class="container-fluid sticky-top bg-light shadow-sm py-3 mb-4">
                    <div class="container">
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fa-solid fa-search"></i></span>
                                    <input type="text" id="filtroBuscaInput" class="form-control" placeholder="Buscar por nome, categoria, cor...">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h6 class="small fw-bold text-muted">IR PARA:</h6>
                                <nav id="filtroCategorias" class="nav nav-pills flex-nowrap overflow-auto" style="white-space: nowrap;">

                                    <?php if (!empty($categorias)): ?>
                                        <?php foreach ($categorias as $cat): ?>
                                            <?php
                                            $subcategorias = $subcategoriaHelper->obtenerSubCategoriasDelEmprendimiento($cat['idCategoria']);
                                            $productosEnCategoria = 0;

                                            foreach ($subcategorias as $subCat) {
                                                foreach ($productos as $prod) {
                                                    if ($prod['producto_idSubcategoria'] == $subCat['idSubcategoria']) {
                                                        $productosEnCategoria++;
                                                    }
                                                }
                                            }

                                            if ($productosEnCategoria == 0) continue;
                                            ?>

                                            <a class="nav-link" href="#cat-<?php echo h($cat['idCategoria']); ?>">
                                                <?php echo h($cat['nombre']); ?>
                                            </a>

                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                </nav>
                            </div>
                        </div>
                    </div>
                </div>


                <?php foreach ($categorias as $iCat => $cat): ?>

                    <?php
                    // Obtener las subcategorías de la categoría actual
                    $subcategorias = $subcategoriaHelper->obtenerSubCategoriasDelEmprendimiento($cat['idCategoria']);

                    // Contador de productos totales dentro de la categoría
                    $productosEnCategoria = 0;

                    // Precalcular si la categoría tiene productos
                    foreach ($subcategorias as $subCat) {
                        foreach ($productos as $prod) {
                            if ($prod['producto_idSubcategoria'] == $subCat['idSubcategoria']) {
                                $productosEnCategoria++;
                            }
                        }
                    }

                    // Si la categoría no tiene productos, saltar (no mostrar)
                    if ($productosEnCategoria == 0) continue;
                    ?>

                    <!-- Si tiene productos, mostramos la categoría -->
                    <div class="row mb-5 category-section">
                        <div class="col-12 p-2 category-title-wrapper" id="cat-<?php echo h($cat['idCategoria']); ?>">
                            <h3 class="subTitulo text-uppercase">
                                <i class="fa-solid fa-folder"></i> <?php echo h($cat['nombre']); ?>
                            </h3>
                        </div>

                        <?php foreach ($subcategorias as $iSubCat => $subCat): ?>
                            <?php
                            $productosEnSubCategoria = 0;
                            foreach ($productos as $prod) {
                                if ($prod['producto_idSubcategoria'] == $subCat['idSubcategoria']) {
                                    $productosEnSubCategoria++;
                                }
                            }

                            if ($productosEnSubCategoria == 0) continue;
                            ?>

                            <div class="col-12 ms-4 mt-3 subcategory-section" id="subcat-<?php echo h($subCat['idSubcategoria']); ?>">
                                <hr>
                                <h4 class="parrafo">
                                    <i class="fa-regular fa-folder-open"></i>
                                    <?php echo h($subCat['nombre']); ?>
                                </h4>
                            </div>

                            <div class="row ms-4 product-grid">
                                <?php foreach ($productos as $iProd => $prod): ?>
                                    <?php if ($prod['producto_idSubcategoria'] == $subCat['idSubcategoria']): ?>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 product-item-col"
                                            data-search-terms="<?php
                                                                echo h($prod['titulo']) . ' ' .
                                                                    h($prod['descripcion']) . ' ' .
                                                                    h($cat['nombre']) . ' ' .
                                                                    h($subCat['nombre']) . ' ' .
                                                                    h($prod['color']) . ' ' .
                                                                    h($prod['tamano']);
                                                                ?>">

                                            <div class="card h-100" id="product-card-<?php echo h($prod['idProducto']); ?>">
                                                <img class="card-img-top product-card-image" height="250px"
                                                    src="<?php echo h($prod['imagen_principal']); ?>"
                                                    alt="Producto por defecto">
                                                <div class="card-body">
                                                    <h4 class="card-title text-center product-card-title">
                                                        <?php echo h($prod['titulo']); ?>
                                                    </h4>
                                                    <div class="d-flex align-items-center mt-4">
                                                        <span>
                                                            <small><b>$ <span class="product-card-price"><?php echo h($prod['precio']); ?></span></b></small>
                                                        </span>
                                                        <button class="btn btn--vermelho ms-auto btn-comprar-produto"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalheProduto"
                                                            data-product-id="<?php echo h($prod['idProducto']); ?>">
                                                            Comprar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>

                        <?php endforeach; ?>

                        <div class="col-12 text-center no-results-message" style="display: none;">
                            <p class="text-muted">Nenhum produto encontrado nesta categoria com o termo buscado.</p>
                        </div>
                    </div>
                <?php endforeach; ?>


                <div class="row" id="globalNoResults" style="display: none;">
                    <div class="col-12 text-center py-5">
                        <h3 class="text-muted">Nenhum produto encontrado</h3>
                        <p>Tente ajustar seus termos de busca.</p>
                    </div>
                </div>

            <?php else: ?>
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <h3 class="subTitulo">Produtos</h3>
                    </div>
                    <div class="col-12">
                        <p>Não tem produtos guardados...</p>
                    </div>
                </div>
            <?php endif; ?>


        </div>

        <div class="modal fade" id="modalDetalheProduto" tabindex="-1" aria-labelledby="modalDetalheProdutoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-lg-down">
                <div class="modal-content modal-detalhe-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container-fluid">
                            <div class="row">

                                <div class="col-lg-7">
                                    <div class="mb-3">
                                        <img src="" id="modalDetalheImagemPrincipal" class="img-fluid w-100" style="border-radius: 15px; max-height: 500px; object-fit: cover;">
                                    </div>
                                    <div id="modalDetalheThumbnails" class="d-flex justify-content-center flex-wrap" style="gap: 10px;">
                                    </div>
                                </div>

                                <div class="col-lg-5">
                                    <h2 id="modalDetalheTitulo" class="fw-bold mb-3">Cargando...</h2>

                                    <h3 id="modalDetalhePreco" class="h2 text-success fw-light mb-4"></h3>

                                    <p id="modalDetalheDescricao" class="lead fs-6 mb-4"></p>

                                    <ul class="list-group list-group-flush mb-4">
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <strong class="text-muted">Cor:</strong>
                                            <span id="modalDetalheCor"></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between px-0">
                                            <strong class="text-muted">Tamanho:</strong>
                                            <span id="modalDetalheTamanho"></span>
                                        </li>
                                    </ul>

                                    <div class="d-grid">
                                        <a href="#" id="modalDetalheBtnWhatsapp" target="_blank" class="btn btn-success btn-lg">
                                            <i class="fa-brands fa-whatsapp"></i>
                                            Solicitar por WhatsApp
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
        </style>

    </main>
    <footer class="mt-5 footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-6 text-center mb-3">
                    <a href="#navBar">
                        <img width="80px" src="../<?php echo h($emprendimiento['logo']); ?>" alt="Logo Footer">
                    </a>
                    <h3 class="footer__titulo"> <?php echo h($emprendimiento['nome']); ?>
                    </h3>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <h4 class="footer__subTitulo"> Legales </h4>
                    <ul class="footer__texto list-unstyled">
                        <li> <a href="#" class="text-decoration-none footer__texto--link"> Politica de Qualidade </a> </li>
                        <li> <a href="#" class="text-decoration-none footer__texto--link"> Politica de Privacidade</a> </li>
                        <li> <a href="#" class="text-decoration-none footer__texto--link"> Politica de Dados </a> </li>
                    </ul>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <h4 class="footer__subTitulo"> Informação </h4>
                    <ul class="footer__texto list-unstyled">
                        <li> Telefone: <?php echo h($emprendimiento['telefone']); ?> </li>
                        <li> Horarios: <?php echo h($emprendimiento['horarios']); ?> </li>
                        <li> WhatsApp: <?php echo h($emprendimiento['celular']); ?> </li>
                    </ul>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <h4 class="footer__subTitulo"> Endereço </h4>
                    <ul class="footer__texto list-unstyled">
                        <li> <?php echo h($emprendimiento['ubicacao']); ?> </li>
                    </ul>
                </div>
                <div class="col-md-3 col-12 text-center">
                    <h4 class="footer__subTitulo"> Redes Sociais </h4>
                    <div class="d-flex justify-content-center gap-3">
                        <?php if (!empty($emprendimiento['facebook'])): ?>
                            <a href="<?php echo h($emprendimiento['facebook']); ?>" target="_blank" class="fs-2 text-primary">
                                <i class="fa-brands fa-facebook"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($emprendimiento['instagram'])): ?>
                            <a href="<?php echo h($emprendimiento['instagram']); ?>" target="_blank" class="fs-2 text-danger">
                                <i class="fa-brands fa-square-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <a href="#" class="fs-2 text-success">
                            <i class="fa-brands fa-square-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        xintegrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        xintegrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>

    <!-- Sweet Alert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>


    <!-- My JS  -->
    <script src="../assets/js/MenuEmprendedores.js"></script>

    <script>
        //Info para la compra de los productos
        const whatsappEmprendedor = "<?php echo h($emprendimiento['celular'] ?? ''); ?>";
        const nomeUsuarioLogado = "<?php echo h($nomeUsuairo ?? ''); ?>";
    </script>

    <?php if ($idUsuario != null && $tipoUsuario == "associado") { ?>
        <script>
            const listaDeProductos = <?php echo json_encode($productos ?? []); ?>;
        </script>
        <script src="../assets/js/MenuEmprendedoresAdmin.js"></script>
    <?php } ?>


    <?php
    //Alerta de bien venida al associado
    if (isset($_SESSION["user"]['alerta'])) {

        if ($_SESSION["user"]['tipo'] === "associado") {
            echo "
                <script>
                    Swal.fire({
                        title: 'Bem-vindo, Empreendedor!',
                        html: 'Prepare-se para gerenciar seus produtos e vendas de forma eficiente.<br><br>Vamos crescer juntos!',
                        icon: 'success',
                        confirmButtonText: 'Vamos lá!',
                        background: '#DCA700',
                        color: '#000000',
                        confirmButtonColor: '#B2442E',
                        iconColor: '#FFFFFF'
                    });
                </script>
            ";
        }
        unset($_SESSION["user"]['alerta']);
    }
    ?>
</body>

</html>