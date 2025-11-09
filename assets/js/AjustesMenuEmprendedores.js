document.addEventListener("DOMContentLoaded", function () {

    const formHistoria = document.getElementById("formHistoria");
    const formProcesso = document.getElementById("formProcesso");
    const formInfoEmprendimento = document.getElementById("formInfoEmprendimento");
    const formInfoAdmin = document.getElementById("formInfoAdmin");
    const fotoPerfilInput = document.getElementById("fotoPerfilInput");
    const formEliminarConta = document.getElementById("formEliminarConta");
    const checkConfirma = document.getElementById('checkConfirma');
    const btnEliminarConta = document.getElementById('btnEliminarConta');
    const formImgFabricacao = document.getElementById("formImgFabricacao");
    const formImgGaleria = document.getElementById("formImgGaleria");
    const formCores = document.getElementById("formCores");

    if (formHistoria) {
        formHistoria.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Salvando...`;

            // Envia os dados por AJAX
            fetch("../controllers/EmprendimentoController.php", {
                method: "POST",
                body: formData,
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na rede: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'ok') {
                        // Alerta de sucesso
                        Swal.fire({
                            title: "Sucesso!",
                            text: data.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const newHistoria = formData.get('historia');
                        this.querySelector('textarea[name="historia"]').value = newHistoria;

                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text: data.message || "Ocorreu um problema.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição AJAX:", error);
                    Swal.fire({
                        title: "Erro de Conexão",
                        text: "Não foi possível conectar ao servidor. Verifique o console.",
                        icon: "error"
                    });
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
        });
    }

    if (formProcesso) {
        formProcesso.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Salvando...`;

            fetch("../controllers/EmprendimentoController.php", {
                method: "POST",
                body: formData,
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na rede: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                            title: "Sucesso!",
                            text: data.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        const newProcesso = formData.get('processoFabricacao');
                        this.querySelector('textarea[name="processoFabricacao"]').value = newProcesso;

                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text: data.message || "Ocorreu um problema.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição AJAX:", error);
                    Swal.fire({
                        title: "Erro de Conexão",
                        text: "Não foi possível conectar ao servidor.",
                        icon: "error"
                    });
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
        });
    }

    if (formInfoEmprendimento) {
        formInfoEmprendimento.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Salvando...`;

            fetch("../controllers/EmprendimentoController.php", {
                method: "POST",
                body: formData,
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na rede: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                            title: "Sucesso!",
                            text: data.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text: data.message || "Ocorreu um problema.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição AJAX:", error);
                    Swal.fire({
                        title: "Erro de Conexão",
                        text: "Não foi possível conectar ao servidor.",
                        icon: "error"
                    });
                })
                .finally(() => {
                    // Restaura o botão
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
        });
    }

    if (formInfoAdmin) {
        const imgPreview = formInfoAdmin.querySelector(".img-thumbnail");

        if (fotoPerfilInput && imgPreview) {
            fotoPerfilInput.addEventListener("change", function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (event) {
                        imgPreview.src = event.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        formInfoAdmin.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Salvando...`;

            // Envia para o AdminAssociadoController
            fetch("../controllers/AdminAssociadoController.php", {
                method: "POST",
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                            title: "Sucesso!",
                            text: data.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });

                        if (data.newFotoPath) {
                            imgPreview.src = `${data.newFotoPath}?t=${new Date().getTime()}`;
                        }

                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text: data.message || "Ocorreu um problema.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição AJAX:", error);
                    Swal.fire({
                        title: "Erro de Conexão",
                        text: "Não foi possível conectar ao servidor.",
                        icon: "error"
                    });
                })
                .finally(() => {
                    // Restaura o botão
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
        });
    }

    if (formImgFabricacao) {
        formImgFabricacao.addEventListener("submit", function (e) {
            e.preventDefault();
            handleImageUpload(this, 'fabricacao', 4);
        });
    }
    if (formImgGaleria) {
        formImgGaleria.addEventListener("submit", function (e) {
            e.preventDefault();
            handleImageUpload(this, 'galeria', 10);
        });
    }

    if (formCores) {
        const corPrincipalInput = formCores.querySelector("#corPrincipal");
        const corSecundariaInput = formCores.querySelector("#corSecundaria");
        const valorCorPrincipal = formCores.querySelector("#valorCorPrincipal");
        const valorCorSecundaria = formCores.querySelector("#valorCorSecundaria");

        corPrincipalInput.addEventListener('input', (e) => {
            const newColor = e.target.value;
            valorCorPrincipal.textContent = newColor;

            document.documentElement.style.setProperty('--cor-primaria', newColor);
        });
        corSecundariaInput.addEventListener('input', (e) => {
            const newColor = e.target.value;
            valorCorSecundaria.textContent = newColor;

            document.documentElement.style.setProperty('--cor-secundaria', newColor);
        });


        formCores.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const originalButtonText = submitButton.innerHTML;

            submitButton.disabled = true;
            submitButton.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Salvando...`;

            fetch("../controllers/EmprendimentoController.php", {
                method: "POST",
                body: formData,
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na rede: ' + response.statusText);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                            title: "Sucesso!",
                            text: data.message,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text: data.message || "Ocorreu um problema.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição AJAX:", error);
                    Swal.fire({
                        title: "Erro de Conexão",
                        text: "Não foi possível conectar ao servidor.",
                        icon: "error"
                    });
                })
                .finally(() => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalButtonText;
                });
        });
    }


    if (checkConfirma && btnEliminarConta) {
        checkConfirma.addEventListener('change', function () {
            btnEliminarConta.disabled = !this.checked;
        });
    }

    if (formEliminarConta) {
        formEliminarConta.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            const buttonOriginalText = btnEliminarConta.innerHTML;

            Swal.fire({
                title: 'Você tem CERTEZA ABSOLUTA?',
                text: "Esta ação é irreversível. Todos os seus dados, empreendimentos e imagens serão apagados permanentemente. Deseja continuar?",
                icon: 'error',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, eliminar minha conta!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {

                    btnEliminarConta.disabled = true;
                    btnEliminarConta.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Eliminando...`;

                    fetch("../controllers/UsuarioController.php", {
                        method: "POST",
                        body: formData
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'ok') {
                                Swal.fire({
                                    title: 'Conta Eliminada!',
                                    text: data.message,
                                    icon: 'success',
                                    allowOutsideClick: false,
                                    allowEscapeKey: false,
                                    confirmButtonText: 'Entendido'
                                }).then(() => {
                                    location.href = '../';
                                });
                            } else {
                                Swal.fire('Erro!', data.message, 'error');
                                btnEliminarConta.disabled = false;
                                btnEliminarConta.innerHTML = buttonOriginalText;
                            }
                        })
                        .catch(error => {
                            console.error("Erro na requisição AJAX:", error);
                            Swal.fire('Erro de Conexão', 'Não foi possível conectar ao servidor.', 'error');
                            btnEliminarConta.disabled = false;
                            btnEliminarConta.innerHTML = buttonOriginalText;
                        });
                }
            });
        });
    }

});

