<?php
session_start();

// 1. Verificación de seguridad
if (!isset($_SESSION["user"])) {
    header("location:../");
    exit;
} else {
    if ($_SESSION["user"]["tipo"] != "adminGeneral") {
        header("location:../");
        exit;
    }
}

// 2. Conexión y Controladores
require_once '../config/database.php';
require_once '../controllers/PaginaPrincipalController.php';
require_once '../models/modelsDAO/UsuarioDAO.php'; // Necesario para listar usuarios

$db = (new Database())->getConnection();
$controller = new PaginaPrincipalController();
$datos = $controller->obtenerDatos();

// 3. Obtener listas de usuarios para la gestión de contraseñas
// Obtener Associados
$stmtAssociados = $db->prepare("SELECT * FROM usuario WHERE tipo = 'associado' ORDER BY nombre ASC");
$stmtAssociados->execute();
$listaAssociados = $stmtAssociados->fetchAll(PDO::FETCH_ASSOC);

// Obtener Clientes
$stmtClientes = $db->prepare("SELECT * FROM usuario WHERE tipo = 'cliente' ORDER BY nombre ASC");
$stmtClientes->execute();
$listaClientes = $stmtClientes->fetchAll(PDO::FETCH_ASSOC);

// Datos del Admin logueado
$idAdmin = $_SESSION['user']['id'];
?>
<!doctype html>
<html lang="br">

