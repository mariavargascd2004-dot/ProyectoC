<?php
session_start();

if (!isset($_SESSION["user"])) {
    header("location:../");
} else {
    if ($_SESSION["user"]["tipo"] != "adminGeneral") {
        header("location:../");
    }
}

// Cargar datos de la página principal
require_once '../controllers/PaginaPrincipalController.php';
$controller = new PaginaPrincipalController();
$datos = $controller->obtenerDatos();
?>
<!doctype html>
<html lang="br">

<head>
    <title>Pagina Inicial</title>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS v5.2.1 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <!-- FontaWesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
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
        href="https://fonts.googleapis.com/css2?family=Alegreya+SC:ital,wght@0,400;0,500;0,700;0,800;0,900;1,400;1,500;1,700;1,800;1,900&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- Fonte Lora -->
    <link
        href="https://fonts.googleapis.com/css2?family=Alegreya+SC:ital,wght@0,400;0,500;0,700;0,800;0,900;1,400;1,500;1,700;1,800;1,900&family=Lora:ital,wght@0,400..700;1,400..700&family=Work+Sans:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">

    <!-- My CSS -->
    <link rel="stylesheet" href="../assets/css/stylePrincipal.css">
</head>

<body>

    <header>
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <!-- Logo y enlaces -->
                <div class="navbar-nav d-flex align-items-center">
                    <img src="../assets/img/CasaSolidaria/defaultLogo.png" alt="Logo de la Empresa" width="80px">
                    <a class="nav-item nav-link titulo" href="../">Loja Solidaria</a>
                </div>
            </div>
        </nav>

    </header>
    <main>
        <div class="container mt-3 mb-5">

            <!-- ================= HISTORIA ================= -->
            <form id="formHistoria" method="POST" action="../controllers/PaginaPrincipalController.php">
                <input type="hidden" name="action" value="actualizar_historia">
                <div class="row contornoGris p-3 mb-3">
                    <div class="col-12">
                        <h2 class="subTitulo text-uppercase">História</h2>
                        <textarea require class="form-control parrafo fs-6" name="historia" rows="4"><?php echo htmlspecialchars($datos ? $datos->getHistoria() : ''); ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button disabled type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-pen-to-square"></i> Atualizar História
                        </button>
                    </div>
                </div>
            </form>

            <!-- ================= PORTADA ================= -->
            <form id="formPortada" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="actualizar_portada">
                <div class="row contornoGris p-3 mb-3 align-items-center">
                    <div class="col-md-8">
                        <h2 class="subTitulo text-uppercase">Portada</h2>
                        <input type="file" name="portada" class="form-control mt-2" accept="image/*">
                    </div>
                    <div class="col-md-4 text-end">
                        <button disabled type="submit" class="btn btn--amarelo mt-4">
                            <i class="fa-solid fa-pen-to-square"></i> Atualizar Foto
                        </button>
                    </div>
                </div>
            </form>

            <!-- ================= MISSÃO ================= -->
            <form id="formMision" method="POST" action="../controllers/PaginaPrincipalController.php">
                <input type="hidden" name="action" value="actualizar_mision">
                <div class="row contornoGris p-3 mb-3">
                    <div class="col-12">
                        <h2 class="subTitulo text-uppercase">Missão</h2>
                        <textarea class="form-control parrafo fs-6" name="mision" rows="4"><?php echo htmlspecialchars($datos ? $datos->getMision() : ''); ?></textarea>
                    </div>
                    <div class="col-12 text-end mb-3">
                        <button disabled type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-pen-to-square"></i> Atualizar Missão
                        </button>
                    </div>
            </form>

            <!-- Galería de Fotos con vista previa -->
            <div class="col-12">
                <h5 class="text-uppercase mt-3">Galeria de Fotos (máx. 4)</h5>
                <div class="row mt-3">

                    <!-- FOTO 1 -->
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 1</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="1">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview1" accept="image/*">
                                <img id="preview1" class="img-fluid rounded" src="<?php echo $datos ? $datos->getPrimerafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 1">
                                <button disabled type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="1">
                                <button disabled type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>

                    <!-- FOTO 2 -->
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 2</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="2">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview2" accept="image/*">
                                <img id="preview2" class="img-fluid rounded" src="<?php echo $datos ? $datos->getSegundafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 2">
                                <button disabled type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="2">
                                <button disabled type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>

                    <!-- FOTO 3 -->
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 3</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="3">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview3" accept="image/*">
                                <img id="preview3" class="img-fluid rounded" src="<?php echo $datos ? $datos->getTercerafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 3">
                                <button disabled type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="3">
                                <button disabled type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>

                    <!-- FOTO 4 -->
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 4</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="4">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview4" accept="image/*">
                                <img id="preview4" class="img-fluid rounded" src="<?php echo $datos ? $datos->getCuartafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 4">
                                <button disabled type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="4">
                                <button disabled type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            <!-- ================= VISÃO ================= -->
            <form id="formVision" method="POST" action="../controllers/PaginaPrincipalController.php">
                <input type="hidden" name="action" value="actualizar_vision">
                <div class="row contornoGris p-3 mb-3">
                    <div class="col-12">
                        <h2 class="subTitulo text-uppercase">Visão</h2>
                        <textarea class="form-control parrafo fs-6" name="vision" rows="4"><?php echo htmlspecialchars($datos ? $datos->getVision() : ''); ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button disabled type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-pen-to-square"></i> Atualizar
                        </button>
                    </div>
                </div>
            </form>

            <!-- ================= INFORMAÇÃO DA EMPRESA ================= -->
            <form id="formInformacionEmpresa" method="POST" action="../controllers/PaginaPrincipalController.php">
                <input type="hidden" name="action" value="actualizar_informacion_empresa">
                <div class="row contornoGris p-3 mb-3">
                    <div class="col-12 mb-3">
                        <h2 class="subTitulo">Informação da Empresa</h2>
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="telefono" class="form-control" placeholder="Telefone" value="<?php echo htmlspecialchars($datos ? $datos->getTelefono() : ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="direccion" class="form-control" placeholder="Direção" value="<?php echo htmlspecialchars($datos ? $datos->getDireccion() : ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="horarios" class="form-control" placeholder="Horários" value="<?php echo htmlspecialchars($datos ? $datos->getHorarios() : ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="celular" class="form-control" placeholder="Celular / WhatsApp" value="<?php echo htmlspecialchars($datos ? $datos->getCelular() : ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="facebook" class="form-control" placeholder="Facebook" value="<?php echo htmlspecialchars($datos ? $datos->getFacebook() : ''); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <input type="text" name="instagram" class="form-control" placeholder="Instagram" value="<?php echo htmlspecialchars($datos ? $datos->getInstagram() : ''); ?>">
                    </div>
                    <div class="col-12 text-end">
                        <button disabled type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-floppy-disk"></i> Salvar Informação
                        </button>
                    </div>
                </div>
            </form>

            <!-- ================= ADMINISTRAR EMPREENDEDORES ================= -->
             <div class="row contornoGris p-3 mb-3">
                 <div class="col-12 mb-3">
                     <h2 class="subTitulo">Administrar Contas de Empreendedores</h2>
                 </div>
                 <div id="emprendimentosContainer"> </div>
             </div>
        </div>
    </main>

    <!-- Bootstrap JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>

    <!-- FontaWesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
        integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Sweet Alert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>

    <!-- My JS  -->
    <script src="../assets/js/AjustesCasaSolidaria.js"></script>
</body>

</html>