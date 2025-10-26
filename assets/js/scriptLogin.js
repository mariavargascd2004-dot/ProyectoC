document.addEventListener("DOMContentLoaded", () => {

    // ------------------------------------------------------------------------------//
    //                                       Variables Globales
    // ------------------------------------------------------------------------------//

    //Para cambianr entre registro de Usuario y Emprendedor
    const btnUsuario = document.getElementById("login_logarComo--usuario");
    const btnEmprendedor = document.getElementById("login_logarComo--emprendedor");
    const formUsuario = document.querySelector(".login__usuario");
    const formEmprendedor = document.querySelector(".login__emprendedor");

    //Login 
    const formLogin__usuario = document.getElementById("formLogin--usuario");

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

    //Login
    formLogin__usuario.addEventListener("submit", function (event) {
        event.preventDefault();

        const formData = new FormData(formLogin__usuario);
        let email = formData.get("email");
        let password = formData.get("password");

        fetch("../controllers/UsuarioController.php", {
            method: "POST",
            body: new URLSearchParams({
                accion: "login",
                email: email,
                password: password,
            })
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === "ok") {
                    Swal.fire({
                        title: 'Sucesso!',
                        html: data.message + "<br><br> redirecionando...",
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
                else if (data.status === 'incorrect_Credencial') {

                    //Si no es usuario ni Associado puede ser el admin:
                    fetch("../controllers/AdminController.php", {
                        method: "POST",
                        body: new URLSearchParams({
                            accion: "login",
                            usuario: email,
                            password: password,
                        })
                    })
                        .then(res => res.json())
                        .then(data => {
                            console.log(data)
                            if (data.status === "ok") {
                                Swal.fire({
                                    title: 'Sucesso!',
                                    html: data.message + "<br><br> redirecionando...",
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

                } else {
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
            .catch(err => console.error("Error en la petición:", err));


    });

});
