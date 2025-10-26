//Variables Globales

//Formularios
const formHistoria = document.getElementById("formHistoria");
const formPortada = document.getElementById("formPortada");
const formMision = document.getElementById("formMision");
const formVision = document.getElementById("formVision");
const formInformacionEmpresa = document.getElementById("formInformacionEmpresa");

//Traer la informacion de los associados
document.addEventListener("DOMContentLoaded", () => {
    fetch("../controllers/EmprendimentoController.php", {
        method: 'POST',
        body: new URLSearchParams({
            accion: "buscar"
        })
    })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById("emprendimentosContainer");

            if (!data.data || data.data.length === 0) {
                container.innerHTML = `<p>No se encontraron emprendimientos.</p>`;
                return;
            }

            data.data.forEach(emprendimento => {
                const fechaRegistro = emprendimento.dataCriacao ?? "DD-MM-YY";
                const dataStr = encodeURIComponent(JSON.stringify(emprendimento));

                const html = `
                                <div class="row contornoGris p-3 mb-3">
                                <div class="col-md-10  p-2 mb-3">
                                    <div class="row align-items-center">
                                    <div class="col-2 col-md-1">
                                        <img src="../${emprendimento.logo ?? "assets/img/CasaSolidaria/defaultLogo.png"}" alt="logo" class="img-fluid"> 
                                    </div>
                                    <div class="col-5">
                                        <p class="mb-0"><b>${emprendimento.nome}</b></p>
                                    </div>
                                    <div class="col-6">
                                        <p class="mb-0">Registro: ${fechaRegistro}</p>
                                    </div>
                                    </div>
                                </div>

                                <div class="col-md-2 text-center mb-3">
                                    <button type="button"
                                    class="btn btn--amarelo w-100 mb-2 ver-detalhes" 
                                    data-emprendimento="${dataStr}">
                                    <i class="fa-solid fa-circle-info"></i> Ver Detalhes
                                    </button>

                                    <button type="button" data-id="${emprendimento.adminAssociado_idUsuario}" class="eliminar-emprendimento btn btn--vermelho w-100">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                    </button>
                                </div>
                                </div>
                            `;

                container.innerHTML += html;
            });

            //Bóton ver más
            container.addEventListener("click", (e) => {
                const btn = e.target.closest(".ver-detalhes");
                if (!btn) return;

                const empreendimento = JSON.parse(decodeURIComponent(btn.dataset.emprendimento));
                const fechaRegistro = empreendimento.dataCriacao ?? "DD-MM-YY";

                Swal.fire({
                    title: `<div style="font-size:1.8em; font-weight:bold; color:#333;">${empreendimento.nome}</div>`,
                    html: `
      <div style="text-align:left; max-width:500px; margin:auto;">
        <div style="text-align:center; margin-bottom:15px;">
          <img src="../${empreendimento.logo ?? "assets/img/CasaSolidaria/defaultLogo.png"}" 
               alt="Logo" 
               style="width:120px; height:120px; object-fit:cover; border-radius:50%; border:3px solid #f5c518;">
        </div>

        <div style="background:#f9f9f9; padding:15px; border-radius:10px;">
          <p style="margin:8px 0;"><i class="fa-solid fa-id-card"></i> <b>ID:</b> ${empreendimento.idEmprendimento}</p>
          <p style="margin:8px 0;"><i class="fa-solid fa-calendar-day"></i> <b>Registro:</b> ${fechaRegistro}</p>
          <p style="margin:8px 0;"><i class="fa-solid fa-envelope"></i> <b>Email:</b> ${empreendimento.emailAdmin ?? "N/A"}</p>
          <p style="margin:8px 0;"><i class="fa-solid fa-phone"></i> <b>Telefone:</b> ${empreendimento.telefone ?? "-"}</p>
          <p style="margin:8px 0;"><i class="fa-solid fa-mobile-screen"></i> <b>Celular:</b> ${empreendimento.celular ?? "-"}</p>
          <p style="margin:8px 0;"><i class="fa-solid fa-location-dot"></i> <b>Localização:</b> ${empreendimento.ubicacao ?? "Não informada"}</p>
        </div>

        <div style="margin-top:15px;">
          <p style="margin:5px 0;"><b>Descrição:</b></p>
          <p style="background:#fff; padding:10px; border-radius:8px; border:1px solid #ddd;">
            ${empreendimento.descricao ?? empreendimento.historia ?? "Sem descrição"}
          </p>
        </div>

        <div style="margin-top:15px; text-align:center;">
          ${empreendimento.instagram ? `<a href="${empreendimento.instagram}" target="_blank" style="margin:0 8px; color:#E4405F;"><i class="fa-brands fa-instagram fa-lg"></i></a>` : ""}
          ${empreendimento.facebook ? `<a href="${empreendimento.facebook}" target="_blank" style="margin:0 8px; color:#1877F2;"><i class="fa-brands fa-facebook fa-lg"></i></a>` : ""}
        </div>
      </div>
    `,
                    showConfirmButton: true,
                    confirmButtonText: "Fechar",
                    confirmButtonColor: "#f5c518",
                    background: "#fff",
                    width: "600px"
                });
            });

            //Botón eliminar
            container.addEventListener("click", (e) => {
                const btn = e.target.closest(".eliminar-emprendimento");
                if (!btn) return;

                const id = btn.dataset.id;

                // Confirmar con SweetAlert antes de eliminar
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
                        fetch("../controllers/UsuarioController.php", {
                            method: "POST",
                            body: new URLSearchParams({
                                accion: "eliminar",
                                id: id,
                            })
                        })
                            .then(res => {
                                console.log("Estado de la respuesta HTTP:", res.status);
                                if (!res.ok) {
                                    // Intenta leer el cuerpo como texto para ver el error del servidor (HTML/texto)
                                    return res.text().then(text => {
                                        console.error("Error en la respuesta del servidor (HTTP " + res.status + "):", text);
                                        // Lanza un error para caer en el catch final
                                        throw new Error("Error del servidor (HTTP " + res.status + "). Verifique la consola para más detalles.");
                                    });
                                }
                                return res.json();
                            })
                            .then(data => {
                                console.log(data);
                                if (data.status === "ok") {
                                    Swal.fire({
                                        title: 'Sucesso!',
                                        html: data.message,
                                        icon: 'success',
                                        showConfirmButton: false,
                                        background: '#DCA700',
                                        color: '#000000',
                                        confirmButtonColor: '#B2442E',
                                        iconColor: '#FFFFFF',
                                        timer: 3000,
                                        timerProgressBar: true
                                    });

                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 3000);
                                }
                                else {
                                    Swal.fire({
                                        title: '¡Error!',
                                        text: data.message,
                                        icon: 'error',
                                        confirmButtonText: 'Aceptar',
                                        background: '#B2442E',
                                        color: '#FFFFFF',
                                        confirmButtonColor: '#FDCB29',
                                        iconColor: '#FDCB29'
                                    });

                                }
                            })
                            .catch(err => {
                                console.error("Error en la petición:", err);
                                Swal.fire({
                                    title: '¡Error de Conexión!',
                                    text: err.message || 'Ocurrió un error de red o el servidor no devolvió una respuesta válida.',
                                    icon: 'error'
                                });
                            });
                    }
                });
            });

        });
})

