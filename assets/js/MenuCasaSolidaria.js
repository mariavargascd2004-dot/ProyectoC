document.addEventListener('DOMContentLoaded', function () {

    //Variables Globales

    let portada__botao = document.getElementById("portada__botao");
    let portada__imagem = document.querySelector(".portada__imagem");
    let portada__conteudo_secundario = document.querySelector(".portada__conteudo-secundario");
    let portada__subTitulo = document.querySelector(".portada__subTitulo");
    let portada__parrafo = document.querySelector(".portada__parrafo");



    /////////////////////////////////////////////////
    ///                    INICIO                  //
    /////////////////////////////////////////////////

    // Cambiar el Pooster Inicial - Mostrar más infomración de la historia
    portada__botao.addEventListener("click", function () {
        //Extender o Reducir Historia
        if (this.style.left == "") {
            portada__imagem.style.filter = "brightness(0.1)";
            portada__conteudo_secundario.style.width = "95vw";
            portada__subTitulo.style["text-align"] = "center";
            portada__subTitulo.style["font-size"] = "3rem";
            this.style.left = "calc(95% - 150px)";
            this.innerHTML = "Ver menos";
            portada__parrafo.classList.remove('parrafo-truncado');
        }
        else {
            portada__imagem.style.filter = "brightness(1)";
            portada__conteudo_secundario.style.width = "22vw";
            portada__subTitulo.style["font-size"] = "2rem";
            this.style.left = "";
            this.innerHTML = "Ver mais";

            setTimeout(() => {
                portada__parrafo.classList.add('parrafo-truncado');
            }, 500);
        }

    })



});
