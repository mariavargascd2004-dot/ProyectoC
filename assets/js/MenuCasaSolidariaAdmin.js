document.addEventListener('DOMContentLoaded', function () {

    // Animar campana de notificaciones
    const btnFlotante__LojaSolidaria = document.getElementById("btnFlotante--aprovacaoEmprendimentos");

    setInterval(() => {
        btnFlotante__LojaSolidaria.style.animation = "moverCampana 0.6s ease";
        setTimeout(() => {
            btnFlotante__LojaSolidaria.style.animation = "none";
        }, 600);
    }, 3000);

    /* Modal Aceptar Registros de Emprendedores  */
    const botonesMostrarMas = document.querySelectorAll(".modal--botonMostrarMas");
    const contenidosMasInfo = document.querySelectorAll(".modal--contenidoMasInfo");

    // Iteramos sobre cada botón
    botonesMostrarMas.forEach((boton, index) => {
        boton.addEventListener("click", function () {
            const contenido = contenidosMasInfo[index]; // contenido correspondiente al botón

            if (contenido.style.display !== "block") {
                boton.innerHTML = "Mostrar detalhes <i class='fa-solid fa-angle-up'></i>";
                contenido.style.display = "block";
            } else {
                boton.innerHTML = "Mostrar detalhes <i class='fa-solid fa-angle-down'></i>";
                contenido.style.display = "none";
            }
        });
    });


    const todosLosBotonesAceptar = document.querySelectorAll(".modal__botonAceptar");
    const modal__botonRechazar = document.querySelectorAll(".modal__botonRechazar");

    // Botão Aceitar
    todosLosBotonesAceptar.forEach(boton => {
        boton.addEventListener("click", () => {
            const id = boton.dataset.id;
            fetch("../controllers/EmprendimentoController.php", {
                method: 'POST',
                body: new URLSearchParams({
                    accion: "aprovar",
                    id: id,
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status == "ok") {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonColor: '#B2442E',
                            timer: 3000,
                            timerProgressBar: true
                        });

                        //Remover del DOM
                        const modal__NuevoEmprendedor = document.querySelectorAll(".modal--NuevoEmprendedor");
                        if (modal__NuevoEmprendedor.length > 1) {
                            document.getElementById("empr_" + id).remove();
                        }
                        else {
                            document.getElementById("empr_" + id).remove();
                            document.getElementById("modalAdminBody").innerHTML = "<p>Nenhum empreendimento pendente de aprovação.</p>";
                        }

                        //Cambiar el valor del contador
                        var cantidad = parseInt(document.getElementById("count_emprendimentos_pendentes").getHTML());
                        cantidad--;
                        document.getElementById("count_emprendimentos_pendentes").innerHTML = cantidad;


                    } else {
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
        });
    });
    // Botão Rejeitar
    modal__botonRechazar.forEach(boton => {

        boton.addEventListener("click", () => {
            const id = boton.dataset.id;
            const nome = boton.dataset.nome;
            const idAdmin = boton.dataset.idadmin;

            const fraseConfirmacao = `excluir ${nome}`;

            Swal.fire({
                title: `Tem certeza que deseja excluir ${nome}?`,
                html: `Para confirmar a exclusão do registro de <b>${nome}</b>, digite a seguinte frase no campo abaixo: 
               <br>
               <br>
               <strong>${fraseConfirmacao}</strong>`,
               target: '#modalAprovacaoEmprendedores',
                icon: 'warning',
                input: 'text',
                inputPlaceholder: 'Digite aqui a frase de confirmação',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, Excluir',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {

                    if (result.value.toLowerCase() === fraseConfirmacao.toLowerCase()) {
                        fetch("../controllers/UsuarioController.php", {
                            method: 'POST',
                            body: new URLSearchParams({
                                accion: "eliminar",
                                id: idAdmin,
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status == "ok") {
                                    // Mostrar mensaje de éxito
                                    Swal.fire({
                                        title: '¡Éxito!',
                                        text: data.message,
                                        icon: 'success',
                                        confirmButtonColor: '#B2442E',
                                        timer: 3000,
                                        timerProgressBar: true
                                    });

                                    //Remover del DOM
                                    const modal__NuevoEmprendedor = document.querySelectorAll(".modal--NuevoEmprendedor");
                                    if (modal__NuevoEmprendedor.length > 1) {
                                        document.getElementById("empr_" + id).remove();
                                    }
                                    else {
                                        document.getElementById("empr_" + id).remove();
                                        document.getElementById("modalAdminBody").innerHTML = "<p>Nenhum empreendimento pendente de aprovação.</p>";
                                    }

                                    //Cambiar el valor del contador
                                    var cantidad = parseInt(document.getElementById("count_emprendimentos_pendentes").getHTML());
                                    cantidad--;
                                    document.getElementById("count_emprendimentos_pendentes").innerHTML = cantidad;


                                } else {
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

                    } else {
                        Swal.fire(
                            'Erro de Confirmação',
                            'A frase digitada não corresponde. A exclusão foi cancelada.',
                            'error'
                        );
                    }
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    swal.close();
                }
            });

        });
    });

});