//mostra más detalles del emprendedor
function verDetalles(id) {
    alert(id);
}

//////////////////////////////////////////////////////
//                        INICIO                    //
//////////////////////////////////////////////////////

/* Vista previa de fotos */
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

// Habilitar todos los botones submit
const submitButtons = document.querySelectorAll('button[type="submit"]');
submitButtons.forEach(button => {
    button.disabled = false;
});

// Validar formulario HISTORIA
formHistoria.addEventListener("submit", function (e) {
    e.preventDefault();
    const textarea = formHistoria.querySelector("[name='historia']");

    if (textarea.value.trim() !== "") {
        enviarFormulario(formHistoria);

    } else {
        formularioVacio(textarea);
    }
});

// Validar formulario PORTADA
formPortada.addEventListener("submit", function (e) {
    e.preventDefault();
    const fileInput = formPortada.querySelector("[name='portada']");

    if (fileInput.files.length > 0) {
        enviarFormulario(formPortada);
    } else {
        formularioVacio(fileInput, "Por favor, seleccione una imagen para la portada.");
    }
});

// Validar formulario MISIÓN
formMision.addEventListener("submit", function (e) {
    e.preventDefault();
    const textarea = formMision.querySelector("[name='mision']");

    if (textarea.value.trim() !== "") {
        enviarFormulario(formMision);
    } else {
        formularioVacio(textarea);
    }
});

