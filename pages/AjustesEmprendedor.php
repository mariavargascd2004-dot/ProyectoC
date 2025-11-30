<?php
session_start();

$idUsuario = $_SESSION["user"]['id'] ?? null;
$tipoUsuario = $_SESSION["user"]['tipo'] ?? null;

if (!$idUsuario || $tipoUsuario !== 'associado') {
    header("location:../");
    exit;
}

if (!isset($_GET["token"]) || empty($_GET["token"])) {
    header("location:../");
    exit;
}

$idEmprendimientoDecoded = base64_decode($_GET['token'], true);

if ($idEmprendimientoDecoded === false || !filter_var($idEmprendimientoDecoded, FILTER_VALIDATE_INT)) {
    header("location:../");
    exit;
}

$idEmprendimiento = intval($idEmprendimientoDecoded);

// 2. Carga de Helpers y Datos
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

    if (!$emprendimiento || $emprendimiento['adminAssociado_idUsuario'] != $idUsuario) {
        header("location:../");
        exit;
    }

    $infoAssociado = $assHelper->obtenerAssociadoPorId($idUsuario);
    $imgemsFabricacao = $FabricacaoHelper->obterImagemsComIdEmprendimento($idEmprendimiento);
    $imgemsGaleria  = $GaleriaHelper->obterImagemsComIdEmprendimento($idEmprendimiento);
} catch (Exception $e) {
    error_log("Error en AjustesEmprendedor.php: " . $e->getMessage());
    header("location:../error.php");
    exit;
}

