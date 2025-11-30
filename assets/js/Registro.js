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

    // Función de validación general
    function validateField(field) {
        field.classList.remove('is-invalid', 'is-valid');
        
        if (field.hasAttribute('required') && !field.value.trim()) {
            field.classList.add('is-invalid');
            return false;
        }

        if (field.type === 'email' && field.value) {
            const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
            if (!emailRegex.test(field.value)) {
                field.classList.add('is-invalid');
                return false;
            }
        }

        if (field.hasAttribute('pattern') && field.value) {
            const pattern = new RegExp(field.getAttribute('pattern'));
            if (!pattern.test(field.value)) {
                field.classList.add('is-invalid');
                return false;
            }
        }

        if (field.hasAttribute('minlength') && field.value) {
            const minLength = parseInt(field.getAttribute('minlength'));
            if (field.value.length < minLength) {
                field.classList.add('is-invalid');
                return false;
            }
        }

        if (field.hasAttribute('maxlength') && field.value) {
            const maxLength = parseInt(field.getAttribute('maxlength'));
            if (field.value.length > maxLength) {
                field.classList.add('is-invalid');
                return false;
            }
        }

        // Validación de archivos
        if (field.type === 'file' && field.hasAttribute('required') && !field.files.length) {
            field.classList.add('is-invalid');
            return false;
        }

        if (field.type === 'file' && field.files.length > 0) {
            const file = field.files[0];
            const maxSize = 2 * 1024 * 1024; // 2MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
            
            if (file.size > maxSize) {
                field.classList.add('is-invalid');
                return false;
            }
            
            if (!allowedTypes.includes(file.type)) {
                field.classList.add('is-invalid');
                return false;
            }
        }

        if (field.value) {
            field.classList.add('is-valid');
        }
        
        return true;
    }

    // Validar paso completo
    function validateStep(stepIndex) {
        const currentStepEl = steps[stepIndex];
        const inputs = currentStepEl.querySelectorAll('input, textarea, select');
        let isValid = true;

        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
                // Enfocar el primer campo inválido
                if (isValid === false) {
                    input.focus();
                    isValid = false; // Ya es false, pero para claridad
                }
            }
        });

        // Validaciones específicas para imágenes
        if (stepIndex === 2) {
            // Validar imágenes de fabricação
            if (filesArrayFabricacao.length !== 4) {
                document.getElementById('fabricacaoInput').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('fabricacaoInput').classList.remove('is-invalid');
            }

            // Validar imágenes de galeria
            if (filesArrayGaleria.length < 1) {
                document.getElementById('galeriaInput').classList.add('is-invalid');
                isValid = false;
            } else {
                document.getElementById('galeriaInput').classList.remove('is-invalid');
            }
        }

        return isValid;
    }

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
        if (!validateStep(currentStep)) {
            Swal.fire({
                title: 'Campos inválidos',
                text: 'Por favor, corrija os campos destacados em vermelho antes de continuar.',
                icon: 'warning',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#ffc107'
            });
            return;
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
            if (!validateStep(currentStep)) {
                Swal.fire({
                    title: 'Campos inválidos',
                    text: 'Por favor, corrija os campos destacados em vermelho antes de mudar de etapa.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }
            currentStep = index;
            showStep(currentStep);
        });
    });

    showStep(currentStep);

    // Validación en tiempo real para campos de formulario
    function setupRealTimeValidation() {
        const allInputs = document.querySelectorAll('input, textarea');
        allInputs.forEach(input => {
            input.addEventListener('blur', () => validateField(input));
            input.addEventListener('input', () => {
                if (input.classList.contains('is-invalid')) {
                    validateField(input);
                }
            });
        });
    }

    setupRealTimeValidation();

    function handleMultipleImages(inputId, previewId, limit = null) {
        const input = document.getElementById(inputId);
        const previewContainer = document.getElementById(previewId);

        // Determinar qué array global usar
        let filesArray = inputId === 'fabricacaoInput' ? filesArrayFabricacao : filesArrayGaleria;

        input.addEventListener('change', (event) => {
            const newFiles = Array.from(event.target.files);

            // Validar tipos de archivo y tamaño
            const invalidFiles = newFiles.filter(file => {
                const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                const maxSize = 2 * 1024 * 1024; // 2MB
                return !allowedTypes.includes(file.type) || file.size > maxSize;
            });

            if (invalidFiles.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Arquivo inválido',
                    text: 'Algumas imagens não são válidas. Use apenas JPG, PNG ou GIF com até 2MB.',
                    confirmButtonColor: '#ffc107',
                    confirmButtonText: 'Entendido',
                });
                // Remover archivos inválidos
                newFiles = newFiles.filter(file => !invalidFiles.includes(file));
            }

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
            validateField(input); // Actualizar validación

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
                validateField(input); // Actualizar validación después de eliminar
            }
        });

        // Inicializar
        renderPreviews();
    }

    handleMultipleImages('fabricacaoInput', 'fabricacaoPreview', 4);
    handleMultipleImages('galeriaInput', 'galeriaPreview', 10);

    // Validación completa del formulario de usuario
    function validateUsuarioForm() {
        const inputs = formRegistrarUsuario.querySelectorAll('input[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });

        // Validar que las contraseñas coincidan
        const password = formRegistrarUsuario.querySelector('input[name="password"]');
        const password2 = formRegistrarUsuario.querySelector('input[name="password2"]');
        
        if (password.value !== password2.value) {
            password.classList.add('is-invalid');
            password2.classList.add('is-invalid');
            isValid = false;
            
            Swal.fire({
                title: 'Senhas não coincidem',
                text: 'Por favor, verifique se as senhas são iguais em ambos os campos.',
                icon: 'error',
                confirmButtonText: 'Entendido',
                background: '#B2442E',
                color: '#FFFFFF',
                confirmButtonColor: '#FDCB29',
                iconColor: '#FDCB29'
            });
        }

        return isValid;
    }

    //Registro de Usuario
    formRegistrarUsuario.addEventListener("submit", function (event) {
        event.preventDefault();

        if (!validateUsuarioForm()) {
            return;
        }

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
        // Validar todo el formulario del emprendedor primero
        if (!validateStep(0) || !validateStep(1) || !validateStep(2)) {
            Swal.fire({
                title: 'Formulário incompleto',
                text: 'Por favor, complete todas as etapas do formulário corretamente antes de cadastrar.',
                icon: 'warning',
                confirmButtonText: 'Verificar',
                confirmButtonColor: '#ffc107'
            });
            return;
        }

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
        formData.append("corPrincipal", form.querySelector('input[name="corPrincipal"]').value);
        formData.append("corSecundaria", form.querySelector('input[name="corSecundaria"]').value);
        formData.append("historia", form.querySelector('textarea[name="historia"]').value);
        formData.append("processoFabricacao", form.querySelector('textarea[name="processoFabricacao"]').value);
        formData.append("telefone", form.querySelector('input[name="telefone"]').value || '');
        formData.append("celular", form.querySelector('input[name="celular"]').value);
        formData.append("horarios", form.querySelector('input[name="horarios"]').value);
        formData.append("ubicacao", form.querySelector('input[name="ubicacao"]').value);
        formData.append("instagram", form.querySelector('input[name="instragram"]').value || '');
        formData.append("facebook", form.querySelector('input[name="facebook"]').value || '');

        // Logo
        const logoFile = form.querySelector('input[name="logoEmprendimento"]').files[0];
        if (logoFile) {
            formData.append("logoEmprendimento", logoFile);
        }

        // Pooster
        const poosterFile = form.querySelector('input[name="poosterEmprendimento"]').files[0];
        if (poosterFile) {
            formData.append("poosterEmprendimento", poosterFile);
        }

        filesArrayFabricacao.forEach((file, index) => {
            formData.append("imagemsFabricacao[]", file);
        });

        filesArrayGaleria.forEach((file, index) => {
            formData.append("imagemsGaleria[]", file);
        });

        // Validaciones finales
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

        // Validar que se hayan subido exactamente 4 imágenes de fabricación
        if (filesArrayFabricacao.length !== 4) {
            Swal.fire({
                title: 'Imagens de fabricação incompletas',
                text: 'São necessárias exatamente 4 imagens do processo de fabricação.',
                icon: 'warning',
                confirmButtonText: 'Adicionar imagens',
                confirmButtonColor: '#ffc107'
            });
            return;
        }

        // Validar que se haya subido al menos 1 imagen de galería
        if (filesArrayGaleria.length < 1) {
            Swal.fire({
                title: 'Galeria vazia',
                text: 'É necessário adicionar pelo menos 1 imagem para a galeria.',
                icon: 'warning',
                confirmButtonText: 'Adicionar imagens',
                confirmButtonColor: '#ffc107'
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
                        text: 'Seu empreendimento foi registrado com sucesso! Agora, aguarde a ativação da sua conta pelo administrador antes de realizar o login.',
                        icon: 'success',
                        confirmButtonText: 'Entendido',
                        background: '#DCA700',
                        color: '#000000',
                        confirmButtonColor: '#B2442E',
                        iconColor: '#FFFFFF'
                    }).then(() => {
                        window.location.href = "Login.html";
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