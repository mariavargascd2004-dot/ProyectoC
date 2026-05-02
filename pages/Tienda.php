<?php
session_start();

$idUsuario = $_SESSION["user"]['id'] ?? null;
$tipoUsuario = $_SESSION["user"]['tipo'] ?? null;
$nomeUsuairo = $_SESSION["user"]['nome'] ?? null;

// Requerir los helpers y controladores
require_once '../controllers/PaginaPrincipalController.php';
require_once '../helpers/emprendimientosHelper.php';
require_once '../helpers/produtoHelper.php';
require_once '../helpers/categoriaHelper.php';
require_once '../helpers/subcategoriaHelper.php';
require_once '../helpers/imagemProdutoHelper.php';

$controllerMenuPrincipal = new PaginaPrincipalController();
$datosPaginaP = $controllerMenuPrincipal->obtenerDatos();

$empHelper = new EmprendimientosHelper();
$produtoHelper = new produtoHelper();
$categoriaHelper = new categoriaHelper();
$subcategoriaHelper = new subcategoriaHelper();
$produtoImagemHelper = new imagemProdutoHelper();

$esTiendaEspecifica = false;
$idEmprendimiento = null;
$infoEmprendimiento = null;

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $decoded = base64_decode($_GET['token'], true);
    if ($decoded !== false && filter_var($decoded, FILTER_VALIDATE_INT)) {
        $idEmprendimiento = intval($decoded);
        $infoEmprendimiento = $empHelper->obtenerEmprendimientoPorId($idEmprendimiento);
        if ($infoEmprendimiento) {
            $esTiendaEspecifica = true;
        }
    }
}

// Filtros GET
$q = $_GET['q'] ?? '';
$catsFiltro = $_GET['cat'] ?? [];
$subcatsFiltro = $_GET['subcat'] ?? [];
$corFiltro = $_GET['cor'] ?? [];
$tamanhoFiltro = $_GET['tamanho'] ?? [];
$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? floatval($_GET['min_price']) : null;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? floatval($_GET['max_price']) : null;
$sort = $_GET['sort'] ?? 'newest';

if (!is_array($catsFiltro)) $catsFiltro = [$catsFiltro];
if (!is_array($subcatsFiltro)) $subcatsFiltro = [$subcatsFiltro];
if (!is_array($corFiltro)) $corFiltro = [$corFiltro];
if (!is_array($tamanhoFiltro)) $tamanhoFiltro = [$tamanhoFiltro];

// Construir arreglo de filtros
$filtros = [
    'q' => $q,
    'categorias' => $catsFiltro,
    'subcategorias' => $subcatsFiltro,
    'cor' => $corFiltro,
    'tamanho' => $tamanhoFiltro,
    'min_price' => $minPrice,
    'max_price' => $maxPrice,
    'sort' => $sort
];

if ($esTiendaEspecifica) {
    $filtros['idEmprendimiento'] = $idEmprendimiento;
}

$productos = $produtoHelper->obtenerProductosFiltrados($filtros);

if ($productos && count($productos) > 0) {
    foreach ($productos as &$producto) {
        $imagenes = $produtoImagemHelper->obterImagemsComIdProduto($producto['idProducto']);
        if ($imagenes && count($imagenes) > 0) {
            $producto['imagen_principal'] = $imagenes[0]['caminho_imagem'];
        } else {
            $producto['imagen_principal'] = '../assets/img/CasaSolidaria/defaultProduct.png';
        }
        $producto['imagenes'] = $imagenes;
    }
    unset($producto);
}

// Obtener datos dinámicos para los menús de filtros de la BD
$coloresDisponibles = $produtoHelper->obtenerColoresDisponibles($esTiendaEspecifica ? $idEmprendimiento : null);
$tamanosDisponibles = $produtoHelper->obtenerTamanosDisponibles($esTiendaEspecifica ? $idEmprendimiento : null);

// Categorías
if ($esTiendaEspecifica) {
    $categoriasDisponibles = $categoriaHelper->obtenerCategoriasDelEmprendimiento($idEmprendimiento);
} else {
    $categoriasDisponibles = $categoriaHelper->obtenerTodasLasCategorias();
    
    // Si es global, eliminar categorías duplicadas por nombre
    $catNames = [];
    $catUnique = [];
    foreach($categoriasDisponibles as $cat) {
        $nombreLower = strtolower(trim($cat['nombre']));
        if(!in_array($nombreLower, $catNames)) {
            $catNames[] = $nombreLower;
            $catUnique[] = $cat;
        }
    }
    $categoriasDisponibles = $catUnique;
}

