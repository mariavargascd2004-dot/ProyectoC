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

        loginUsuario(email, password);

    })

});