function handleImageUpload(formElement, tipo, limite) {
    const formData = new FormData(formElement);
    const submitButton = formElement.querySelector('button[type="submit"]');
    const originalButtonText = submitButton.innerHTML;
    const fileInput = formElement.querySelector('input[type="file"]');

    const inputName = (tipo === 'fabricacao') ? 'novasImagensFabricacao[]' : 'novasImagensGaleria[]';

    if (!fileInput.files || fileInput.files.length === 0) {
        Swal.fire('Atenção', 'Você deve selecionar pelo menos uma imagem.', 'warning');
        return;
    }

    formData.delete(inputName); // Limpa
    for (let i = 0; i < fileInput.files.length; i++) {
        formData.append(inputName, fileInput.files[i]);
    }

    formData.append('limite', limite);

    submitButton.disabled = true;
    submitButton.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-2"></i> Enviando...`;

    fetch("../controllers/EmprendimentoController.php", {
        method: "POST",
        body: formData,
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'ok') {
                Swal.fire({
                    title: "Sucesso!",
                    text: data.message,
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                });

                if (data.novasImagens) {
                    data.novasImagens.forEach(img => {
                        adicionarImagemAoDOM(tipo, img.id, `../${img.caminho}`);
                    });
                }
                fileInput.value = null;

            } else {
                Swal.fire("Erro!", data.message || "Ocorreu um problema.", "error");
            }
        })
        .catch(error => {
            console.error("Erro na requisição AJAX:", error);
            Swal.fire("Erro de Conexão", "Não foi possível conectar ao servidor.", "error");
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
        });
}

function adicionarImagemAoDOM(tipo, id, caminho) {
    const containerId = (tipo === 'fabricacao') ? 'containerImagensFabricacao' : 'containerImagensGaleria';
    const container = document.getElementById(containerId);

    const pMensagem = container.querySelector('p.text-muted.fst-italic');
    if (pMensagem) {
        pMensagem.remove();
    }

    const div = document.createElement('div');
    div.className = 'border rounded position-relative img-preview-container shadow-sm';
    div.innerHTML = `
        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
            onclick="eliminarImagem(this, '${tipo}', ${id})"
            title="Eliminar imagem">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <img src="${caminho}?t=${new Date().getTime()}" alt="Nova imagem">
    `;
    container.appendChild(div);
}


function eliminarImagem(buttonElement, tipo, id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Esta imagem será eliminada permanentemente!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, eliminar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {

            const formData = new FormData();
            formData.append('accion', 'eliminarImagem');
            formData.append('tipo', tipo);
            formData.append('id', id);

            // Mostra um spinner no botão
            buttonElement.disabled = true;
            buttonElement.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            fetch("../controllers/EmprendimentoController.php", {
                method: "POST",
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire(
                            'Eliminada!',
                            data.message,
                            'success'
                        );
                        buttonElement.closest('.img-preview-container').remove();
                    } else {
                        Swal.fire(
                            'Erro!',
                            data.message,
                            'error'
                        );
                        buttonElement.disabled = false;
                        buttonElement.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                    }
                })
                .catch(error => {
                    console.error("Erro na requisição AJAX:", error);
                    Swal.fire('Erro de Conexão', 'Não foi possível conectar ao servidor.', 'error');

                    buttonElement.disabled = false;
                    buttonElement.innerHTML = '<i class="fa-solid fa-xmark"></i>';
                });
        }
    });
}