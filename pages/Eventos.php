<?php
session_start();
if (isset($_SESSION["user"])) {
    $idUsuario = $_SESSION["user"]['id'];
    $tipoUsuario = $_SESSION["user"]['tipo'];
    $nomeUsuairo = $_SESSION["user"]['nome'];
} else {
    $idUsuario = $tipoUsuario = $nomeUsuairo = null;
}

require_once '../controllers/PaginaPrincipalController.php';
$ppController = new PaginaPrincipalController();
$datosPagina = $ppController->obtenerDatos();

require_once '../models/modelsDAO/EventoDAO.php';
$eventoDAO = new EventoDAO();
$eventosVisiveis = $eventoDAO->obtenerEventosPublicos();

function formatarDataLegivel($dataStr)
{
    if (!$dataStr || $dataStr === '0000-00-00' || $dataStr === '0000-00-00 00:00:00') return "N/A";
    $meses = [
        '01' => 'Janeiro',
        '02' => 'Fevereiro',
        '03' => 'Março',
        '04' => 'Abril',
        '05' => 'Maio',
        '06' => 'Junho',
        '07' => 'Julho',
        '08' => 'Agosto',
        '09' => 'Setembro',
        '10' => 'Outubro',
        '11' => 'Novembro',
        '12' => 'Dezembro'
    ];
    $dataPartes = explode('-', date('Y-m-d', strtotime($dataStr)));
    if (count($dataPartes) !== 3) return "Data inválida";
    $dia = $dataPartes[2];
    $mesNum = $dataPartes[1];
    $ano = $dataPartes[0];
    $mesNome = $meses[$mesNum] ?? $mesNum;
    return "$dia de $mesNome de $ano";
}

function formatarHora($dataStr)
{
    if (!$dataStr || $dataStr === '0000-00-00 00:00:00') return null;
    $hora = date('H:i', strtotime($dataStr));
    return ($hora && $hora !== '00:00') ? $hora : null;
}
?>
<!doctype html>
<html lang="br">

