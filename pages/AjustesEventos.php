<?php
session_start();
if (!isset($_SESSION["user"])) {
    header("location:../");
} else {
    if ($_SESSION["user"]["tipo"] != "adminGeneral") {
        header("location:../");
    }
}

?>
<!doctype html>
<html lang="br">

<head>
    <title>Ajustes de Eventos</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../assets/css/stylePrincipal.css">
</head>

<body>

    <header>
        <nav class="navbar navbar-expand navbar-light">
            <div class="container-fluid">
                <div class="navbar-nav d-flex align-items-center">
                    <img src="../assets/img/CasaSolidaria/defaultLogo.png" alt="Logo de la Empresa" width="80px">
                    <a class="nav-item nav-link titulo" href="../">Loja Solidaria</a>
                    <a class="nav-item nav-link" href="Eventos.php">Eventos</a>

                </div>
            </div>
        </nav>
    </header>

    <main class="container py-4">

        <section class="contornoGris p-4 mb-4">
            <h2 class="subTitulo" id="formEventoTitulo">Criar Novo Evento</h2>

            <form id="formEvento" method="POST" action="../controllers/EventoController.php" enctype="multipart/form-data">
                <input type="hidden" name="action" id="eventoAction" value="crear">
                <input type="hidden" name="id" id="eventoId" value="">

                <div class="mb-3">
                    <label for="titulo" class="form-label">Título do Evento</label>
                    <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Digite o título do Evento" required>
                </div>

                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descripcion" name="descripcion" rows="4" placeholder="Descrição do evento"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fechaInicio" class="form-label">Data de Início</label>
                        <input type="datetime-local" class="form-control" id="fechaInicio" name="fechaInicio" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fechaFinal" class="form-label">Data Final</label>
                        <input type="datetime-local" class="form-control" id="fechaFinal" name="fechaFinal">
                    </div>
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Localização</label> <input type="text" class="form-control" id="ubicacion" name="ubicacion" placeholder="Digite o local do evento">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-8 mb-3">
                        <label for="imagem" class="form-label">Imagem</label>
                        <input type="file" class="form-control foto-input" id="imagem" name="imagem" data-preview="previewEvento" accept="image/*">
                    </div>
                    <div class="col-md-4 mb-3">
                        <img id="previewEvento" class="img-fluid rounded" src="../assets/img/CasaSolidaria/defaultLogo.png" alt="Preview Evento" style="max-height: 100px; display: block; margin-top: 1.5rem;">
                    </div>
                </div>

                <div class="text-end">
                    <button type="button" id="btnCancelarEdicion" class="btn btn--cinza me-2" style="display: none;">
                        <i class="fa-solid fa-xmark"></i> Cancelar Edição
                    </button>
                    <button disabled type="submit" id="btnSubmitEvento" class="btn btn--amarelo">
                        <i class="fa-solid fa-floppy-disk"></i> Salvar Evento
                    </button>
                </div>
            </form>
        </section>

        <section class="contornoGris p-3 mb-3">
            <div class="col-12 mb-3">
                <h2 class="subTitulo">Eventos Cadastrados</h2>
            </div>
            <div id="eventosContainer">
            </div>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.23.0/dist/sweetalert2.all.min.js"></script>
    <script src="../assets/js/AjustesEventos.js"></script>
</body>

</html>