<head>
    <title>Ajustes Gerais - Casa Solidaria</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+SC:wght@400;700&family=Work+Sans:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/stylePrincipal.css">
    
    <style>
        /* Estilos adicionales para las pestañas de administración */
        .nav-tabs .nav-link { color: #B2442E; font-weight: bold; }
        .nav-tabs .nav-link.active { background-color: #B2442E; color: #fff !important; border-color: #B2442E; }
        .table-hover tbody tr:hover { background-color: #f8f9fa; }
    </style>
</head>

<body>

    <header>
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <div class="navbar-nav d-flex align-items-center">
                    
                         <a class="nav-link p-0 me-2" href="../">
                        <img src="<?php echo htmlspecialchars($datos ? $datos->getLogo() : '../assets/img/CasaSolidaria/defaultLogo.png'); ?>" alt="Logo de la Empresa" width="80px">
                    </a>
                    <a class="nav-item nav-link titulo" href="../">Loja Solidaria</a>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container mt-3 mb-5">
            
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="text-center" style="color: #B2442E;">Painel de Ajustes Gerais</h1>
                </div>
            </div>

            <form id="formHistoria" method="POST" action="../controllers/PaginaPrincipalController.php">
                <input type="hidden" name="action" value="actualizar_historia">
                <div class="row contornoGris p-3 mb-3">
                    <div class="col-12">
                        <h2 class="subTitulo text-uppercase">História</h2>
                        <textarea required class="form-control parrafo fs-6" name="historia" rows="4"><?php echo htmlspecialchars($datos ? $datos->getHistoria() : ''); ?></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-pen-to-square"></i> Atualizar História
                        </button>
                    </div>
                </div>
            </form>

            <form id="formLogo" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="actualizar_logo">
                <div class="row contornoGris p-3 mb-3 align-items-center">
                    <div class="col-md-8">
                        <h2 class="subTitulo text-uppercase">Logo da Empresa</h2>
                        <input type="file" name="logo" class="form-control mt-2" accept="image/*">
                    </div>
                    <div class="col-md-4 text-center">
                        <img src="<?php echo htmlspecialchars($datos ? $datos->getLogo() : '../assets/img/CasaSolidaria/defaultLogo.png'); ?>" 
                             alt="Logo Actual" 
                             class="img-fluid mb-2" 
                             style="max-height: 80px;">
                        <div class="text-end">
                            <button type="submit" class="btn btn--amarelo mt-2">
                                <i class="fa-solid fa-pen-to-square"></i> Atualizar Logo
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <form id="formPortada" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="actualizar_portada">
                <div class="row contornoGris p-3 mb-3 align-items-center">
                    <div class="col-md-8">
                        <h2 class="subTitulo text-uppercase">Portada</h2>
                        <input type="file" name="portada" class="form-control mt-2" accept="image/*">
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="submit" class="btn btn--amarelo mt-4">
                            <i class="fa-solid fa-pen-to-square"></i> Atualizar Foto
                        </button>
                    </div>
                </div>
            </form>

            <form id="formMision" method="POST" action="../controllers/PaginaPrincipalController.php">
                <input type="hidden" name="action" value="actualizar_mision">
                <div class="row contornoGris p-3 mb-3">
                    <div class="col-12">
                        <h2 class="subTitulo text-uppercase">Missão</h2>
                        <textarea class="form-control parrafo fs-6" name="mision" rows="4"><?php echo htmlspecialchars($datos ? $datos->getMision() : ''); ?></textarea>
                    </div>
                    <div class="col-12 text-end mb-3">
                        <button type="submit" class="btn btn--amarelo">
                            <i class="fa-solid fa-pen-to-square"></i> Atualizar Missão
                        </button>
                    </div>
            </form>

            <div class="col-12">
                <h5 class="text-uppercase mt-3">Galeria de Fotos (máx. 4)</h5>
                <div class="row mt-3">
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 1</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="1">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview1" accept="image/*">
                                <img id="preview1" class="img-fluid rounded" src="<?php echo $datos ? $datos->getPrimerafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 1">
                                <button type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="1">
                                <button type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 2</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="2">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview2" accept="image/*">
                                <img id="preview2" class="img-fluid rounded" src="<?php echo $datos ? $datos->getSegundafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 2">
                                <button type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="2">
                                <button type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 3</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="3">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview3" accept="image/*">
                                <img id="preview3" class="img-fluid rounded" src="<?php echo $datos ? $datos->getTercerafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 3">
                                <button type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="3">
                                <button type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-6 col-md-3 mb-4 text-center">
                        <div class="border p-2 rounded">
                            <p class="small text-muted mb-1">Foto 4</p>
                            <form class="formFotoGaleria" method="POST" action="../controllers/PaginaPrincipalController.php" enctype="multipart/form-data">
                                <input type="hidden" name="action" value="actualizar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="4">
                                <input type="file" name="foto" class="form-control mb-2 foto-input" data-preview="preview4" accept="image/*">
                                <img id="preview4" class="img-fluid rounded" src="<?php echo $datos ? $datos->getCuartafotogaleria() : '../assets/img/CasaSolidaria/defaultLogo.png'; ?>" alt="Preview 4">
                                <button type="submit" class="btn btn-sm btn--amarelo mt-2 w-100">Actualizar</button>
                            </form>
                            <form class="formEliminarFoto mt-1" method="POST" action="../controllers/PaginaPrincipalController.php">
                                <input type="hidden" name="action" value="eliminar_foto_galeria">
                                <input type="hidden" name="numero_foto" value="4">
                                <button type="submit" class="btn btn-sm btn--vermelho w-100">Eliminar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form id="formVision" method="POST" action="../controllers/PaginaPrincipalController.php">
            <input type="hidden" name="action" value="actualizar_vision">
            <div class="row contornoGris p-3 mb-3">
                <div class="col-12">
                    <h2 class="subTitulo text-uppercase">Visão</h2>
                    <textarea class="form-control parrafo fs-6" name="vision" rows="4"><?php echo htmlspecialchars($datos ? $datos->getVision() : ''); ?></textarea>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn--amarelo">
                        <i class="fa-solid fa-pen-to-square"></i> Atualizar
                    </button>
                </div>
            </div>
        </form>

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
                    <button type="submit" class="btn btn--amarelo">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Informação
                    </button>
                </div>
            </div>
        </form>

        <div class="row contornoGris p-3 mb-3">
            <div class="col-12 mb-3 text-center">
                <h2 class="subTitulo">Gerenciamento de Contas e Senhas</h2>
                <p class="text-muted">Selecione a aba para visualizar e gerenciar as senhas de Clientes ou Empreendedores.</p>
            </div>
            
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin-pane" type="button" role="tab">
                            <i class="fa-solid fa-user-shield"></i> Meu Perfil (Admin)
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="associados-tab" data-bs-toggle="tab" data-bs-target="#associados-pane" type="button" role="tab">
                            <i class="fa-solid fa-store"></i> Empreendedores
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="clientes-tab" data-bs-toggle="tab" data-bs-target="#clientes-pane" type="button" role="tab">
                            <i class="fa-solid fa-users"></i> Clientes
                        </button>
                    </li>
                </ul>

                <div class="tab-content bg-white p-4 border border-top-0 rounded-bottom shadow-sm" id="myTabContent">
                    
                    <div class="tab-pane fade show active" id="admin-pane" role="tabpanel">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4>Alterar Minha Senha</h4>
                                <p class="text-muted">Mantenha sua conta segura alterando sua senha periodicamente.</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <button class="btn btn-danger btn-lg" onclick="alterarSenhaUsuario(<?php echo $idAdmin; ?>, 'adminGeneral')">
                                    <i class="fa-solid fa-key"></i> Alterar Minha Senha
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="associados-pane" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h5>Lista de Empreendedores (<?php echo count($listaAssociados); ?>)</h5>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="buscadorAssociados" class="form-control" placeholder="Buscar por nome ou email...">
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaAssociados">
                                    <?php foreach ($listaAssociados as $user): ?>
                                    <tr>
                                        <td>#<?php echo $user['idUsuario']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($user['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-warning" 
                                                    onclick="alterarSenhaUsuario(<?php echo $user['idUsuario']; ?>, 'associado')">
                                                <i class="fa-solid fa-key"></i> Nova Senha
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="clientes-pane" role="tabpanel">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h5>Lista de Clientes (<?php echo count($listaClientes); ?>)</h5>
                            </div>
                            <div class="col-md-6">
                                <input type="text" id="buscadorClientes" class="form-control" placeholder="Buscar por nome ou email...">
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-hover align-middle">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Email</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaClientes">
                                    <?php foreach ($listaClientes as $user): ?>
                                    <tr>
                                        <td>#<?php echo $user['idUsuario']; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($user['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td class="text-end">
                                            <button class="btn btn-sm btn-secondary" 
                                                    onclick="alterarSenhaUsuario(<?php echo $user['idUsuario']; ?>, 'cliente')">
                                                <i class="fa-solid fa-key"></i> Nova Senha
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div> </div>
        </div>
          <div class="row contornoGris p-3 mb-3">
                 <div class="col-12 mb-3">
                     <h2 class="subTitulo">Administrar Contas de Empreendedores</h2>
                 </div>
                 <div id="emprendimentosContainer"> </div>
             </div>
        </div>

        </div> 

   
</main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
        crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
        crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
        integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>

    <script src="../assets/js/AjustesCasaSolidaria.js"></script>

    <script>
        // ----------------------------------------------------
        // BUSCADOR RÁPIDO (FILTRO JS)
        // ----------------------------------------------------
        function filtrarTabla(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName("tr");

            input.addEventListener("keyup", function() {
                const filter = input.value.toLowerCase();
                for (let i = 0; i < tr.length; i++) {
                    // Buscar en columna Nombre (1) y Email (2)
                    let tdNombre = tr[i].getElementsByTagName("td")[1];
                    let tdEmail = tr[i].getElementsByTagName("td")[2];
                    
                    if (tdNombre || tdEmail) {
                        let txtNombre = tdNombre.textContent || tdNombre.innerText;
                        let txtEmail = tdEmail.textContent || tdEmail.innerText;
                        
                        if (txtNombre.toLowerCase().indexOf(filter) > -1 || txtEmail.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                        } else {
                            tr[i].style.display = "none";
                        }
                    }
                }
            });
        }

        // Activar buscadores si existen elementos
        if(document.getElementById("buscadorAssociados")) filtrarTabla("buscadorAssociados", "tablaAssociados");
        if(document.getElementById("buscadorClientes")) filtrarTabla("buscadorClientes", "tablaClientes");

        // ----------------------------------------------------
        // LÓGICA DE CAMBIO DE CONTRASEÑA (SWEETALERT2)
        // ----------------------------------------------------
        function alterarSenhaUsuario(idUsuario, tipoUsuario) {
            let titulo = tipoUsuario === 'adminGeneral' ? 'Alterar Minha Senha' : 'Alterar Senha de Usuário';
            
            Swal.fire({
                title: titulo,
                html: `Digite a nova senha para <b>${tipoUsuario}</b>:`,
                input: 'password',
                inputPlaceholder: 'Digite a nova senha aqui...',
                inputAttributes: {
                    autocapitalize: 'off',
                    autocorrect: 'off'
                },
                showCancelButton: true,
                confirmButtonText: 'Salvar Nova Senha',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#FDCB29', 
                cancelButtonColor: '#333',
                background: '#B2442E',
                color: '#FFF',
                showLoaderOnConfirm: true,
                preConfirm: (novaSenha) => {
                    if (!novaSenha || novaSenha.length < 4) {
                        Swal.showValidationMessage('A senha deve ter pelo menos 4 caracteres');
                        return false;
                    }

                    // Preparar datos para enviar al controlador
                    const formData = new FormData();
                    formData.append('accion', 'actualizarPassword');
                    formData.append('idUsuario', idUsuario);
                    formData.append('tipoUsuario', tipoUsuario);
                    formData.append('nuevaPassword', novaSenha);

                    return fetch('../controllers/AdminController.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Erro na requisição: ${error}`);
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value.status === 'ok') {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: result.value.message,
                            icon: 'success',
                            confirmButtonColor: '#FDCB29',
                            background: '#B2442E',
                            color: '#FFF'
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: result.value.message || 'Não foi possível alterar a senha.',
                            icon: 'error',
                            confirmButtonColor: '#B2442E'
                        });
                    }
                }
            });
        }
    </script>
</body>
</html>