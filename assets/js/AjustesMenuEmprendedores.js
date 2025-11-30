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
    const formIdentidadeVisual = document.getElementById("formIdentidadeVisual");
    
    
    if (formIdentidadeVisual) {
        // Lógica para previsualizar el nombre del archivo seleccionado (UX)
        const inputLogo = document.getElementById('inputLogo');
        const inputPooster = document.getElementById('inputPooster');

        if(inputLogo) {
            inputLogo.addEventListener('change', function(e) {
                const target = document.getElementById('previewLogo');
                if (this.files && this.files[0]) target.textContent = "Arquivo selecionado: " + this.files[0].name;
            });
        }
        if(inputPooster) {
            inputPooster.addEventListener('change', function(e) {
                const target = document.getElementById('previewPooster');
                if (this.files && this.files[0]) target.textContent = "Arquivo selecionado: " + this.files[0].name;
            });
        }

        // Lógica del SUBMIT por AJAX
        formIdentidadeVisual.addEventListener("submit", function (e) {
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

                        if (data.logoPath) {
                            const imgLogo = document.getElementById('imgLogoActual');
                            if(imgLogo) imgLogo.src = "../" + data.logoPath + "?t=" + new Date().getTime();
                            document.getElementById('previewLogo').textContent = ""; // Limpiar texto
                        }
                        if (data.poosterPath) {
                            const imgPooster = document.getElementById('imgPoosterActual');
                            if(imgPooster) imgPooster.src = "../" + data.poosterPath + "?t=" + new Date().getTime();
                            document.getElementById('previewPooster').textContent = ""; // Limpiar texto
                        }

                        // Limpiar los inputs file
                        this.reset();
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

if (document.getElementById('gerenciadorCategorias')) {

    // --- Variáveis Globais ---
    let minhasCategorias = []; 
    const idEmprendimento = document.getElementById('idEmprendimentoAtual').value;

    // --- Elementos do DOM ---
    const listaCatEl = document.getElementById('listaCategorias');
    const listaSubEl = document.getElementById('listaSubcategorias');
    const formCat = document.getElementById('formNovaCategoria');
    const inputCat = document.getElementById('inputNomeCategoria');
    const formSub = document.getElementById('formNovaSubcategoria');
    const inputSub = document.getElementById('inputNomeSubcategoria');
    const idCatAtivaInput = document.getElementById('idCategoriaAtiva');
    const nomeCatAtivaEl = document.getElementById('nomeCategoriaAtiva');
    const msgSelecioneCat = document.getElementById('msgSelecioneCategoria');
    const hrSubcat = document.getElementById('hrSubcategorias');

    // --- Função Principal de Renderização ---
    function renderizarTudo() {
        renderizarCategorias();
        const idCatAtiva = idCatAtivaInput.value;
        if (idCatAtiva) {
            const cat = minhasCategorias.find(c => c.idCategoria == idCatAtiva);
            if (cat) renderizarSubcategorias(cat);
            else resetarSubcategorias();
        } else {
            resetarSubcategorias();
        }
    }

    function renderizarCategorias() {
        listaCatEl.innerHTML = ''; 
        if (minhasCategorias.length === 0) {
            listaCatEl.innerHTML = '<li class="list-group-item text-center text-muted">Nenhuma categoria cadastrada.</li>';
            return;
        }

        minhasCategorias.forEach(cat => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.dataset.id = cat.idCategoria;

            const idCatAtiva = idCatAtivaInput.value;
            if (cat.idCategoria == idCatAtiva) {
                li.classList.add('active');
            }

            li.innerHTML = `
                <span class="nome-categoria">${cat.nombre}</span> <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary btn-editar-cat" title="Editar"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-sm btn-outline-danger btn-excluir-cat" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                </div>
            `;

            // Evento para selecionar a categoria
            li.querySelector('.nome-categoria').addEventListener('click', () => {
                idCatAtivaInput.value = cat.idCategoria;
                nomeCatAtivaEl.textContent = cat.nombre; 
                renderizarTudo();
            });

            // Eventos CRUD
            li.querySelector('.btn-editar-cat').addEventListener('click', () => editarCategoria(cat));
            li.querySelector('.btn-excluir-cat').addEventListener('click', () => excluirCategoria(cat));

            listaCatEl.appendChild(li);
        });
    }

    function renderizarSubcategorias(cat) {
        listaSubEl.innerHTML = '';
        msgSelecioneCat.style.display = 'none';
        formSub.style.display = 'flex';
        hrSubcat.style.display = 'block';

        if (!cat.subcategorias || cat.subcategorias.length === 0) {
            listaSubEl.innerHTML = '<li class="list-group-item text-center text-muted">Nenhuma subcategoria cadastrada.</li>';
            return;
        }

        cat.subcategorias.forEach(sub => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.dataset.id = sub.idSubcategoria;
            li.innerHTML = `
                <span class="nome-subcategoria">${sub.nombre}</span> <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary btn-editar-sub" title="Editar"><i class="fa-solid fa-pen"></i></button>
                    <button class="btn btn-sm btn-outline-danger btn-excluir-sub" title="Excluir"><i class="fa-solid fa-trash"></i></button>
                </div>
            `;

            // Eventos CRUD
            li.querySelector('.btn-editar-sub').addEventListener('click', () => editarSubcategoria(sub));
            li.querySelector('.btn-excluir-sub').addEventListener('click', () => excluirSubcategoria(sub));

            listaSubEl.appendChild(li);
        });
    }

    function resetarSubcategorias() {
        listaSubEl.innerHTML = '';
        msgSelecioneCat.style.display = 'block';
        formSub.style.display = 'none';
        hrSubcat.style.display = 'none';
        nomeCatAtivaEl.textContent = '---';
        idCatAtivaInput.value = '';
    }


    // Carregar Inicial
    function carregarDados() {
        const formData = new FormData();
        formData.append('accion', 'listarTudo');
        formData.append('idEmprendimento', idEmprendimento);

        fetch("../controllers/CategoriaController.php", { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    minhasCategorias = data.categorias;
                    renderizarTudo();
                } else {
                    listaCatEl.innerHTML = `<li class="list-group-item text-danger">${data.message}</li>`;
                }
            })
            .catch(err => {
                console.error(err);
                listaCatEl.innerHTML = `<li class="list-group-item text-danger">Erro ao carregar dados.</li>`;
            });
    }

    // --- CRUD CATEGORIAS ---
    formCat.addEventListener('submit', (e) => {
        e.preventDefault();
        const nome = inputCat.value.trim();
        if (!nome) return;

        const formData = new FormData();
        formData.append('accion', 'criar');
        formData.append('idEmprendimento', idEmprendimento);
        formData.append('nome', nome);

        fetch("../controllers/CategoriaController.php", { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    minhasCategorias.push(data.categoria);
                    renderizarTudo();
                    inputCat.value = '';
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire('Erro!', data.message, 'error');
                }
            });
    });

    function editarCategoria(cat) {
        Swal.fire({
            title: 'Editar Categoria',
            input: 'text',
            inputValue: cat.nombre,
            showCancelButton: true,
            confirmButtonText: 'Salvar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value !== cat.nombre) { 
                const formData = new FormData();
                formData.append('accion', 'atualizar');
                formData.append('idCategoria', cat.idCategoria);
                formData.append('nome', result.value);

                fetch("../controllers/CategoriaController.php", { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            cat.nombre = result.value; 
                            renderizarTudo();
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                        } else {
                            Swal.fire('Erro!', data.message, 'error');
                        }
                    });
            }
        });
    }

    function excluirCategoria(cat) {
        Swal.fire({
            title: `Excluir "${cat.nombre}"?`,
            text: "Esta ação é irreversível! Verifique se não há subcategorias ou produtos associados.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('accion', 'excluir');
                formData.append('idCategoria', cat.idCategoria);

                fetch("../controllers/CategoriaController.php", { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            minhasCategorias = minhasCategorias.filter(c => c.idCategoria != cat.idCategoria);
                            // Se a categoria ativa foi excluída, resetar o painel de subcategorias
                            if (idCatAtivaInput.value == cat.idCategoria) {
                                resetarSubcategorias();
                            }
                            renderizarTudo();
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                        } else {
                            Swal.fire('Erro!', data.message, 'error');
                        }
                    });
            }
        });
    }

    // --- CRUD SUBCATEGORIAS ---
    formSub.addEventListener('submit', (e) => {
        e.preventDefault();
        const nome = inputSub.value.trim();
        const idCatAtiva = idCatAtivaInput.value;
        if (!nome || !idCatAtiva) return;

        const formData = new FormData();
        formData.append('accion', 'criar');
        formData.append('idCategoria', idCatAtiva);
        formData.append('nome', nome);

        fetch("../controllers/SubcategoriaController.php", { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'ok') {
                    const cat = minhasCategorias.find(c => c.idCategoria == idCatAtiva);
                    cat.subcategorias.push(data.subcategoria);
                    renderizarTudo();
                    inputSub.value = '';
                    Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                } else {
                    Swal.fire('Erro!', data.message, 'error');
                }
            });
    });

    function editarSubcategoria(sub) {
        Swal.fire({
            title: 'Editar Subcategoria',
            input: 'text',
            inputValue: sub.nombre, 
            showCancelButton: true,
            confirmButtonText: 'Salvar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value !== sub.nombre) { 
                const formData = new FormData();
                formData.append('accion', 'atualizar');
                formData.append('idSubcategoria', sub.idSubcategoria);
                formData.append('nome', result.value);

                fetch("../controllers/SubcategoriaController.php", { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            sub.nombre = result.value; 
                            renderizarTudo();
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                        } else {
                            Swal.fire('Erro!', data.message, 'error');
                        }
                    });
            }
        });
    }

    function excluirSubcategoria(sub) {
        Swal.fire({
            title: `Excluir "${sub.nombre}"?`, 
            text: "Verifique se não há produtos associados a esta subcategoria.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('accion', 'excluir');
                formData.append('idSubcategoria', sub.idSubcategoria);

                fetch("../controllers/SubcategoriaController.php", { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'ok') {
                            const idCatAtiva = idCatAtivaInput.value;
                            const cat = minhasCategorias.find(c => c.idCategoria == idCatAtiva);
                            cat.subcategorias = cat.subcategorias.filter(s => s.idSubcategoria != sub.idSubcategoria);
                            renderizarTudo();
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 2000 });
                        } else {
                            Swal.fire('Erro!', data.message, 'error');
                        }
                    });
            }
        });
    }

    carregarDados();
    
    
}