<head>
    <title>Eventos - Loja Solidaria</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Alegreya+SC:ital,wght@0,400;0,500;0,700;0,800;0,900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:ital,wght@0,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400..700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/stylePrincipal.css">
    <link rel="stylesheet" href="../assets/css/styleEventos.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <div class="navbar-nav d-flex align-items-center">
                    <img src="../assets/img/CasaSolidaria/defaultLogo.png" alt="Logo de la Empresa" width="80px">
                    <div>
                        <a class="nav-item nav-link titulo" href="index.php">Loja Solidaria</a>
                    </div>
                    <a class="nav-item nav-link active" href="#Eventos">Eventos <i
                            class="fa-solid fa-arrow-down"></i></a>
                    <a class="nav-item nav-link" href="index.php#Emprendimentos">Emprendimentos</a>
                </div>
                <?php if ($idUsuario == null) { ?>
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
                                    Swal.fire({
                                        title: 'Confirmar saída',
                                        text: "Você tem certeza que deseja sair da conta?",
                                        icon: 'warning',
                                        showCancelButton: true,
                                        confirmButtonText: 'Sim, sair',
                                        cancelButtonText: 'Cancelar',
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            window.location.href = "CerrarSesion.php";
                                        }
                                    });
                                });
                            }
                        });
                    </script>
                <?php } ?>
            </div>
        </nav>
    </header>

    <?php if ($idUsuario != null && $tipoUsuario == "adminGeneral") { ?>
        <!-- Boton Flotante para Ajustes -->
        <a href="AjustesEventos.php" title="Ajustes Gerais" class="btn" id="btnFlotante--ajustesGerais"><i
                class="fa-solid fa-gear"></i></a>
    <?php } ?>
    <main>
        <?php if ($idUsuario != null && $tipoUsuario == "adminGeneral") { ?>
            <a href="AjustesEventos.php" title="Ajustes Gerais" class="btn" id="btnFlotante--ajustesGerais"><i
                    class="fa-solid fa-gear"></i></a>
        <?php } ?>
        <div class="container">
            <div class="row">

                <div class="col-12 mb-5">
                    <h2 class="subTitulo">Eventos em Destaque</h2>

                    <div id="galeriaFotos" class="carousel slide" data-bs-ride="carousel">

                        <?php if (!empty($eventosVisiveis)): ?>
                            <ol class="carousel-indicators">
                                <?php foreach ($eventosVisiveis as $i => $evento): ?>
                                    <li data-bs-target="#galeriaFotos" data-bs-slide-to="<?php echo $i; ?>"
                                        class="<?php echo $i == 0 ? 'active' : ''; ?>"
                                        aria-current="<?php echo $i == 0 ? 'true' : 'false'; ?>"></li>
                                <?php endforeach; ?>
                            </ol>

                            <div class="carousel-inner" role="listbox">
                                <?php foreach ($eventosVisiveis as $i => $evento): ?>
                                    <div class="carousel-item <?php echo $i == 0 ? 'active' : ''; ?>">

                                        <img src="<?php echo $evento->getImagen() ? $evento->getImagen() : '../assets/img/CasaSolidaria/defaultPooster.png'; ?>"
                                            class="w-100 d-block galeria__imagem" alt="<?php echo htmlspecialchars($evento->getTitulo()); ?>" />

                                        <div class="carousel-caption d-none d-md-block" style="background-color: rgba(0,0,0,0.5); border-radius: 10px;">
                                            <h5 class="subTitulo"><?php echo htmlspecialchars($evento->getTitulo()); ?></h5>

                                            <?php $ubicacion = $evento->getUbicacion(); ?>
                                            <?php if ($ubicacion): ?>
                                                <p class="parrafo mb-1" style="font-weight: 500;">
                                                    <i class="fa-solid fa-map-marker-alt"></i> <?php echo htmlspecialchars($ubicacion); ?>
                                                </p>
                                            <?php endif; ?>
                                            <p class="parrafo mb-1">
                                                <i class="fa-solid fa-calendar-day"></i> <?php echo formatarDataLegivel($evento->getFechaInicio()); ?>
                                            </p>
                                            <?php $horaInicio = formatarHora($evento->getFechaInicio()); ?>
                                            <?php if ($horaInicio): ?>
                                                <p class="parrafo">
                                                    <i class="fa-solid fa-clock"></i> <?php echo $horaInicio; ?> hs
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <button class="carousel-control-prev" type="button" data-bs-target="#galeriaFotos" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#galeriaFotos" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>

                        <?php else: ?>
                            <div class="carousel-inner" role="listbox">
                                <div class="carousel-item active">
                                    <img src="../assets/img/CasaSolidaria/defaultPooster.png" class="w-100 d-block galeria__imagem" alt="Sem eventos" />
                                    <div class="carousel-caption d-none d-md-block">
                                        <h5>Nenhum evento em destaque</h5>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>


                <div class="col-12 mb-3">
                    <h2 id="Eventos" class="subTitulo"> Todos os Eventos </h2>
                </div>
                <div class="row" id="eventos-container">
                    <?php if (!empty($eventosVisiveis)): ?>
                        <?php foreach ($eventosVisiveis as $evento): ?>
                            <div class="col-12 col-md-6 col-lg-4 mb-4">
                                <div class="card h-100">
                                    <img class="card-img-top" height="300px" style="object-fit: cover;"
                                        src="<?php echo $evento->getImagen() ? $evento->getImagen() : '../assets/img/CasaSolidaria/defaultProduct.png'; ?>"
                                        alt="<?php echo htmlspecialchars($evento->getTitulo()); ?>" />

                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title subTitulo"><?php echo htmlspecialchars($evento->getTitulo()); ?></h5>
                                        <div class="mt-auto">

                                            <?php $ubicacion = $evento->getUbicacion(); ?>
                                            <?php if ($ubicacion): ?>
                                                <p class="parrafo mb-1">
                                                    <strong><i class="fa-solid fa-map-marker-alt"></i> Local:</strong>
                                                    <?php echo htmlspecialchars($ubicacion); ?>
                                                </p>
                                            <?php endif; ?>
                                            <p class="parrafo mb-1">
                                                <strong><i class="fa-solid fa-calendar-day"></i> Data:</strong>
                                                <?php echo formatarDataLegivel($evento->getFechaInicio()); ?>
                                            </p>
                                            <?php $horaInicio = formatarHora($evento->getFechaInicio()); ?>
                                            <?php if ($horaInicio): ?>
                                                <p class="parrafo mb-2">
                                                    <strong><i class="fa-solid fa-clock"></i> Hora:</strong> <?php echo $horaInicio; ?> hs
                                                </p>
                                            <?php else: ?>
                                                <p class="parrafo mb-2" style="visibility: hidden;">&nbsp;</p>
                                            <?php endif; ?>

                                            <?php
                                            $eventoData = htmlspecialchars(json_encode($evento->toArray()), ENT_QUOTES, 'UTF-8');
                                            ?>
                                            <button type="button" class="btn btn--amarelo w-100 ver-detalhes-btn"
                                                data-evento="<?php echo $eventoData; ?>">
                                                Ver Detalhes
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center">
                            <p class="parrafo">Nenhum evento futuro encontrado no momento.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>

    <script src="../assets/js/Eventos.js"></script>
</body>

</html>