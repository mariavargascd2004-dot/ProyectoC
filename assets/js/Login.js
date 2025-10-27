document.addEventListener("DOMContentLoaded", () => {
    
    //Variables Globales
    const formLogin__usuario = document.getElementById("formLogin--usuario");

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
            .catch(err => {
                console.error("Error en la petición:", err);
                Swal.fire({
                    title: '¡Error de Conexión!',
                    text: err.message || 'Ocurrió un error de red o el servidor no devolvió una respuesta válida.',
                    icon: 'error'
                });
            });

    });

});
