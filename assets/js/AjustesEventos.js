let eventosCargados = [];

document.addEventListener("DOMContentLoaded", () => {
    //Variables Globales
    const formEvento = document.getElementById("formEvento");
    const eventosContainer = document.getElementById("eventosContainer");
    const formEventoTitulo = document.getElementById("formEventoTitulo");
    const eventoAction = document.getElementById("eventoAction");
    const eventoId = document.getElementById("eventoId");
    const btnSubmitEvento = document.getElementById("btnSubmitEvento");
    const btnCancelarEdicion = document.getElementById("btnCancelarEdicion");
    const previewEventoImg = document.getElementById("previewEvento");

    cargarEventos();
    btnSubmitEvento.disabled = false;


    function cargarEventos() {
        eventosContainer.innerHTML = '<p>Carregando eventos...</p>';
        fetch("../controllers/EventoController.php", {
            method: 'POST',
            body: new URLSearchParams({
                action: "obtener_todos"
            })
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success || !data.data || data.data.length === 0) {
                    eventosContainer.innerHTML = `<p>Nenhum evento encontrado.</p>`;
                    eventosCargados = [];
                    return;
                }

                eventosCargados = data.data;
                eventosContainer.innerHTML = '';

                data.data.forEach(evento => {

                    const dataInicioFormatada = formatarDataLegivelJS(evento.fechaInicio);
                    const horaInicioFormatada = formatarHoraJS(evento.fechaInicio);

                    const html = `
                <div class="row contornoGris p-3 mb-3">
                    <div class="col-md-3">
                        <img src="${evento.imagen ?? "../assets/img/CasaSolidaria/defaultLogo.png"}" alt="Imagem do Evento" class="img-fluid rounded">
                    </div>
                    <div class="col-md-6">
                        <h5 class="subTitulo">${evento.titulo}</h5>
                        
                        <p class="mb-1"><strong><i class="fa-solid fa-map-marker-alt"></i> Local:</strong> ${evento.ubicacion || "Não informado"}</p>
                        
                        <p class="mb-1"><strong><i class="fa-solid fa-calendar-day"></i> Início:</strong> ${dataInicioFormatada} ${horaInicioFormatada ? 'às ' + horaInicioFormatada : ''}</p>
                        <p class="mb-1"><strong><i class="fa-solid fa-circle-info"></i> Estado:</strong> ${evento.estado || "Não informado"}</p>
                        <p class="small">${evento.descripcion || "Sem descrição"}</p>
                    </div>
                    <div class="col-md-3 text-center d-flex flex-column justify-content-center">
                        <button type="button"
                            class="btn btn--amarelo w-100 mb-2 editar-evento" 
                            data-id="${evento.idEvento}"> <i class="fa-solid fa-pen-to-square"></i> Editar
                        </button>
                        <button type="button" 
                            data-id="${evento.idEvento}" class="eliminar-evento btn btn--vermelho w-100">
                            <i class="fa-solid fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>
            `;
                    eventosContainer.innerHTML += html;
                });
            })
            .catch(err => {
                eventosContainer.innerHTML = `<p>Erro ao carregar eventos.</p>`;
                console.error("Error en fetch:", err);
            });
    }

    eventosContainer.addEventListener("click", (e) => {
        const btnEliminar = e.target.closest(".eliminar-evento");
        if (btnEliminar) {
            const id = btnEliminar.dataset.id;
            handleEliminar(id);
            return;
        }

        const btnEditar = e.target.closest(".editar-evento");
        if (btnEditar) {
            const id = btnEditar.dataset.id;
            handleEditar(id);
            return;
        }
    });

    formEvento.addEventListener("submit", function (e) {
        e.preventDefault();
        const titulo = formEvento.querySelector("[name='titulo']").value.trim();
        const fechaInicio = formEvento.querySelector("[name='fechaInicio']").value;
        if (titulo === "" || fechaInicio === "") {
            formularioVacio(formEvento.querySelector("[name='titulo']"), "Por favor, preencha Título e Data de Início.");
            return;
        }
        enviarFormulario(formEvento);
    });

    btnCancelarEdicion.addEventListener("click", () => {
        resetarFormulario();
    });

    function handleEditar(id) {
        const evento = eventosCargados.find(e => e.idEvento == id);
        if (!evento) return;

        formEventoTitulo.innerText = "Editar Evento";
        eventoAction.value = "actualizar";
        eventoId.value = evento.idEvento;

        formEvento.querySelector("[name='titulo']").value = evento.titulo;
        formEvento.querySelector("[name='descripcion']").value = evento.descripcion;

        formEvento.querySelector("[name='fechaInicio']").value = formatDBtoInputDatetime(evento.fechaInicio);
        formEvento.querySelector("[name='fechaFinal']").value = formatDBtoInputDatetime(evento.fechaFinal);
        formEvento.querySelector("[name='estado']").value = evento.estado;
        formEvento.querySelector("[name='ubicacion']").value = evento.ubicacion;

        previewEventoImg.src = evento.imagen;
        previewEventoImg.style.display = 'block';
        formEvento.querySelector("[name='imagem']").value = "";

        btnSubmitEvento.innerHTML = '<i class="fa-solid fa-save"></i> Atualizar Evento';
        btnCancelarEdicion.style.display = "inline-block";

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function handleEliminar(id) {
        Swal.fire({
            title: "Tem certeza?",
            text: "Esta ação não pode ser desfeita.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sim, eliminar!",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                fetch("../controllers/EventoController.php", {
                    method: "POST",
                    body: new URLSearchParams({
                        action: "eliminar",
                        id: id,
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Sucesso!', data.message, 'success');
                            cargarEventos();
                        } else {
                            Swal.fire('Erro!', data.message, 'error');
                        }
                    })
                    .catch(err => {
                        console.error("Error en la petición:", err);
                        Swal.fire('¡Error de Conexión!', 'Ocorreu um erro de rede.', 'error');
                    });
            }
        });
    }

    function resetarFormulario() {
        formEvento.reset();
        formEventoTitulo.innerText = "Criar Novo Evento";
        eventoAction.value = "crear";
        eventoId.value = "";
        previewEventoImg.src = "../assets/img/CasaSolidaria/defaultLogo.png";
        btnSubmitEvento.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Salvar Evento';
        btnCancelarEdicion.style.display = "none";
    }


    function enviarFormulario(formulario) {
        const formData = new FormData(formulario);
        btnSubmitEvento.disabled = true;
        btnSubmitEvento.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';
        fetch(formulario.getAttribute("action"), {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('¡Éxito!', data.message, 'success');
                    resetarFormulario();
                    cargarEventos();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocorreu um error ao processar a solicitação.', 'error');
            })
            .finally(() => {
                btnSubmitEvento.disabled = false;
                if (eventoAction.value === 'actualizar') {
                    btnSubmitEvento.innerHTML = '<i class="fa-solid fa-save"></i> Atualizar Evento';
                } else {
                    btnSubmitEvento.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> Salvar Evento';
                }
            });
    }

    document.querySelectorAll('.foto-input').forEach(input => {
        input.addEventListener('change', function (e) {
            const previewId = this.getAttribute('data-preview');
            const previewImg = document.getElementById(previewId);
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImg.src = event.target.result;
                    previewImg.style.display = 'block';
                };
                reader.readAsDataURL(this.files[0]);
            } else {
                previewImg.src = '../assets/img/CasaSolidaria/defaultLogo.png';
            }
        });
    });

    function formularioVacio(elemento, mensajePersonalizado = null) {
        const mensaje = mensajePersonalizado || 'O formulário não pode estar vazio. Por favor, preencha todos os campos obrigatórios.';
        Swal.fire('Atenção!', mensaje, 'warning');
        if (elemento) {
            elemento.focus();
        }
    }

    function formatDBtoInputDatetime(dataStr) {
        if (!dataStr || dataStr.startsWith('0000-00-00')) return "";
        return dataStr.replace(' ', 'T').substring(0, 16);
    }

    function formatarDataLegivelJS(dataStr) {
        if (!dataStr || dataStr.startsWith('0000-00-00')) return "N/A";
        const meses = [
            'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
        ];
        const data = new Date(dataStr.replace(' ', 'T'));
        const dia = data.getDate().toString().padStart(2, '0');
        const mesIndex = data.getMonth();
        const ano = data.getFullYear();
        return `${dia} de ${meses[mesIndex]} de ${ano}`;
    }

    function formatarHoraJS(dataStr) {
        if (!dataStr || dataStr.startsWith('0000-00-00')) return null;
        const data = new Date(dataStr.replace(' ', 'T'));
        const hora = data.getHours().toString().padStart(2, '0');
        const minuto = data.getMinutes().toString().padStart(2, '0');
        const horaCompleta = `${hora}:${minuto}`;
        return (horaCompleta !== '00:00') ? horaCompleta : null;
    }

});