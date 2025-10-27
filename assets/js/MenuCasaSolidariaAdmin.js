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
    const modal__NuevoEmprendedor = document.querySelector(".modal--NuevoEmprendedor");
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


    const modal__botonRechazar = document.querySelector(".modal__botonRechazar");
    const modal__botonAceptar = document.querySelector(".modal__botonAceptar");
    // Botão Aceitar
    modal__botonAceptar.addEventListener("click", () => {
        modal__NuevoEmprendedor.remove();
        Swal.fire({
            icon: 'success',
            title: 'Registro Aceito',
            text: 'O novo empreendedor foi aceito.',
            confirmButtonColor: '#DCA700',
            confirmButtonText: 'Ok'
        });
    });
    // Botão Rejeitar
    modal__botonRechazar.addEventListener("click", () => {
        Swal.fire({
            icon: 'warning',
            title: 'Tem certeza?',
            text: 'O registro do empreendedor será rejeitado.',
            showCancelButton: true,
            confirmButtonColor: '#B2442E',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, rejeitar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                modal__NuevoEmprendedor.remove();
                Swal.fire({
                    icon: 'success',
                    title: 'Registro Rejeitado',
                    text: 'O registro do empreendedor foi rejeitado.',
                    confirmButtonColor: '#DCA700',
                    confirmButtonText: 'Ok'
                });
            }
        });
    });
});
