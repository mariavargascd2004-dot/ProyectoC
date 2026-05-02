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
require_once '../controllers/PaginaPrincipalController.php';

try {
    $empHelper = new EmprendimientosHelper();
    $assHelper = new adminsAssociadoHelper();
    $FabricacaoHelper = new imagemFabricacaoHelper();
    $GaleriaHelper = new imagemGaleriaHelper();
    $produtoHelper = new produtoHelper();
    $produtoImagemHelper = new imagemProdutoHelper();
    $categoriaHelper = new categoriaHelper();
    $subcategoriaHelper = new subcategoriaHelper();
    $controllerMenuPrincipal = new PaginaPrincipalController();

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
    
    $datosPaginaP = $controllerMenuPrincipal->obtenerDatos();

} catch (Exception $e) {
    error_log("Error en menuEmprendimiento.php: " . $e->getMessage());
    header("location:../");
    exit;
}

function h($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function getContrastColor($hexColor) {
    $hexColor = str_replace('#', '', $hexColor);
    if (strlen($hexColor) == 3) {
        $hexColor = str_repeat(substr($hexColor,0,1), 2) . str_repeat(substr($hexColor,1,1), 2) . str_repeat(substr($hexColor,2,1), 2);
    }
    if (strlen($hexColor) != 6) { return '#000000'; }
    $r = hexdec(substr($hexColor, 0, 2));
    $g = hexdec(substr($hexColor, 2, 2));
    $b = hexdec(substr($hexColor, 4, 2));
    $yiq = (($r * 299) + ($g * 587) + ($b * 114)) / 1000;
    return ($yiq >= 128) ? '#000000' : '#FFFFFF';
}

function getImageUrl($path) {
    if (empty($path)) return '';
    if (strpos($path, 'http') === 0) return h($path);
    if (strpos($path, '../') === 0) return h($path);
    return '../' . h($path);
}

$corTextoPrimaria = getContrastColor(h($emprendimiento['corPrincipal']));
$corTextoSecundaria = getContrastColor(h($emprendimiento['corSecundaria']));


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
            --cor-texto-primaria: <?php echo $corTextoPrimaria; ?>;
            --cor-texto-secundaria: <?php echo $corTextoSecundaria; ?>;
        }
        
        .btn--vermelho {
            background-color: var(--cor-secundaria) !important;
            color: var(--cor-texto-secundaria) !important;
            border: 1px solid var(--cor-secundaria) !important;
        }
        .btn--vermelho:hover {
            background-color: var(--cor-primaria) !important;
            color: var(--cor-texto-primaria) !important;
            border-color: var(--cor-primaria) !important;
        }
        .btn--amarelo {
            background-color: var(--cor-primaria) !important;
            color: var(--cor-texto-primaria) !important;
            border: 1px solid var(--cor-primaria) !important;
        }
        .btn--amarelo:hover {
            background-color: var(--cor-secundaria) !important;
            color: var(--cor-texto-secundaria) !important;
            border-color: var(--cor-secundaria) !important;
        }
        .fundoVermelho {
            background-color: var(--cor-secundaria) !important;
            color: var(--cor-texto-secundaria) !important;
        }
        .titulo {
            color: var(--cor-primaria);
            font-family: 'Alegreya SC', serif;
        }
    </style>

    <header>
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <!-- Logo y enlaces -->
                <div class="navbar-nav d-flex align-items-center">
                    <a href="index.php">
                        <img src="<?php echo htmlspecialchars(isset($datosPaginaP) && $datosPaginaP ? $datosPaginaP->getLogo() : '../assets/img/CasaSolidaria/defaultLogo.png'); ?>" alt="Logo Casa Solidaria" class="rounded-circle shadow-sm bg-white" style="width: 80px; height: 80px; object-fit: cover;">
                    </a>
                    
                    <img src="<?php echo getImageUrl($emprendimiento['logo']); ?>" alt="Logo de <?php echo h($emprendimiento['nome']); ?>" class="rounded-circle shadow-sm bg-white ms-2" style="width: 80px; height: 80px; object-fit: cover;">
                    
                    <div>
                        <a class="nav-item nav-link titulo nav__titulo" href="#"><?php echo h($emprendimiento['nome']); ?></a>
                    </div>
                    
                    <a class="nav-item nav-link" href="#Produtos">Produtos <i class="fa-solid fa-arrow-down"></i></a>
                </div>

                <!-- Botones alineados a la derecha -->
                <!-- Sin login -->
                <?php if (!isset($idUsuario) || $idUsuario == null) { ?>
                    <div class="d-flex ms-auto gap-2">
                        <a href="Registro.html" class="btn btn--cinza">Registrarse <i class="fa-solid fa-user-plus"></i></a>
                        <a href="Login.html" class="btn btn--amarelo">Logar <i class="fa-solid fa-right-to-bracket"></i></a>
                    </div>
                <?php } else { ?>
                    <div class="d-flex ms-auto gap-2">
                        <a href="#" id="cerrarSesion-boton" class="btn btn--amarelo"> Sair <i class="fa-solid fa-right-to-bracket"></i></a>
                    </div>
                    <script>
                        document.addEventListener('DOMContentLoaded', () => {
                            const botonCerrar = document.getElementById("cerrarSesion-boton");
                            if (botonCerrar) {
                                botonCerrar.addEventListener("click", function(e) {
                                    e.preventDefault();
                                    if (typeof Swal !== 'undefined') {
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
                                    } else {
                                        if (confirm("Você tem certeza que deseja sair da conta?")) {
                                            window.location.href = "CerrarSesion.php";
                                        }
                                    }
                                });
                            }
                        });
                    </script>
                <?php } ?>
            </div>
        </nav>
    </header>
    <main>
        <?php
        $esAdminGeneral = ($tipoUsuario === 'adminGeneral');
        $esDono = ($idUsuario && $idUsuario == $idAssociadoDelEmprendimiento);
        if ($esDono || $esAdminGeneral):
        ?>
            <!-- Boton Flotante para Ajustes (visible para el dueño y para el adminGeneral) -->
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
            <div class="modal fade modal-produto" id="modalProduto" tabindex="-1" aria-hidden="true" enctype="multipart/form-data">
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
                                                    <input name="Precio" required type="number" class="form-control modal-produto-input"
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

        <?php endif; ?>


        <section class="hero-business">
            <div class="container">
                <div class="row align-items-center">
                    <!-- Columna Izquierda: Imagen de Portada -->
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="hero-business__img-wrapper">
                            <img class="hero-business__img" src="<?php echo getImageUrl($emprendimiento['pooster']); ?>"
                                alt="Foto de portada de <?php echo h($emprendimiento['nome']); ?>">
                        </div>
                    </div>

                    <!-- Columna Derecha: Información del Emprendimiento -->
                    <div class="col-lg-6">
                        <div class="hero-business__content">
                            <h2 class="hero-business__history-title">Nossa Historia</h2>
                            <p class="hero-business__history-text parrafo-truncado">
                                <?php echo h($emprendimiento['historia']); ?>
                            </p>
                            
                            <button id="portada__botao" type="button" class="btn btn--vermelho hero-business__btn"> 
                                Ver mais
                            </button>

                            <!-- Mini Perfil del Responsable (Secundario) -->
                            <div class="hero-business__vendor">
                                <img src="<?php echo getImageUrl($infoAssociado['FotoPerfilAssociado'] ?? ''); ?>" 
                                     class="hero-business__vendor-img" 
                                     alt="Foto de <?php echo h($infoAssociado['NombreAssociado']); ?>"
                                     onerror="this.onerror=null; this.src='../assets/img/CasaSolidaria/defaultLogo.png'">
                                <div class="hero-business__vendor-info">
                                    <span class="hero-business__vendor-label">Responsável</span>
                                    <span class="hero-business__vendor-name"><?php echo h($infoAssociado['NombreAssociado']); ?></span>
                                    <p class="hero-business__vendor-desc mb-0"><?php echo h($infoAssociado['DescripcionAssociado'] ?? ''); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        </div> <!-- Cierre del container general para permitir ancho completo en la pizarra -->

        <!-- Sección: Processo de Fabricação (Nota del Autor) -->
        <?php if (!empty($imgemsFabricacao)): ?>
            <section class="manufacturing-section">
                <div class="container-fluid px-5"> <!-- px-5 para que el contenido no pegue a los bordes físicos -->
                    <div class="row align-items-center g-5">
                        <?php
                        $maxFabImages = min(count($imgemsFabricacao), 4);
                        ?>
                        
                        <!-- Columna Izquierda: Pasos 1 y 2 -->
                        <div class="col-lg-3 order-2 order-lg-1">
                            <div class="m-side-gallery">
                                <?php for ($i = 0; $i < min($maxFabImages, 2); $i++): ?>
                                    <div class="polaroid-item">
                                        <span class="polaroid-step">Passo <?php echo ($i + 1); ?></span>
                                        <img src="<?php echo getImageUrl($imgemsFabricacao[$i]["caminho_imagem"]); ?>"
                                             alt="Processo de fabricação <?php echo $i + 1; ?>">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>

                        <!-- Columna Central: Información -->
                        <div class="col-lg-6 order-1 order-lg-2">
                            <div class="studio-card mx-auto">
                                <h2 class="studio-title">Processo de Fabricação</h2>
                                <p class="studio-text">
                                    <?php echo nl2br(h($emprendimiento['processoFabricacao'])); ?>
                                </p>
                            </div>
                        </div>

                        <!-- Columna Derecha: Pasos 3 y 4 -->
                        <div class="col-lg-3 order-3 order-lg-3">
                            <div class="m-side-gallery">
                                <?php for ($i = 2; $i < $maxFabImages; $i++): ?>
                                    <div class="polaroid-item">
                                        <span class="polaroid-step">Passo <?php echo ($i + 1); ?></span>
                                        <img src="<?php echo getImageUrl($imgemsFabricacao[$i]["caminho_imagem"]); ?>"
                                             alt="Processo de fabricação <?php echo $i + 1; ?>">
                                    </div>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <div class="container"> <!-- Reabrimos el container para el resto del contenido -->
            <hr class="mt-5 mb-5">

            <!-- Galeria de fotos -->
            <?php if (!empty($imgemsGaleria)): ?>
                <div class="row mt-5 mb-4">
                    <div class="col-12 text-center">
                        <h3 class="subTitulo">Nossa Galeria</h3>
                    </div>
                </div>

                <div id="galeriaFotos" class="carousel slide" data-bs-ride="carousel">
                    <!-- Indicadores -->
                    <div class="carousel-indicators">
                        <?php foreach ($imgemsGaleria as $index => $imagem): ?>
                            <button type="button" 
                                data-bs-target="#galeriaFotos"
                                data-bs-slide-to="<?php echo $index; ?>"
                                class="<?php echo $index === 0 ? 'active' : ''; ?>"
                                aria-current="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                aria-label="Slide <?php echo $index + 1; ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>

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

                <div id="filtroContainer" class="container sticky-top bg-white border-bottom py-3 mb-5">
                    <div class="row g-3 align-items-center">
                        <!-- Búsqueda -->
                        <div class="col-12 col-lg-8">
                            <div class="search-box">
                                <i class="fa-solid fa-search search-icon"></i>
                                <input type="text" id="filtroBuscaInput" class="form-control-modern" placeholder="O que você está procurando?">
                            </div>
                        </div>
                        
                        <!-- Botón Tienda -->
                        <div class="col-12 col-lg-4">
                            <a href="Tienda.php?token=<?php echo h($_GET['token']); ?>" class="btn btn-loja-modern w-100">
                                Loja Completa <i class="fa-solid fa-shopping-bag ms-2"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <?php foreach ($categorias as $iCat => $cat): ?>
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

                    <div class="row mb-4 category-section">
                        <div class="col-12 p-3 category-title-wrapper collapsible-header" 
                             data-target="#cat-content-<?php echo h($cat['idCategoria']); ?>">
                            <h3 class="subTitulo text-uppercase d-flex align-items-center justify-content-between mb-0">
                                <span><i class="fa-solid fa-folder-open me-2"></i> <?php echo h($cat['nombre']); ?></span>
                                <i class="fa-solid fa-chevron-up toggle-icon"></i>
                            </h3>
                        </div>

                        <div id="cat-content-<?php echo h($cat['idCategoria']); ?>" class="collapsible-body show">
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

                                <div class="col-12 mt-3 subcategory-section collapsible-header" 
                                     data-target="#subcat-content-<?php echo h($subCat['idSubcategoria']); ?>">
                                    <h4 class="parrafo d-flex align-items-center justify-content-between py-2 border-bottom">
                                        <span><i class="fa-regular fa-folder-open me-2"></i> <?php echo h($subCat['nombre']); ?></span>
                                        <i class="fa-solid fa-chevron-up toggle-icon ms-auto" style="font-size: 0.8rem;"></i>
                                    </h4>
                                </div>

                                <div id="subcat-content-<?php echo h($subCat['idSubcategoria']); ?>" class="collapsible-body show">
                                    <div class="row product-grid pt-3">
                                <?php foreach ($productos as $iProd => $prod): ?>
                                    <?php if ($prod['producto_idSubcategoria'] == $subCat['idSubcategoria']): ?>
                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4 product-item-col"
                                            data-titulo="<?php echo h($prod['titulo']); ?>"
                                            data-precio="<?php echo h($prod['precio']); ?>"
                                            data-search-terms="<?php
                                                                echo h($prod['titulo']) . ' ' .
                                                                    h($prod['descripcion']) . ' ' .
                                                                    h($cat['nombre']) . ' ' .
                                                                    h($subCat['nombre']) . ' ' .
                                                                    h($prod['color']) . ' ' .
                                                                    h($prod['tamano']);
                                                                ?>">

                                            <div class="product-card-modern">
                                                <div class="product-card-img-wrapper">
                                                    <img class="product-card-image-modern" 
                                                         src="<?php echo h($prod['imagen_principal']); ?>"
                                                         alt="<?php echo h($prod['titulo']); ?>">
                                                    <div class="product-card-overlay">
                                                        <button class="btn-quick-view" 
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#modalDetalheProduto"
                                                                data-product-id="<?php echo h($prod['idProducto']); ?>">
                                                            <i class="fa-solid fa-eye"></i> Detalhes
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="product-card-info">
                                                    <h4 class="product-card-title-modern">
                                                        <?php echo h($prod['titulo']); ?>
                                                    </h4>
                                                    <div class="product-card-footer-modern">
                                                        <span class="product-card-price-modern">
                                                            R$ <?php echo h($prod['precio']); ?>
                                                        </span>
                                                        <button class="btn-buy-icon"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#modalDetalheProduto"
                                                            data-product-id="<?php echo h($prod['idProducto']); ?>">
                                                            <i class="fa-solid fa-cart-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                          </div> <!-- product-grid -->
                                </div> <!-- collapsible-body subcat -->
                            <?php endforeach; ?>

                            <div class="col-12 text-center no-results-message" style="display: none;">
                                <p class="text-muted">Nenhum produto encontrado nesta categoria com o termo buscado.</p>
                            </div>
                        </div> <!-- collapsible-body cat -->
                    </div> <!-- category-section -->
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
                        <li> <a href="politicaQualidade.html" class="text-decoration-none footer__texto--link"> Politica de Qualidade </a> </li>
                        <li> <a href="politicaPrivacidade.html" class="text-decoration-none footer__texto--link"> Politica de Privacidade</a> </li>
                        <li> <a href="politicaDados.html" class="text-decoration-none footer__texto--link"> Politica de Dados </a> </li>
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
                        <a href="https://api.whatsapp.com/send?phone=<?php echo h($emprendimiento['celular']); ?>" class="fs-2 text-success">
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

    <?php if (isset($esDono) && isset($esAdminGeneral) && ($esDono || $esAdminGeneral)) { ?>
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