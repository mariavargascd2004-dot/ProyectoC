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
        let filesArray = [];

        input.addEventListener('change', (event) => {
            const newFiles = Array.from(event.target.files);

            // Si hay límite, verificar cuántas hay actualmente
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

            filesArray = filesArray.concat(newFiles);
            renderPreviews();
            input.value = "";
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
                    <img src="${e.target.result}" alt="Imagem">
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
                const index = e.target.dataset.index;
                filesArray.splice(index, 1);
                renderPreviews();
            }
        });

        renderPreviews();
    }

    handleMultipleImages('fabricacaoInput', 'fabricacaoPreview', 4); // límite de 4 imágenes
    handleMultipleImages('galeriaInput', 'galeriaPreview');


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
            alert("contraseñas no coinciden")
            return;
        }

        //Llamar funcion registrar(/ajax)
        registrarUsuario(nombre, email, password, tipo);

    });
    //Registro de Emprendedor
    function irARegistrarAdminAssociado() {

        const inputFoto = document.getElementById("fotoPerfil");
        const formData = new FormData(formRegistrarEmprendedor);

        let nombre = formData.get("nombre");
        let apellido = formData.get("apellido");
        let descripcion = formData.get("descripcion");
        let email = formData.get("email");
        let password = formData.get("password");
        let password2 = formData.get("password2");
        let tipo = "associado";

        if (password !== password2) {
            Swal.fire({ title: 'Error', text: 'Las contraseñas no coinciden', icon: 'error' });
            return;
        }


        //Validar la foto del usuario
        const file = inputFoto.files[0];
        if (file) {
            const maxBytes = 2 * 1024 * 1024; // 2MB por ejemplo
            if (file.size > maxBytes) {
                Swal.fire({ title: 'Archivo muy grande', text: 'Máx 2MB', icon: 'warning' });
                return;
            }
        }


        //Llamar funcion registrar(/ajax)
        registrarAdminAssociado(nombre, apellido, descripcion, file, email, password, tipo);

    };

});