function h($string)
{
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <title>Ajustes - <?php echo h($emprendimiento['nome']); ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">

    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Alegreya+SC:wght@400;700;900&family=Work+Sans:wght@300;400;500;700&display=swap"
        rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/stylePrincipal.css">
    <link rel="stylesheet" href="../assets/css/styleMenuEmprendedores.css">
</head>

<body class="bg-light">
    <style>
        :root {
            /*Cores*/
            --cor-primaria: <?php echo h($emprendimiento['corPrincipal']) ?>;
            --cor-secundaria: <?php echo h($emprendimiento['corSecundaria']) ?>;
        }
    </style>
    <header>
        <nav class="navbar navbar-expand navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <div class="navbar-nav d-flex align-items-center">
                    
         <a href="Emprendimento.php?token=<?php echo h($_GET['token']); ?>" class="text-decoration-none">
                <img src="../<?php echo h($emprendimiento['logo']); ?>" alt="Logo <?php echo h($emprendimiento['nome']); ?>" width="60" class="me-3">
            </a>
                   
                        <a class="nav-item nav-link titulo fs-4" href="Emprendimento.php?token=<?php echo h($_GET['token']); ?>">
                        <i class="fa-solid fa-arrow-left me-2"></i> Voltar para <?php echo h($emprendimiento['nome']); ?>
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container mt-4 mb-5">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3">Ajustes Gerais do Empreendimento</h1>
                </div>
            </div>
            
            <form id="formIdentidadeVisual" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="actualizarImagensPrincipais">
                <input type="hidden" name="idEmprendimento" value="<?php echo $idEmprendimiento; ?>">

                <div class="row contornoGris p-4 mb-4 shadow-sm">
                    <div class="col-12 mb-4">
                        <h2 class="subTitulo h5"><i class="fa-solid fa-id-card-clip me-2"></i>Identidade Visual</h2>
                    </div>

                    <div class="col-md-4 text-center mb-3">
                        <label class="form-label fw-bold d-block">Logotipo Atual</label>
                        <div class="mb-2 p-2 border rounded bg-white d-inline-block">
                            <img id="imgLogoActual" 
                                 src="../<?php echo h($emprendimiento['logo'] ?? 'assets/img/logos/default.png'); ?>" 
                                 alt="Logo Atual" 
                                 class="img-fluid" 
                                 style="max-height: 150px; object-fit: contain;">
                        </div>
                        <div class="mt-2">
                            <label for="inputLogo" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fa-solid fa-upload me-1"></i> Alterar Logo
                            </label>
                            <input type="file" id="inputLogo" name="logoEmprendimento" class="d-none" accept="image/*">
                            <div id="previewLogo" class="mt-2 small text-success fst-italic"></div>
                        </div>
                    </div>

                    <div class="col-md-8 text-center mb-3">
                        <label class="form-label fw-bold d-block">Pôster (Capa) Atual</label>
                        <div class="mb-2 p-2 border rounded bg-white d-inline-block w-100">
                            <img id="imgPoosterActual" 
                                 src="../<?php echo h($emprendimiento['pooster'] ?? 'assets/img/poosters/default.png'); ?>" 
                                 alt="Pooster Atual" 
                                 class="img-fluid" 
                                 style="max-height: 250px; width: 100%; object-fit: cover;">
                        </div>
                        <div class="mt-2">
                            <label for="inputPooster" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-image me-1"></i> Alterar Pôster
                            </label>
                            <input type="file" id="inputPooster" name="poosterEmprendimento" class="d-none" accept="image/*">
                            <div id="previewPooster" class="mt-2 small text-success fst-italic"></div>
                        </div>
                    </div>

                    <div class="col-12 text-end mt-3 border-top pt-3">
                        <button type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Atualizar Imagens
                        </button>
                    </div>
                </div>
            </form>            
            <script>
                function previewImage(input, targetId) {
                    const target = document.getElementById(targetId);
                    if (input.files && input.files[0]) {
                        target.textContent = "Arquivo selecionado: " + input.files[0].name;
                    } else {
                        target.textContent = "";
                    }
                }
            </script>

            <!-- ================= HISTÓRIA ================= -->
            <form id="formHistoria" action="../controllers/EmprendimentoController.php" method="POST">
                <input type="hidden" name="accion" value="actualizarHistoria">
                <input type="hidden" name="idEmprendimento" value="<?php echo $idEmprendimiento; ?>">
                <div class="row contornoGris p-4 mb-4 shadow-sm">
                    <div class="col-12 mb-3">
                        <h2 class="subTitulo h5"><i class="fa-solid fa-book-open me-2"></i>História</h2>
                        <textarea class="form-control mt-3" name="historia" rows="5" required><?php echo h($emprendimiento['historia']); ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>

            <!-- ================= PROCESSO DE FABRICAÇÃO (TEXTO) ================= -->
            <form id="formProcesso" action="../controllers/EmprendimentoController.php" method="POST">
                <input type="hidden" name="accion" value="actualizarProcesso">
                <input type="hidden" name="idEmprendimento" value="<?php echo $idEmprendimiento; ?>">
                <div class="row contornoGris p-4 mb-4 shadow-sm">
                    <div class="col-12 mb-3">
                        <h2 class="subTitulo h5"><i class="fa-solid fa-gears me-2"></i>Processo de fabricação</h2>
                        <textarea class="form-control mt-3" name="processoFabricacao" rows="5" required><?php echo h($emprendimiento['processoFabricacao']); ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Alterações
                        </button>
                    </div>
                </div>
            </form>

            <!-- ================= CORES ================= -->
            <form id="formCores" action="../controllers/EmprendimentoController.php" method="POST">
                <input type="hidden" name="accion" value="actualizarCores">
                <input type="hidden" name="idEmprendimento" value="<?php echo $idEmprendimiento; ?>">
                <div class="row contornoGris p-4 mb-4 shadow-sm">
                    <div class="col-12 mb-3">
                        <h2 class="subTitulo h5"><i class="fa-solid fa-palette me-2"></i>Cores do Empreendimento</h2>
                        <p class="text-muted small">Estas cores serão usadas no design da página do seu empreendimento. <b>(recomenda-se usar cores opostas)</b> </p>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="corPrincipal" class="form-label fw-bold">Cor Principal</label>
                        <div class="d-flex align-items-center">
                            <input type="color" class="form-control form-control-color" id="corPrincipal" name="corPrincipal" value="<?php echo h($emprendimiento['corPrincipal']); ?>" title="Escolha a cor principal">
                            <span class="ms-3" id="valorCorPrincipal"><?php echo h($emprendimiento['corPrincipal']); ?></span>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="corSecundaria" class="form-label fw-bold">Cor Secundária</label>
                        <div class="d-flex align-items-center">
                            <input type="color" class="form-control form-control-color" id="corSecundaria" name="corSecundaria" value="<?php echo h($emprendimiento['corSecundaria']); ?>" title="Escolha a cor secundária">
                            <span class="ms-3" id="valorCorSecundaria"><?php echo h($emprendimiento['corSecundaria']); ?></span>
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Cores
                        </button>
                    </div>
                </div>
            </form>

            <!-- ================= IMAGENS DE FABRICAÇÃO ================= -->
            <div class="row contornoGris p-4 mb-4 shadow-sm">
                <div class="col-12 mb-4">
                    <h2 class="subTitulo h5"><i class="fa-solid fa-images me-2"></i>Imagens de fabricação</h2>
                    <p class="text-muted small">Máximo 4 imagens.</p>
                </div>

                <!-- Previews de imagens existentes -->
                <div class="col-12 d-flex flex-wrap gap-3 mb-4" id="containerImagensFabricacao">
                    <?php if (empty($imgemsFabricacao)): ?>
                        <p class="text-muted fst-italic">Nenhuma imagem de fabricação cadastrada.</p>
                    <?php else: ?>
                        <?php foreach ($imgemsFabricacao as $img): ?>
                            <div class="border rounded position-relative img-preview-container shadow-sm">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                    onclick="eliminarImagem(this, 'fabricacao', <?php echo $img['idImagem'] ?? 0; ?>)"
                                    title="Eliminar imagem">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <img src="../<?php echo h($img['caminho_imagem']); ?>" alt="Imagem fabricação">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <hr>
                    <h6 class="mb-3">Adicionar novas imagens:</h6>
                    <form id="formImgFabricacao" action="../controllers/EmprendimentoController.php" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center flex-wrap">
                        <input type="hidden" name="accion" value="subirImagensFabricacao">
                        <input type="hidden" name="idEmprendimento" value="<?php echo $idEmprendimiento; ?>">

                        <input type="file" class="form-control w-auto" name="novasImagensFabricacao[]" multiple accept="image/*">
                        <button type="submit" class="btn btn--verde">
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> Enviar Imagens
                        </button>
                    </form>
                </div>
            </div>

            <!-- ================= GALERIA DE FOTOS ================= -->
            <div class="row contornoGris p-4 mb-4 shadow-sm">
                <div class="col-12 mb-4">
                    <h2 class="subTitulo h5"><i class="fa-solid fa-camera-retro me-2"></i>Imagens da Galeria</h2>
                    <p class="text-muted small">Máximo 10 imagens.</p>
                </div>

                <!-- Previews de imagens existentes -->
                <div class="col-12 d-flex flex-wrap gap-3 mb-4" id="containerImagensGaleria">
                    <?php if (empty($imgemsGaleria)): ?>
                        <p class="text-muted fst-italic">Nenhuma imagem na galeria.</p>
                    <?php else: ?>
                        <?php foreach ($imgemsGaleria as $img): ?>
                            <div class="border rounded position-relative img-preview-container shadow-sm">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                    onclick="eliminarImagem(this, 'galeria', <?php echo $img['idImagem'] ?? 0; ?>)"
                                    title="Eliminar imagem">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                                <img src="../<?php echo h($img['caminho_imagem']); ?>" alt="Imagem galeria">
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="col-12">
                    <hr>
                    <h6 class="mb-3">Adicionar novas imagens:</h6>
                    <form id="formImgGaleria" action="../controllers/EmprendimentoController.php" method="POST" enctype="multipart/form-data" class="d-flex gap-2 align-items-center flex-wrap">
                        <input type="hidden" name="accion" value="subirImagensGaleria">
                        <input type="hidden" name="idEmprendimento" value="<?php echo $idEmprendimiento; ?>">

                        <input type="file" class="form-control w-auto" name="novasImagensGaleria[]" multiple accept="image/*">
                        <button type="submit" class="btn btn--verde">
                            <i class="fa-solid fa-cloud-arrow-up me-2"></i> Enviar Imagens
                        </button>
                    </form>
                </div>
            </div>

            <!-- ================= INFORMAÇÕES DO EMPREENDIMENTO ================= -->
            <form id="formInfoEmprendimento" action="../controllers/EmprendimentoController.php" method="POST">
                <input type="hidden" name="accion" value="actualizarInfoEmprendimento">
                <input type="hidden" name="idEmprendimento" value="<?php echo $idEmprendimiento; ?>">

                <div class="row contornoGris p-4 mb-4 shadow-sm">
                    <h2 class="subTitulo h5 mb-4"><i class="fa-solid fa-shop me-2"></i>Informações de Contato e Localização</h2>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Telefone</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                            <input type="text" class="form-control" name="telefone" value="<?php echo h($emprendimiento['telefone']); ?>" placeholder="Ex: (11) 3333-4444">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">WhatsApp / Celular *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-whatsapp"></i></span>
                            <input type="number" class="form-control" name="celular" value="<?php echo h($emprendimiento['celular']); ?>" required placeholder="Ex: 551199999999  (BR) / 59899999999 (UY) ">
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Endereço Completo *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-solid fa-location-dot"></i></span>
                            <input type="text" class="form-control" name="ubicacao" value="<?php echo h($emprendimiento['ubicacao']); ?>" required placeholder="Rua, Número, Bairro, Cidade - UF">
                        </div>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label fw-bold">Horários de Atendimento *</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-regular fa-clock"></i></span>
                            <input type="text" class="form-control" name="horarios" value="<?php echo h($emprendimiento['horarios']); ?>" required placeholder="Ex: Seg a Sex das 9h às 18h">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Facebook (Link)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-facebook"></i></span>
                            <input type="url" class="form-control" name="facebook" value="<?php echo h($emprendimiento['facebook']); ?>" placeholder="https://facebook.com/sua-pagina">
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Instagram (Link)</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fa-brands fa-instagram"></i></span>
                            <input type="url" class="form-control" name="instagram" value="<?php echo h($emprendimiento['instagram']); ?>" placeholder="https://instagram.com/seu-perfil">
                        </div>
                    </div>

                    <div class="col-12 text-end mt-3">
                        <button type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Informações
                        </button>
                    </div>
                </div>
            </form>

            <!-- Para categorias y Subcategorias -->
            <div id="gerenciadorCategorias" class="row contornoGris p-4 mb-4 shadow-sm">
                <h2 class="subTitulo h5 mb-4"><i class="fa-solid fa-tags me-2"></i>Gerenciar Categorias e Subcategorias</h2>
                <input type="hidden" id="idEmprendimentoAtual" value="<?php echo $idEmprendimiento; ?>">

                <div class="col-md-6">
                    <h5 class="fw-bold">Categorias</h5>
                    <div class="card">
                        <div class="card-body">
                            <form id="formNovaCategoria" class="d-flex gap-2 mb-3">
                                <input type="text" id="inputNomeCategoria" class="form-control" placeholder="Nova Categoria" required>
                                <button type="submit" class="btn btn--verde flex-shrink-0">
                                    <i class="fa-solid fa-plus"></i> Adicionar
                                </button>
                            </form>
                            <hr>
                            <ul id="listaCategorias" class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                                <li class="list-group-item text-center p-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <h5 class="fw-bold">Subcategorias de: <span id="nomeCategoriaAtiva" class="text-primary">---</span></h5>
                    <div class="card">
                        <div class="card-body">
                            <form id="formNovaSubcategoria" class="d-flex gap-2 mb-3" style="display: none!important;">
                                <input type="hidden" id="idCategoriaAtiva">
                                <input type="text" id="inputNomeSubcategoria" class="form-control" placeholder="Nova Subcategoria" required>
                                <button type="submit" class="btn btn--verde flex-shrink-0">
                                    <i class="fa-solid fa-plus"></i> Adicionar
                                </button>
                            </form>
                            <div id="msgSelecioneCategoria" class="text-center p-3 text-muted">
                                <i class="fa-solid fa-arrow-left me-2"></i> Selecione uma categoria para ver suas subcategorias.
                            </div>
                            <hr id="hrSubcategorias" style="display: none!important;">
                            <ul id="listaSubcategorias" class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= INFORMAÇÕES DO ADMINISTRADOR ================= -->
            <form id="formInfoAdmin" action="../controllers/AdminAssociadoController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="actualizarPerfil">
                <input type="hidden" name="idUsuario" value="<?php echo $idUsuario; ?>">

                <div class="row contornoGris p-4 mb-4 shadow-sm">
                    <h2 class="subTitulo h5 mb-4"><i class="fa-solid fa-user-tie me-2"></i>Informações do Administrador</h2>

                    <div class="col-md-3 mb-4 text-center">
                        <!-- Preview de la foto de perfil actual -->
                        <div class="mb-2">
                            <img src="<?php echo h($infoAssociado['FotoPerfilAssociado'] ?? '../assets/img/perfil/default.png'); ?>"
                                alt="Foto de perfil"
                                class="rounded-circle img-thumbnail"
                                style="width: 150px; height: 150px; object-fit: cover;">
                        </div>
                        <label for="fotoPerfilInput" class="btn btn-sm btn-outline-primary">
                            <i class="fa-solid fa-camera"></i> Alterar Foto
                        </label>
                        <input type="file" id="fotoPerfilInput" name="fotoPerfil" class="d-none" accept="image/*">
                    </div>

                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nome *</label>
                                <input type="text" class="form-control" name="nombre" value="<?php echo h($infoAssociado['NombreAssociado']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Sobrenome *</label>
                                <input type="text" class="form-control" name="apellido" value="<?php echo h($infoAssociado['ApellidoAssociado']); ?>" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Mini Biografia *</label>
                                <textarea class="form-control" name="descripcion" rows="4" required placeholder="Conte um pouco sobre você..."><?php echo h($infoAssociado['DescripcionAssociado']); ?></textarea>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn--amarelo">
                                <i class="fa-solid fa-floppy-disk me-2"></i> Salvar Meu Perfil
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- ================= ELIMINAR CONTA (ZONA DE PELIGRO) ================= -->
            <div class="row contornoGris p-4 mb-3 border-danger shadow-sm" style="background-color: #fff5f5;">
                <div class="col-12">
                    <h2 class="subTitulo text-danger h5"><i class="fa-solid fa-triangle-exclamation me-2"></i>Zona de Perigo</h2>
                    <div class="alert alert-danger mt-3">
                        <strong>Atenção:</strong> Esta ação é irreversível. Ao eliminar sua conta, todos os dados do seu empreendimento, imagens e informações pessoais serão apagados permanentemente do nosso sistema.
                    </div>
                    <form id="formEliminarConta" action="../controllers/UsuarioController.php" method="POST">
                        <input type="hidden" name="accion" value="eliminarConta">
                        <input type="hidden" name="idUsuario" value="<?php echo $idUsuario; ?>">

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="checkConfirma" required>
                            <label class="form-check-label fw-bold text-danger" for="checkConfirma">
                                Estou ciente e confirmo que desejo eliminar minha conta permanentemente.
                            </label>
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg" id="btnEliminarConta" disabled>
                            <i class="fa-solid fa-trash-can me-2"></i> ELIMINAR CONTA DEFINITIVAMENTE
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
        crossorigin="anonymous"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>

    <script src="../assets/js/AjustesMenuEmprendedores.js"></script>
</body>

</html>