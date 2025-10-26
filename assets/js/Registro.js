document.addEventListener("DOMContentLoaded", () => {

    //Para cambianr entre registro de Usuario y Emprendedor
    const btnUsuario = document.getElementById("login_logarComo--usuario");
    const btnEmprendedor = document.getElementById("login_logarComo--emprendedor");
    const formUsuario = document.querySelector(".login__usuario");
    const formEmprendedor = document.querySelector(".login__emprendedor");
    //Cambio entre secciones del Emprendedor
    const steps = document.querySelectorAll('.step');
    const navLinks = document.querySelectorAll('#stepsNav .nav-link');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    let currentStep = 0;
    //Registro
    const formRegistrarUsuario = document.getElementById("formRegistrar--usuario");
    const formRegistrarEmprendedor = document.getElementById("formRegistrar--emprendedor");

    //Imagenes
    let filesArrayFabricacao = [];
    let filesArrayGaleria = [];


    // ------------------------------------------------------------------------------//
    //                                       Inicio
    // ------------------------------------------------------------------------------//

    //Cambiar entre Usuario y Emprendedor
    const activar = (activo, inactivo, mostrar, ocultar) => {
        // Botones
        activo.classList.add("fundoVermelho", "text-white");
        inactivo.classList.remove("fundoVermelho", "text-white");
        inactivo.classList.add("fundoAmarelo");
        // Formularios
        mostrar.style.display = "block";
        setTimeout(() => {
            mostrar.style.opacity = "1";
        }, 100);
        ocultar.style.display = "none";
        ocultar.style.opacity = "0.1";
    };

    btnUsuario.addEventListener("click", () =>
        activar(btnUsuario, btnEmprendedor, formUsuario, formEmprendedor)
    );

    btnEmprendedor.addEventListener("click", () =>
        activar(btnEmprendedor, btnUsuario, formEmprendedor, formUsuario)
    );
    //Cambio entre secciones del Emprendedor
    function showStep(index) {
        steps.forEach((step, i) => {
            step.classList.toggle('active', i === index);
            navLinks[i].classList.toggle('active', i === index);
        });
        prevBtn.style.display = index === 0 ? 'none' : 'inline-block';
        nextBtn.textContent = index === steps.length - 1 ? 'Cadastrar' : 'Siguiente →';
    }
    nextBtn.addEventListener('click', () => {
        const currentStepEl = steps[currentStep];
        const inputs = currentStepEl.querySelectorAll('input, textarea');
        for (let i = 0; i < inputs.length; i++) {
            if (!inputs[i].checkValidity()) {
                inputs[i].reportValidity();
                inputs[i].focus();
                return;
            }
        }
        if (currentStep < steps.length - 1) {
            currentStep++;
            showStep(currentStep);
        } else {
            irARegistrarAdminAssociado();
        }
    });
    prevBtn.addEventListener('click', () => {
        if (currentStep > 0) currentStep--;
        showStep(currentStep);
    });
    navLinks.forEach((link, index) => {
        link.addEventListener('click', () => {
            const currentStepEl = steps[currentStep];
            const inputs = currentStepEl.querySelectorAll('input, textarea');
            for (let i = 0; i < inputs.length; i++) {
                if (!inputs[i].checkValidity()) {
                    inputs[i].reportValidity();
                    inputs[i].focus();
                    return;
                }
            }
            currentStep = index;
            showStep(currentStep);
        });
    });
    showStep(currentStep);

    function handleMultipleImages(inputId, previewId, limit = null) {
        const input = document.getElementById(inputId);
        const previewContainer = document.getElementById(previewId);

        // Determinar qué array global usar
        let filesArray = inputId === 'fabricacaoInput' ? filesArrayFabricacao : filesArrayGaleria;

        input.addEventListener('change', (event) => {
            const newFiles = Array.from(event.target.files);

            // Validar límite
            if (limit && filesArray.length + newFiles.length > limit) {
                const remaining = limit - filesArray.length;
                Swal.fire({
                    icon: 'warning',
                    title: 'Limite de imagens atingido',
                    text: remaining > 0
                        ? `Você só pode adicionar mais ${remaining} imagem${remaining === 1 ? '' : 'ns'}.`
                        : `Você já atingiu o limite máximo de ${limit} imagens.`,
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Entendido',
                });
                input.value = "";
                return;
            }

            // Agregar nuevos archivos al array global
            filesArray.push(...newFiles);
            renderPreviews();
            input.value = ""; // Limpiar input

        });

        function renderPreviews() {
            previewContainer.innerHTML = "";
            if (filesArray.length === 0) {
                previewContainer.innerHTML = "<span class='text-muted'>Sem imagens</span>";
                return;
            }

            filesArray.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const div = document.createElement('div');
                    div.classList.add('image-preview');
                    div.innerHTML = `
                    <img src="${e.target.result}" alt="Imagem" style="max-width: 100px; max-height: 100px;">
                    <span>${file.name}</span>
                    <button type="button" data-index="${index}">&times;</button>
                `;
                    previewContainer.appendChild(div);
                };
                reader.readAsDataURL(file);
            });
        }

        // Eliminar imagen
        previewContainer.addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON') {
                const index = parseInt(e.target.dataset.index);
                filesArray.splice(index, 1);
                renderPreviews();
            }
        });

        // Inicializar
        renderPreviews();
    }
    handleMultipleImages('fabricacaoInput', 'fabricacaoPreview', 4);
    handleMultipleImages('galeriaInput', 'galeriaPreview', 10);


    //Registro de Usuario
    formRegistrarUsuario.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(formRegistrarUsuario);
        let nombre = formData.get("nombre");
        let email = formData.get("email");
        let password = formData.get("password");
        let password2 = formData.get("password2");
        let tipo = "cliente";

        if (password !== password2) {
            Swal.fire({
                title: 'Senhas não coincidem',
                text: 'Por favor, verifique se as senhas são iguais em ambos os campos.',
                icon: 'error',
                confirmButtonText: 'Entendido',
                background: '#B2442E',
                color: '#FFFFFF',
                confirmButtonColor: '#FDCB29',
                iconColor: '#FDCB29'
            }); return;
        }

        //Registrar Usuario
        fetch("../controllers/UsuarioController.php", {
            method: "POST",
            body: new URLSearchParams({
                accion: "registrar",
                nombre: nombre,
                email: email,
                password: password,
                tipo: tipo
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'error') {
                    Swal.fire({
                        title: 'Erro no registro',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Tentar novamente',
                        background: '#B2442E',
                        color: '#FFFFFF',
                        confirmButtonColor: '#FDCB29',
                        iconColor: '#FDCB29'
                    });
                } else {
                    Swal.fire({
                        title: '¡Registro realizado com sucesso!',
                        html: data.message + "<br><br>Redirecionando...",
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
                        window.location.href = "../";
                    }, 2980);
                }
            })
            .catch(err => {
                Swal.fire({
                    title: 'Erro de conexão',
                    text: 'Não foi possível conectar ao servidor. Por favor, tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Tentar novamente',
                    confirmButtonColor: '#d33'
                });
            });
    });
    function irARegistrarAdminAssociado() {
        const inputFoto = document.getElementById("fotoPerfil");
        const form = document.getElementById("formRegistrar--emprendedor");

        let nombre = form.querySelector('input[name="nombre"]').value;
        let apellido = form.querySelector('input[name="apellido"]').value;
        let descripcion = form.querySelector('textarea[name="descripcion"]').value;
        let email = form.querySelector('input[name="email"]').value;
        let password = form.querySelector('input[name="password"]').value;
        let password2 = form.querySelector('input[name="password2"]').value;
        let tipo = "associado";

        if (password !== password2) {
            Swal.fire({
                title: 'Senhas não coincidem',
                text: 'As senhas digitadas não são iguais. Por favor, verifique.',
                icon: 'warning',
                confirmButtonText: 'Corrigir',
                confirmButtonColor: '#ffc107',
                background: '#fff',
                color: '#333'
            }); return;
        }

        // Validar la foto del usuario
        const file = inputFoto.files[0];
        if (file) {
            const maxBytes = 2 * 1024 * 1024;
            if (file.size > maxBytes) {
                Swal.fire({
                    title: 'Arquivo muito grande',
                    text: 'A imagem de perfil não deve ultrapassar 2MB.',
                    icon: 'warning',
                    confirmButtonText: 'Selecionar outra',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }
        }

        const formData = new FormData();
        formData.append("accion", "registrarAdmin");
        formData.append("nombre", nombre);
        formData.append("apellido", apellido);
        formData.append("descripcion", descripcion);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("tipo", tipo);

        if (file) {
            formData.append("fotoPerfil", file);
        }

        // Mostrar loading
        Swal.fire({
            title: 'Processando registro...',
            text: 'Aguarde enquanto cadastramos suas informações.',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            background: '#DCA700',
            color: '#000000',
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch("../controllers/AdminAssociadoController.php", {
            method: "POST",
            body: formData
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'error') {
                    Swal.fire({
                        title: 'Erro no cadastro',
                        text: data.message,
                        icon: 'error',
                        confirmButtonText: 'Tentar novamente',
                        background: '#B2442E',
                        color: '#FFFFFF',
                        confirmButtonColor: '#FDCB29',
                        iconColor: '#FDCB29'
                    });
                } else {
                    Swal.fire({
                        title: 'Administrador cadastrado!',
                        text: 'Prosseguindo para o cadastro do empreendimento...',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        background: '#DCA700',
                        color: '#000000',
                        iconColor: '#FFFFFF'
                    });
                    setTimeout(() => {
                        registrarEmprendimento(data.idUsuario);
                    }, 2000);
                }
            })
            .catch(err => {
                Swal.fire({
                    title: 'Erro de conexão',
                    text: 'Não foi possível conectar ao servidor. Verifique sua conexão e tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Tentar novamente',
                    background: '#B2442E',
                    color: '#FFFFFF',
                    confirmButtonColor: '#FDCB29',
                    iconColor: '#FDCB29'
                });
            });
    }

    //Registrar Emprendimiento
    function registrarEmprendimento(adminId) {

        const form = document.getElementById("formRegistrar--emprendedor");
        const formData = new FormData();

        // Datos básicos del emprendimiento
        formData.append("accion", "registrar");
        formData.append("adminAssociado_idUsuario", adminId);
        formData.append("nome", form.querySelector('input[name="nombreEmprendimento"]').value);
        formData.append("historia", form.querySelector('textarea[name="historia"]').value);
        formData.append("processoFabricacao", form.querySelector('textarea[name="processoFabricacao"]').value);
        formData.append("telefone", form.querySelector('input[name="telefone"]').value || '');
        formData.append("celular", form.querySelector('input[name="celular"]').value);
        formData.append("ubicacao", form.querySelector('input[name="ubicacao"]').value);
        formData.append("instagram", form.querySelector('input[name="instragram"]').value || '');
        formData.append("facebook", form.querySelector('input[name="facebook"]').value || '');

        // Logo
        const logoFile = form.querySelector('input[name="logoEmprendimento"]').files[0];
        if (logoFile) {
            formData.append("logoEmprendimento", logoFile);
        }

        filesArrayFabricacao.forEach((file, index) => {
            formData.append("imagemsFabricacao[]", file);
        });

        filesArrayGaleria.forEach((file, index) => {
            formData.append("imagemsGaleria[]", file);
        });

        // Validaciones
        if (!formData.get("nome") || !formData.get("historia") || !formData.get("processoFabricacao") ||
            !formData.get("celular") || !formData.get("ubicacao")) {
            Swal.fire({
                title: 'Campos obrigatórios',
                text: 'Por favor, preencha todos os campos obrigatórios do empreendimento.',
                icon: 'error',
                confirmButtonText: 'Completar dados',
                background: '#B2442E',
                color: '#FFFFFF',
                confirmButtonColor: '#FDCB29',
                iconColor: '#FDCB29'
            });
            return;
        }

        Swal.fire({
            title: 'Cadastrando empreendimento...',
            text: 'Estamos processando suas informações. Isso pode levar alguns instantes.',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false,
            background: '#DCA700',
            color: '#000000',
            didOpen: () => {
                Swal.showLoading();
            }
        });


        // Enviar al servidor
        fetch("../controllers/EmprendimentoController.php", {
            method: "POST",
            body: formData
        })
            .then(async res => {
                const text = await res.text();
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error("Respuesta no JSON: " + text.substring(0, 200));
                }
            })
            .then(data => {
                if (data.status === 'ok') {
                    filesArrayFabricacao = [];
                    filesArrayGaleria = [];

                    Swal.fire({
                        title: 'Cadastro concluído!',
                        text: 'Seu empreendimento foi registrado com sucesso!',
                        icon: 'success',
                        confirmButtonText: 'Aceitar',
                        background: '#DCA700',
                        color: '#000000',
                        confirmButtonColor: '#B2442E',
                        iconColor: '#FFFFFF'
                    }).then(() => {
                        window.location.href = "../";
                    });
                } else {
                    Swal.fire({
                        title: 'Erro no cadastro',
                        text: data.message || 'Ocorreu um erro inesperado durante o cadastro.',
                        icon: 'error',
                        confirmButtonText: 'Tentar novamente',
                        background: '#B2442E',
                        color: '#FFFFFF',
                        confirmButtonColor: '#FDCB29',
                        iconColor: '#FDCB29'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    title: 'Erro de conexão',
                    text: 'Não foi possível conectar ao servidor. Verifique sua conexão e tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Tentar novamente',
                    background: '#B2442E',
                    color: '#FFFFFF',
                    confirmButtonColor: '#FDCB29',
                    iconColor: '#FDCB29'
                });
            });
    }



});