// Validar formularios de FOTOS GALERÍA
document.querySelectorAll('.formFotoGaleria').forEach(form => {
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        const fileInput = this.querySelector("[name='foto']");

        if (fileInput.files.length > 0) {
            enviarFormulario(this);
        } else {
            formularioVacio(fileInput, "Por favor, seleccione una imagen para la galería.");
        }
    });
});

// Validar formularios de ELIMINAR FOTOS GALERÍA
document.querySelectorAll('.formEliminarFoto').forEach(form => {
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Confirmación antes de eliminar
        Swal.fire({
            title: '¿Está seguro?',
            text: "Esta acción eliminará la foto de la galería.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#B2442E',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                enviarFormulario(this);
            }
        });
    });
});

// Validar formulario VISIÓN
formVision.addEventListener("submit", function (e) {
    e.preventDefault();
    const textarea = formVision.querySelector("[name='vision']");

    if (textarea.value.trim() !== "") {
        enviarFormulario(formVision);
    } else {
        formularioVacio(textarea);
    }
});

// Validar formulario INFORMACIÓN EMPRESA
formInformacionEmpresa.addEventListener("submit", function (e) {
    e.preventDefault();

    const inputs = formInformacionEmpresa.querySelectorAll('input[type="text"]');
    let formularioValido = true;
    let primerInputVacio = null;

    // Verificar que al menos un campo esté lleno
    let alMenosUnoLleno = false;
    inputs.forEach(input => {
        if (input.value.trim() !== "") {
            alMenosUnoLleno = true;
        }
    });

    if (!alMenosUnoLleno) {
        formularioValido = false;
        primerInputVacio = inputs[0]; // Usar el primer input para mostrar el error
    }

    if (formularioValido) {
        enviarFormulario(formInformacionEmpresa);
    } else {
        formularioVacio(primerInputVacio, "Por favor, complete al menos un campo de información.");
    }
});

// Función para enviar formulario
function enviarFormulario(formulario) {
    const formData = new FormData(formulario);
    fetch(formulario.getAttribute("action"), {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonColor: '#B2442E',
                    timer: 3000,
                    timerProgressBar: true
                });

                // Recargar la página después de 2 segundos para ver los cambios
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                // Mostrar mensaje de error
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    confirmButtonColor: '#B2442E'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Ocurrió un error al procesar la solicitud.',
                icon: 'error',
                confirmButtonColor: '#B2442E'
            });
        });
}

// Función para mostrar alerta de formulario vacío
function formularioVacio(elemento, mensajePersonalizado = null) {
    const mensaje = mensajePersonalizado || 'O formulário não pode estar vazio. Por favor, preencha todos os campos obrigatórios.';

    Swal.fire({
        title: 'Atenção!',
        html: mensaje,
        icon: 'warning',
        showConfirmButton: true,
        confirmButtonText: 'Entendi',
        background: '#DCA700',
        color: '#000000',
        confirmButtonColor: '#B2442E',
        iconColor: '#FFFFFF',
        timer: 4000,
        timerProgressBar: true
    });

    // Aplicar borde rojo al elemento si existe
    if (elemento) {
        elemento.style.animation = "parpadeoBordeRojo 1s 3";
        setTimeout(() => {
            elemento.style.animation = "none";
        }, 3000);

        // Enfocar el elemento
        elemento.focus();
    }
}   