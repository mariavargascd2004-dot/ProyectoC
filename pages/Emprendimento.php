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

try {
    $empHelper = new EmprendimientosHelper();
    $assHelper = new adminsAssociadoHelper();
    $FabricacaoHelper = new imagemFabricacaoHelper();
    $GaleriaHelper = new imagemGaleriaHelper();

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

    $imgemsFabricacao = $FabricacaoHelper->obterImagemsComIdEmprendimento($idEmprendimiento);
    $imgemsGaleria  = $GaleriaHelper->obterImagemsComIdEmprendimento($idEmprendimiento);
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
                    class="fa-solid fa-gear"></i></a>
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

            <div class="row mt-5" id="Produtos">
                <div class="col-12 text-center">
                    <h3 class="subTitulo"> Produtos </h3>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <h4 class="parrafo"> Geleias </h4>
                </div>
            </div>
            <!-- EJEMPLO DE PRODUCTO ESTÁTICO -->
            <div class="row mt-2 produto">
                <div class="col-3">
                    <div class="card">
                        <img class="card-img-top" height="250px"
                            src="../assets/img/CasaSolidaria/defaultProduct.png" alt="Producto por defecto" />
                        <div class="card-body">
                            <h4 class="card-title text-center">Nome do produto</h4>
                            <div class="d-flex align-items-center mt-4">
                                <span><small> <b> R$ 99.99 </b> </small></span>
                                <a href="#" class="btn btn--vermelho ms-auto">Comprar</a>
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