$subcategoriasPorCategoria = [];
if (!empty($categoriasDisponibles)) {
    foreach ($categoriasDisponibles as $cat) {
        $subs = $subcategoriaHelper->obtenerSubCategoriasDelEmprendimiento($cat['idCategoria']);
        if (!empty($subs)) {
            $subcategoriasPorCategoria[$cat['idCategoria']] = $subs;
        }
    }
}

function h($string) { return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8'); }

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

// Datos visuales
$tituloTienda = $esTiendaEspecifica ? h($infoEmprendimiento['nome']) : 'Loja Global';
$logoCasaSolidaria = htmlspecialchars($datosPaginaP ? $datosPaginaP->getLogo() : '../assets/img/CasaSolidaria/defaultLogo.png');
$logoTienda = $logoCasaSolidaria;
if ($esTiendaEspecifica && !empty($infoEmprendimiento['logo'])) {
    if (strpos($infoEmprendimiento['logo'], 'http') === 0) {
        $logoTienda = h($infoEmprendimiento['logo']);
    } else if (strpos($infoEmprendimiento['logo'], '../') === 0) {
        $logoTienda = h($infoEmprendimiento['logo']);
    } else {
        $logoTienda = '../' . h($infoEmprendimiento['logo']);
    }
}

$corPrimaria = $esTiendaEspecifica ? h($infoEmprendimiento['corPrincipal']) : '#DCA700'; 
$corSecundaria = $esTiendaEspecifica ? h($infoEmprendimiento['corSecundaria']) : '#B2442E';
$corTextoPrimaria = getContrastColor($corPrimaria);
$corTextoSecundaria = getContrastColor($corSecundaria);

$linkHome = $esTiendaEspecifica ? "Emprendimento.php?token=" . h($_GET['token']) : "../";

$telefono = $esTiendaEspecifica ? h($infoEmprendimiento['telefone']) : ($datosPaginaP ? $datosPaginaP->getTelefono() : '-');
$horarios = $esTiendaEspecifica ? h($infoEmprendimiento['horarios']) : ($datosPaginaP ? $datosPaginaP->getHorarios() : '-');
$celular = $esTiendaEspecifica ? h($infoEmprendimiento['celular']) : ($datosPaginaP ? $datosPaginaP->getCelular() : '-');
$direccion = $esTiendaEspecifica ? h($infoEmprendimiento['ubicacao']) : ($datosPaginaP ? $datosPaginaP->getDireccion() : '-');

$facebook = $esTiendaEspecifica ? h($infoEmprendimiento['facebook'] ?? '') : ($datosPaginaP ? $datosPaginaP->getFacebook() : '#');
$instagram = $esTiendaEspecifica ? h($infoEmprendimiento['instagram'] ?? '') : ($datosPaginaP ? $datosPaginaP->getInstagram() : '#');
$whatsappLink = "https://api.whatsapp.com/send?phone=" . urlencode($celular);

$tokenQS = $esTiendaEspecifica ? '<input type="hidden" name="token" value="'.h($_GET['token']).'">' : '';
?>
<!doctype html>
<html lang="pt-br">

<head>
    <title><?php echo $tituloTienda; ?> - Tienda</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

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
    <link rel="stylesheet" href="../assets/css/styleTienda.css">

    <style>
        :root {
            --cor-primaria: <?php echo $corPrimaria; ?>;
            --cor-secundaria: <?php echo $corSecundaria; ?>;
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
</head>

<body>
    <header>
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <!-- Logo y enlaces -->
                <div class="navbar-nav d-flex align-items-center">
                    <a href="index.php">
                        <img src="<?php echo htmlspecialchars(isset($datosPaginaP) && $datosPaginaP ? $datosPaginaP->getLogo() : '../assets/img/CasaSolidaria/defaultLogo.png'); ?>" alt="Logo Casa Solidaria" class="rounded-circle shadow-sm bg-white" style="width: 80px; height: 80px; object-fit: cover;">
                    </a>
                    
                    <?php if ($esTiendaEspecifica && isset($infoEmprendimiento)): ?>
                        <img src="../<?php echo htmlspecialchars($infoEmprendimiento['logo']); ?>" alt="Logo de <?php echo htmlspecialchars($infoEmprendimiento['nome']); ?>" class="rounded-circle shadow-sm bg-white ms-2" style="width: 80px; height: 80px; object-fit: cover;">
                        <div>
                            <a class="nav-item nav-link titulo nav__titulo" href="Emprendimento.php?token=<?php echo h($_GET['token']); ?>"><?php echo htmlspecialchars($infoEmprendimiento['nome']); ?></a>
                        </div>
                    <?php else: ?>
                        <div>
                            <a class="nav-item nav-link titulo" href="index.php">Loja Solidaria</a>
                        </div>
                    <?php endif; ?>

                    <?php if ($esTiendaEspecifica): ?>
                        <a class="nav-item nav-link fw-bold text-danger" href="Tienda.php"><i class="fa-solid fa-store me-1"></i> Toda a Loja</a>
                    <?php endif; ?>

                    <a class="nav-item nav-link" href="index.php#Emprendimentos">Emprendimentos <i class="fa-solid fa-arrow-down"></i></a>
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
        <div class="container-fluid">
            <!-- Tienda -->
            <div class="row mt-4 px-2 px-md-0">
                <!-- Filtros -->
                <div class="col-12 col-md-3 col-lg-2 ms-md-3 mb-4 mb-md-0 h-100">
                    <button class="btn btn-outline-secondary d-md-none w-100 mb-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasFiltros" aria-controls="offcanvasFiltros">
                        <i class="fa-solid fa-filter"></i> Mostrar Filtros
                    </button>
                    
                    <div class="offcanvas-md offcanvas-start contornoGris" tabindex="-1" id="offcanvasFiltros" aria-labelledby="offcanvasFiltrosLabel">
                        <div class="offcanvas-header border-bottom d-md-none">
                            <h5 class="offcanvas-title" id="offcanvasFiltrosLabel">Filtros</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#offcanvasFiltros" aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body p-3 flex-column">
                            <form method="GET" action="Tienda.php" id="formFiltros" class="w-100">
                                <?php echo $tokenQS; ?>
                        
                        <?php if(!empty($q)): ?>
                        <!-- Palabras-chaves -->
                        <div class="col-12 mt-3">
                            <span> Palabras-chaves </span>
                        </div>
                        <div class="col-12">
                            <span class="btn btn-light m-1"> <small> <?php echo h($q); ?> <i class="fa-solid fa-xmark text-danger" onclick="document.getElementById('tienda__buscador--input_main').value=''; document.getElementById('tienda__buscador--input_hidden').value=''; document.getElementById('formFiltros').submit();" style="cursor:pointer"></i> </small> </span>
                        </div>
                        <input type="hidden" name="q" id="tienda__buscador--input_hidden" value="<?php echo h($q); ?>">
                        <?php endif; ?>
                        
                        <!-- Categorias -->
                        <?php if (!empty($categoriasDisponibles)): ?>
                        <div class="col-12 mt-3">
                            <span class="fw-bold">Categorias</span>
                        </div>
                        <?php foreach($categoriasDisponibles as $cat): ?>
                        <div class="col-12 mt-2">
                            <div class="form-check">
                                <input class="form-check-input filter-checkbox" type="checkbox" name="cat[]" value="<?php echo h($cat['nombre']); ?>" id="cat_<?php echo h($cat['idCategoria']); ?>" <?php echo in_array($cat['nombre'], $catsFiltro) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="cat_<?php echo h($cat['idCategoria']); ?>">
                                    <?php echo h($cat['nombre']); ?>
                                </label>
                            </div>
                            
                            <!-- Sub Categorias de esta categoria -->
                            <?php if(!empty($subcategoriasPorCategoria[$cat['idCategoria']])): ?>
                                <?php foreach($subcategoriasPorCategoria[$cat['idCategoria']] as $subcat): ?>
                                    <div class="form-check ms-3">
                                        <input class="form-check-input filter-checkbox" type="checkbox" name="subcat[]" value="<?php echo h($subcat['nombre']); ?>" id="subcat_<?php echo h($subcat['idSubcategoria']); ?>" <?php echo in_array($subcat['nombre'], $subcatsFiltro) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" style="font-size: 0.85em;" for="subcat_<?php echo h($subcat['idSubcategoria']); ?>">
                                            <?php echo h($subcat['nombre']); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Preço  -->
                        <div class="col-12 mt-3">
                            <span class="fw-bold"> Preço </span>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="row g-1">
                                <div class="col-5">
                                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="<?php echo $minPrice !== null ? $minPrice : ''; ?>">
                                </div>
                                <div class="col-2 text-center">-</div>
                                <div class="col-5">
                                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="<?php echo $maxPrice !== null ? $maxPrice : ''; ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-sm w-100 bg-light border mt-2"> Aplicar Preço </button>
                        </div>
                        
                        <!-- Cor -->
                        <?php if (!empty($coloresDisponibles)): ?>
                        <div class="col-12 mt-3">
                            <span class="fw-bold">Cor</span>
                        </div>
                        <?php foreach($coloresDisponibles as $cor): ?>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input filter-checkbox" type="checkbox" name="cor[]" value="<?php echo h($cor); ?>" id="cor_<?php echo h(md5($cor)); ?>" <?php echo in_array($cor, $corFiltro) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="cor_<?php echo h(md5($cor)); ?>">
                                    <?php echo h($cor); ?>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <!-- Tamanho -->
                        <?php if (!empty($tamanosDisponibles)): ?>
                        <div class="col-12 mt-3">
                            <span class="fw-bold">Tamanho</span>
                        </div>
                        <?php foreach($tamanosDisponibles as $tam): ?>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input filter-checkbox" type="checkbox" name="tamanho[]" value="<?php echo h($tam); ?>" id="tam_<?php echo h(md5($tam)); ?>" <?php echo in_array($tam, $tamanhoFiltro) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="tam_<?php echo h(md5($tam)); ?>">
                                    <?php echo h($tam); ?>
                                </label>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        
                        <div class="col-12 mt-4">
                            <a href="Tienda.php<?php echo $esTiendaEspecifica ? '?token='.h($_GET['token']) : ''; ?>" class="btn w-100 btn-outline-danger"> Limpar Filtros </a>
                        </div>
                        
                                <input type="hidden" name="sort" id="sortHidden" value="<?php echo h($sort); ?>">
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Produtos -->
                <div class="col-12 col-md-8 col-lg-9 ms-md-4">
                    <div class="row">
                        <!-- Buscador -->
                        <div class="col-12 col-md-5 tienda__buscador">
                            <input id="tienda__buscador--input_main" type="text" class="form-control" placeholder="Buscar produto..." value="<?php echo h($q); ?>">
                            <span class="tienda_buscador--icono" style="cursor:pointer;" onclick="aplicarBusqueda()"> <i class="fa-solid fa-magnifying-glass"></i> </span>
                        </div>
                        
                        <!-- Ordem dos Produtos -->
                        <div class="col-12 col-md-7 text-end mt-3 mt-md-0 d-flex justify-content-end align-items-center flex-wrap">
                            <span class="me-2 text-muted"><small>Ordenar por:</small></span>
                            <span class="btn <?php echo $sort == 'newest' ? 'btn-dark' : 'btn-light border'; ?> m-1 sort-btn" data-sort="newest"><small> Novos <?php if($sort=='newest') echo '<i class="fa-solid fa-check"></i>'; ?> </small></span>
                            <span class="btn <?php echo $sort == 'price_asc' ? 'btn-dark' : 'btn-light border'; ?> m-1 sort-btn" data-sort="price_asc"><small> Menor preço <?php if($sort=='price_asc') echo '<i class="fa-solid fa-check"></i>'; ?> </small></span>
                            <span class="btn <?php echo $sort == 'price_desc' ? 'btn-dark' : 'btn-light border'; ?> m-1 sort-btn" data-sort="price_desc"><small>Maior Preço <?php if($sort=='price_desc') echo '<i class="fa-solid fa-check"></i>'; ?> </small></span>
                        </div>
                    </div>
                    
                    <!-- Cars Produtos -->
                    <div class="row mt-4">
                        <?php if(empty($productos)): ?>
                            <div class="col-12 text-center py-5">
                                <h3 class="text-muted">Nenhum produto encontrado</h3>
                                <p>Tente ajustar seus termos de busca ou filtros.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach($productos as $prod): ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                                <div class="card h-100">
                                    <img class="card-img-top p-3" style="height: 250px; object-fit: cover;"
                                        src="<?php echo h($prod['imagen_principal']); ?>" alt="<?php echo h($prod['titulo']); ?>" />
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title text-center" style="font-size: 1.1rem; height: 2.5rem; overflow: hidden;"><?php echo h($prod['titulo']); ?></h5>
                                        <?php if(!$esTiendaEspecifica): ?>
                                            <p class="text-center text-muted small mb-2"><i class="fa-solid fa-store"></i> <?php echo h($prod['emprendimiento_nome']); ?></p>
                                        <?php endif; ?>
                                        <div class="d-flex align-items-center mt-auto pt-3 border-top">
                                            <span class="text-success"><small> <b> R$ <?php echo number_format($prod['precio'], 2, ',', '.'); ?> </b> </small></span>
                                            
                                            <!-- Data attributes para pasar info al modal en JS -->
                                            <button class="btn btn--vermelho ms-auto btn-ver-detalhe" 
                                                data-id="<?php echo $prod['idProducto']; ?>"
                                                data-titulo="<?php echo h($prod['titulo']); ?>"
                                                data-precio="<?php echo h($prod['precio']); ?>"
                                                data-desc="<?php echo h($prod['descripcion']); ?>"
                                                data-tam="<?php echo h($prod['tamano']); ?>"
                                                data-cor="<?php echo h($prod['color']); ?>"
                                                data-img="<?php echo h($prod['imagen_principal']); ?>"
                                                data-imagenes="<?php echo htmlspecialchars(json_encode($prod['imagenes']), ENT_QUOTES, 'UTF-8'); ?>"
                                                data-emp="<?php echo h($prod['emprendimiento_nome']); ?>"
                                                data-cel="<?php echo h($esTiendaEspecifica ? $celular : ''); ?>" 
                                                data-bs-toggle="modal" data-bs-target="#modalDetalheProduto">
                                                Comprar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Modal para Produto -->
            <div class="modal fade" id="modalDetalheProduto" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
                role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold" id="modalTitleId"> Detalhes do Produto </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="row">
                                    <div class="col-md-6 mb-3 text-center">
                                        <img src="" id="modalDetalheImagemPrincipal" class="img-fluid rounded shadow-sm mb-2" style="max-height: 400px; object-fit: contain; width: 100%;">
                                        <div id="modalDetalheMiniaturas" class="d-flex justify-content-center gap-2 flex-wrap mt-2">
                                            <!-- Miniaturas se inyectan aquí -->
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h2 id="modalDetalheTitulo" class="fw-bold mb-2"></h2>
                                        <p id="modalDetalheEmpresa" class="text-muted small mb-3"><i class="fa-solid fa-store"></i> <span></span></p>
                                        
                                        <h3 id="modalDetalhePreco" class="text-success fw-bold mb-3"></h3>
                                        
                                        <p id="modalDetalheDescricao" class="mb-4 text-secondary"></p>
                                        
                                        <ul class="list-group list-group-flush mb-4">
                                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                                                <strong class="text-muted">Cor:</strong>
                                                <span id="modalDetalheCor"></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                                                <strong class="text-muted">Tamanho:</strong>
                                                <span id="modalDetalheTamanho"></span>
                                            </li>
                                        </ul>

                                        <a href="#" id="modalDetalheBtnWhatsapp" target="_blank" class="btn btn-success btn-lg w-100">
                                            <i class="fa-brands fa-whatsapp"></i> Solicitar por WhatsApp
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    
    <footer class="mt-5 footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-6 text-center mb-3">
                    <a href="#navBar">
                        <img width="80px" src="<?php echo $logoTienda; ?>"
                            alt="logo do da empresa/empreendimento">
                    </a>
                    <h3 class="footer__titulo"><?php echo $tituloTienda; ?></h3>
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
                        <li> Telefone: <?php echo $telefono; ?> </li>
                        <li> Horarios: <?php echo $horarios; ?> </li>
                        <li> WhatsApp: <?php echo $celular; ?> </li>
                    </ul>
                </div>
                <div class="col-md-2 col-6 mb-3">
                    <h4 class="footer__subTitulo"> Endereço </h4>
                    <ul class="footer__texto list-unstyled">
                        <li> <?php echo $direccion; ?> </li>
                    </ul>
                </div>
                <div class="col-md-3 col-12 text-center">
                    <h4 class="footer__subTitulo"> Redes Sociais </h4>
                    <div class="d-flex justify-content-center gap-3 mt-2">
                        <?php if ($facebook != '#'): ?>
                            <a href="<?php echo $facebook; ?>" target="_blank" class="fs-2 text-primary">
                                <i class="fa-brands fa-facebook"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($instagram != '#'): ?>
                            <a href="<?php echo $instagram; ?>" target="_blank" class="fs-2 text-danger">
                                <i class="fa-brands fa-square-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if ($celular != '-'): ?>
                        <a href="<?php echo $whatsappLink; ?>" target="_blank" class="fs-2 text-success">
                            <i class="fa-brands fa-square-whatsapp"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Checkboxes envían form automáticamente
            const checkboxes = document.querySelectorAll('.filter-checkbox');
            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    document.getElementById('formFiltros').submit();
                });
            });

            // Botones de orden
            const sortBtns = document.querySelectorAll('.sort-btn');
            sortBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    document.getElementById('sortHidden').value = btn.dataset.sort;
                    document.getElementById('formFiltros').submit();
                });
            });

            // Lógica del modal de detalles
            const modalBtns = document.querySelectorAll('.btn-ver-detalhe');
            const tituloGlobalTienda = "<?php echo $tituloTienda; ?>";
            const isGlobalTienda = <?php echo $esTiendaEspecifica ? 'false' : 'true'; ?>;
            const globalCelular = "<?php echo $celular; ?>";

            modalBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    document.getElementById('modalDetalheTitulo').textContent = this.dataset.titulo;
                    document.getElementById('modalDetalhePreco').textContent = 'R$ ' + parseFloat(this.dataset.precio).toFixed(2).replace('.', ',');
                    document.getElementById('modalDetalheDescricao').textContent = this.dataset.desc;
                    document.getElementById('modalDetalheCor').textContent = this.dataset.cor || 'N/A';
                    document.getElementById('modalDetalheTamanho').textContent = this.dataset.tam || 'N/A';
                    
                    // Manejo de Imágenes (Principal + Miniaturas)
                    const containerMiniaturas = document.getElementById('modalDetalheMiniaturas');
                    containerMiniaturas.innerHTML = '';
                    const imgPrincipalEl = document.getElementById('modalDetalheImagemPrincipal');
                    
                    if (this.dataset.imagenes) {
                        try {
                            const imagenes = JSON.parse(this.dataset.imagenes);
                            if (imagenes && imagenes.length > 0) {
                                imgPrincipalEl.src = imagenes[0].caminho_imagem || this.dataset.img;
                                
                                if (imagenes.length > 1) {
                                    imagenes.forEach(img => {
                                        const srcImg = img.caminho_imagem || this.dataset.img;
                                        const imgEl = document.createElement('img');
                                        imgEl.src = srcImg;
                                        imgEl.className = 'img-thumbnail border';
                                        imgEl.style.width = '70px';
                                        imgEl.style.height = '70px';
                                        imgEl.style.objectFit = 'cover';
                                        imgEl.style.cursor = 'pointer';
                                        imgEl.onclick = function() {
                                            imgPrincipalEl.src = this.src;
                                        };
                                        containerMiniaturas.appendChild(imgEl);
                                    });
                                }
                            } else {
                                imgPrincipalEl.src = this.dataset.img;
                            }
                        } catch(e) {
                            imgPrincipalEl.src = this.dataset.img;
                        }
                    } else {
                        imgPrincipalEl.src = this.dataset.img;
                    }

                    const empresaNome = this.dataset.emp || tituloGlobalTienda;
                    document.querySelector('#modalDetalheEmpresa span').textContent = empresaNome;
                    
                    const celularUsar = this.dataset.cel || globalCelular;
                    
                    if(celularUsar && celularUsar !== '-') {
                        const texto = `Hola! Estoy interesado en el producto: ${this.dataset.titulo} (${this.dataset.cor}, ${this.dataset.tam}) de la tienda ${empresaNome}. ¿Sigue disponible?`;
                        document.getElementById('modalDetalheBtnWhatsapp').href = `https://api.whatsapp.com/send?phone=${celularUsar}&text=${encodeURIComponent(texto)}`;
                        document.getElementById('modalDetalheBtnWhatsapp').style.display = 'block';
                    } else {
                        document.getElementById('modalDetalheBtnWhatsapp').style.display = 'none';
                    }
                });
            });
        });

        function aplicarBusqueda() {
            const val = document.getElementById('tienda__buscador--input_main').value;
            let hiddenInput = document.getElementById('tienda__buscador--input_hidden');
            if(!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'q';
                hiddenInput.id = 'tienda__buscador--input_hidden';
                document.getElementById('formFiltros').appendChild(hiddenInput);
            }
            hiddenInput.value = val;
            document.getElementById('formFiltros').submit();
        }

        let typingTimer;
        const doneTypingInterval = 800; // 800ms de espera antes de buscar
        const searchInput = document.getElementById('tienda__buscador--input_main');

        searchInput.addEventListener('input', function() {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(aplicarBusqueda, doneTypingInterval);
        });

        searchInput.addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(typingTimer);
                aplicarBusqueda();
            }
        });

        // Restaurar foco al final del texto si hay una búsqueda activa
        if (searchInput.value) {
            searchInput.focus();
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.value = val;
        }
    </script>

</body>
</html>