<?php
session_start();
require "config/database.php";

if (isset($_SESSION["user"])) {
    $idUsuario = $_SESSION["user"]['id'];
    $tipoUsuario = $_SESSION["user"]['tipo'];
    $nomeUsuairo = $_SESSION["user"]['nome'];
} else {
    $idUsuario = $tipoUsuario = $nomeUsuairo = null;
}
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
    <link rel="stylesheet" href="assets/css/stylePrincipal.css">
    <link rel="stylesheet" href="assets/css/styleMenuCasaSolidaria.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <!-- Logo y enlaces -->
                <div class="navbar-nav d-flex align-items-center">
                    <img src="assets/img//CasaSolidaria/defaultLogo.png" alt="Logo de la Empresa" width="80px">
                    <div>
                        <a class="nav-item nav-link titulo" href="index.html">Loja Solidaria</a>
                        <?php if ($nomeUsuairo != null) { ?>
                            <hp href="#" class="mensajemBemvindo"> Oi, <?php echo $nomeUsuairo ?>! </p>
                            <?php } ?>

                    </div>
                    <a class="nav-item nav-link" href="pages/Eventos.html">Eventos</a>
                    <a class="nav-item nav-link" href="#Emprendimentos">Emprendimentos <i
                            class="fa-solid fa-arrow-down"></i></a>

                </div>

                <!-- Botones alineados a la derecha -->
                <!-- Sin login -->
                <?php if ($idUsuario == null) { ?>
                    <div class="d-flex ms-auto gap-2">
                        <a href="pages/Registro.html" class="btn btn--cinza">Registrarse <i
                                class="fa-solid fa-user-plus"></i></a href="#">
                        <a href="pages/Login.html" class="btn btn--amarelo">Logar <i
                                class="fa-solid fa-right-to-bracket"></i></a>
                    </div>
                <?php } else { ?>
                    <div class="d-flex ms-auto gap-2">
                        <a href="#" id="cerrarSesion-boton" class="btn btn--amarelo"> Sair <i
                                class="fa-solid fa-right-to-bracket"></i></a>
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
                                    window.location.href = "pages/CerrarSesion.php";
                                }
                            });
                        });
                    </script>
                <?php } ?>
            </div>
        </nav>

    </header>
    <main>

        <!-- Contenido para el Administrador General -->
        <?php if ($idUsuario != null && $tipoUsuario == "adminGeneral") { ?>
            <!-- Boton flotante para Aprovar Registro de Emprendedores -->
            <button data-bs-toggle="modal" data-bs-target="#modalAprovacaoEmprendedores"
                title="Alertas de Aprovação de Emprendedores" id="btnFlotante--aprovacaoEmprendimentos" class="btn"> <i
                    class="fa-solid fa-bell"></i> </button>

            <!-- Boton Flotante para Ajustes -->
            <a href="pages/AjustesCasaSolidaria.html" title="Ajustes Gerais" class="btn" id="btnFlotante--ajustesGerais"><i class="fa-solid fa-gear"></i></a>

            <!-- Modal para el menu de Aceptar Registro de Emprendedores  -->
            <div class="modal fade" id="modalAprovacaoEmprendedores" tabindex="-1" role="dialog"
                aria-labelledby="modalTitleId" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalTitleId">
                                Aprovação de Emprendedores
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="container-fluid">
                                <div class="modal--NuevoEmprendedor row d-flex align-items-center mb-4">
                                    <div class="col-3">
                                        <img src="assets/img/ImgEmprendimentos/logoLegado.png" width="80px"
                                            alt="logo da empresa">
                                    </div>
                                    <div class="col-9">
                                        <p>Tem um registro novo pendente de aprovação do Emprendimento <b>LEGADO</b></p>
                                    </div>
                                    <div class="col-12  text-end">
                                        <button class="btn btn--amarelo modal--botonMostrarMas">Mostrar detalhes <i
                                                class="fa-solid fa-angle-down"></i></button>
                                    </div>
                                    <div
                                        class="modal--contenidoMasInfo contornoGris ocultarElemento col-12 mt-2 p-3 bg-light rounded shadow-sm">

                                        <!-- Datos Personales -->
                                        <div class="card mb-3">
                                            <div class="card-header fundoVermelho text-white">
                                                <h5 class="mb-0">Datos Personales</h5>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><strong>Nome:</strong> Exemplo</li>
                                                <li class="list-group-item"><strong>Sobrenome:</strong> Exemplo</li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    <strong>Foto de Perfil:</strong>
                                                    <img src="assets/img/ImgEmprendimentos/imgProducto.png" alt="img"
                                                        class="img-thumbnail ms-2" width="50">
                                                </li>
                                            </ul>
                                        </div>

                                        <!-- Datos de la Empresa -->
                                        <div class="card">
                                            <div class="card-header fundoVermelho text-white">
                                                <h5 class="mb-0">Datos de la Empresa</h5>
                                            </div>
                                            <ul class="list-group list-group-flush">
                                                <li class="list-group-item"><strong>Nome:</strong> Exemplo</li>
                                                <li class="list-group-item"><strong>Historia:</strong> Exemplo</li>
                                                <li class="list-group-item"><strong>Proceso de fabricação:</strong> Exemplo
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>Imagens de fabricação:</strong>
                                                    <div class="mt-2">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                    </div>
                                                </li>
                                                <li class="list-group-item">
                                                    <strong>imagens da galeria:</strong>
                                                    <div class="mt-2">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                        <img src="assets/img/ImgEmprendimentos/imgProducto.png"
                                                            class="img-thumbnail me-1 mb-1" width="50" alt="img">
                                                    </div>
                                                </li>
                                                <li class="list-group-item"><strong>Telefone:</strong> Exemplo</li>
                                                <li class="list-group-item"><strong>WhatsApp:</strong> Exemplo</li>
                                                <li class="list-group-item"><strong>Instagram:</strong> Exemplo</li>
                                                <li class="list-group-item"><strong>Facebook:</strong> Exemplo</li>
                                            </ul>
                                        </div>

                                        <div class="col-12 text-end mt-3">
                                            <button type="button" class="btn btn-danger modal__botonRechazar"> Rejeitar
                                            </button>
                                            <button type="button" class="btn btn-success modal__botonAceptar"> Aprovar
                                            </button>
                                        </div>

                                    </div>

                                </div>
                                <hr>
                                <div class="row d-flex align-items-center">
                                    <div class="col-3">
                                        <img src="assets/img/ImgEmprendimentos/logoTearesDoSul.png" width="80px"
                                            alt="logo da empresa">
                                    </div>
                                    <div class="col-9">
                                        <p>Tem um registro novo pendente de aprovação do Emprendimento <b>TEARES DO SUL</b>
                                        </p>
                                    </div>
                                    <div class="col-12  text-end">
                                        <button class="btn btn--amarelo modal--botonMostrarMas">Mostrar detalhes <i
                                                class="fa-solid fa-angle-down"></i></button>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <div class="container-fluid">
            <!-- Conteudo da Portada Principal -->
            <div class="row portada">
                <div class="col-12 portada__conteudo-principal">
                    <img class="portada__imagem transiccionSuave" src="assets/img/CasaSolidaria/defaultPooster.png"
                        alt="Foto de portada da loja solidaria">
                    <div class="portada__conteudo-secundario mt-5 transiccionSuave">
                        <h1 class="subTitulo portada__subTitulo transiccionSuave">Historia</h1>
                        <p class="parrafo portada__parrafo transiccionSuave">
                            Desde 2013, a Casa de Economia Solidária de Santana do Livramento é um espaço autogestionado
                            que
                            reúne
                            empreendimentos locais de artesanato, alimentação, serviços e confecção, promovendo
                            cooperação,
                            formação
                            e desenvolvimento comunitário na Fronteira Oeste.
                        </p>
                        <button id="portada__botao" type="button" class="btn btn--amarelo portada__botao"> Ver mais
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <!-- Conteudo da Missão -->
            <div class="row mt-5 missao">
                <!-- conteudo onde estam as imagems da missão -->
                <div class="col-6 missao__conteudo--principal">
                    <img class="missao__imagem missao__imagem--curvaDireita" src="assets/img/CasaSolidaria/defaultProduct.png"
                        alt="imagem referente a missao da loja solidaria">
                    <img class="missao__imagem missao__imagem--curvaEsquerda" src="assets/img/CasaSolidaria/defaultProduct.png"
                        alt="imagem referente a missao da loja solidaria">
                    <img class="missao__imagem missao__imagem--curvaEsquerda" src="assets/img/CasaSolidaria/defaultProduct.png"
                        alt="imagem referente a missao da loja solidaria">
                    <img class="missao__imagem missao__imagem--curvaDireita" src="assets/img/CasaSolidaria/defaultProduct.png"
                        alt="imagem referente a missao da loja solidaria">
                    <div class="missao__circuloBorboleta"></div>
                </div>
                <!-- conteudo da informação da missão -->
                <div class="col-6 contornoGris">
                    <h2 class="subTitulo m-3 text-center"> Missão </h2>
                    <p class="parrafo">
                        Promover a inclusão social e econômica por meio da valorização do trabalho coletivo, da
                        cooperação e da autogestão, fortalecendo empreendimentos solidários em Santana do Livramento e
                        região. A Casa de Economia Solidária busca ser um espaço de produção, comercialização e
                        formação, apoiando iniciativas que oferecem alternativas sustentáveis e democráticas para o
                        desenvolvimento local.
                    </p>
                </div>
            </div>
            <!-- conteudo para a Vissão da loja solidaria -->
            <div class="row mt-5 visao contornoGris ">
                <div class="col-12">
                    <h2 class="subTitulo m-3 text-center">Visão</h2>
                    <p class="parrafo">
                        Consolidar-se como referência regional em Economia Solidária, sendo um modelo de autogestão e
                        cooperação que inspira outras comunidades. A Casa de Economia Solidária almeja expandir seu
                        impacto, ampliando oportunidades de trabalho digno, fortalecendo redes de empreendimentos e
                        contribuindo para um desenvolvimento socialmente justo e economicamente sustentável na Fronteira
                        Oeste e além.
                    </p>
                </div>
            </div>
        </div>
        <div id="Emprendimentos" class="container mt-5 emprendimento contornoGris">
            <!-- conteudo dos Emprendimentos -->
            <!-- Fila para o titulo -->
            <div class="row mt-5">
                <div class="col-12">
                    <h2 class="subTitulo m-3 text-center"> Emprendimentos </h2>
                </div>
            </div>
            <!-- Fila para as colunas de emprendimento -->
            <div class="row mb-5">
                <div class="col-3">
                    <div class="card">
                        <img class="card-img-top emprendimento__imagem"
                            src="assets/img/CasaSolidaria/defaultProduct.png" alt="logo do Emprendimento" />
                        <div class="card-body">
                            <h4 class="card-title">Nome</h4>
                            <p class="card-text">pequena descripção</p>
                            <button class="btn btn--vermelho"> Ver Loja </button>
                        </div>
                    </div>


                </div>
                <div class="col-3">
                    <div class="card">
                        <img class="card-img-top emprendimento__imagem"
                            src="assets/img/CasaSolidaria/defaultProduct.png" alt="logo do Emprendimento" />
                        <div class="card-body">
                            <h4 class="card-title">Nome</h4>
                            <p class="card-text">pequena descripção</p>
                            <button class="btn btn--vermelho"> Ver Loja </button>
                        </div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card">
                        <img class="card-img-top emprendimento__imagem"
                            src="assets/img/CasaSolidaria/defaultProduct.png" alt="logo do Emprendimento" />
                        <div class="card-body">
                            <h4 class="card-title">Nome</h4>
                            <p class="card-text">pequena descripção</p>
                            <button class="btn btn--vermelho"> Ver Loja </button>
                        </div>
                    </div>


                </div>
                <div class="col-3">
                    <div class="card">
                        <img class="card-img-top emprendimento__imagem"
                            src="assets/img/CasaSolidaria/defaultProduct.png" alt="logo do Emprendimento" />
                        <div class="card-body">
                            <h4 class="card-title">Legado</h4>
                            <p class="card-text">pequena descripção</p>
                            <a href="pages/Emprendimento.html"> <button class="btn btn--vermelho"> Ver Loja </button>
                            </a>
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </main>
    <footer class="mt-5 footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-3 text-center">
                    <img width="80px" src="assets/img/imgLogo.png" alt="logo do da empresa/empreendimento">
                    <h3 class="footer__titulo">Loja Solidaria</h3>
                </div>
                <div class="col-2">
                    <h4 class="footer__subTitulo"> Legales </h4>
                    <ul class="footer__texto">
                        <li> <a href="#" class="footer__texto--link"> Politica de Calidade </li> </a>
                        <li> <a href="#" class="footer__texto--link"> Politica de Privacidade</a> </li>
                        <li> <a href="#" class="footer__texto--link"> Politica de Dados </a> </li>
                    </ul>
                </div>
                <div class="col-2">
                    <h4 class="footer__subTitulo"> informação </h4>
                    <ul class="footer__texto">
                        <li> Telefone: 462111 </li>
                        <li> Horarios: X:XX AM - X:XX PM </li>
                        <li> WhatsApp: 099999999 </li>
                    </ul>
                </div>
                <div class="col-2">
                    <h4 class="footer__subTitulo"> Direção: </h4>
                    <ul class="footer__texto">
                        <li> Sant´Ana do Livramento: <br> Rua Tal. Numero Tal. Av Tal. </li>
                    </ul>
                </div>
                <div class="col-2 text-center">
                    <h4 class="footer__subTitulo"> Redes Sociais: </h4>
                    <button class="btn fs-2 m-0 p-0">
                        <a href="#">
                            <i class="fa-brands fa-facebook"></i>
                        </a>
                    </button>
                    <button class="btn fs-2 m-0 p-0">
                        <a href="#">
                            <i class="fa-brands fa-square-instagram text-danger"></i>
                        </a>
                    </button>
                    <button class="btn fs-2 m-0 p-0">
                        <a href="#">
                            <i class="fa-brands fa-square-whatsapp text-success"></i>
                        </a>
                    </button>
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

    <!-- FontaWesome JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"
        integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Sweet Alert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>


    <!-- My JS  -->
    <script src="assets/js/scriptMenuCasaSolidaria.js"></script>

    <!-- Alerta de Bienvenida -- cuando no hay nadie logueado -->
    <?php
    if ($idUsuario == null) {
    ?>
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                title: 'Bem-vindo ao nosso site!',
                html: 'Estamos felizes em ver você aqui. Explore nossos serviços!',
                icon: 'success',
                background: '#DCA700',
                color: '#000000',
                iconColor: '#FFFFFF',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        </script>
    <?php
    }
    ?>

    <!-- Alerta de Bienvenida -->
    <?php
    if (isset($_SESSION["user"]['alerta'])) {
        if ($_SESSION["user"]['tipo'] === "cliente") {
            echo "
        <script>
            Swal.fire({
                title: 'Bem-vindo!',
                html: 'Obrigado por estar conosco.<br><br>Desfrute da sua experiência!',
                icon: 'success',
                confirmButtonText: 'Começar',
                background: '#DCA700',
                color: '#000000',
                confirmButtonColor: '#B2442E',
                iconColor: '#FFFFFF'
            });
        </script>
        ";
        } else if ($_SESSION["user"]['tipo'] === "associado") {
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
        } else {
            echo "
        <script>
            Swal.fire({
                title: 'Bem-vindo, Administrador!',
                html: 'Você tem acesso total ao sistema.<br><br><b style=\"color:#B2442E\">Lembre-se de usar seus privilégios com responsabilidade.</b>',
                icon: 'success',
                confirmButtonText: 'Continuar',
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

    <!-- SCRIPT para manejar el contenido del Administrador General (Solo se va mostrar si existe la sesion del admin) -->
    <?php if ($idUsuario != null && $tipoUsuario == "adminGeneral") { ?>
        <script src="assets/js/scriptMenuCasaSolidariaAdmin.js"></script>
    <?php } ?>

</body>

</html>