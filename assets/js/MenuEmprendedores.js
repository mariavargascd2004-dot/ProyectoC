document.addEventListener('DOMContentLoaded', function () {

    /* Cambiar el Pooster Inicial - Mostrar más infomración de la historia */
    let portada__botao = document.getElementById("portada__botao");
    let portada__imagem = document.querySelector(".portada__imagem");
    let portada__conteudo_secundario = document.querySelector(".portada__conteudo-secundario");
    let portada__subTitulo = document.querySelector(".portada__subTitulo");
    let portada__parrafo = document.querySelector(".portada__parrafo");
    let portada__conteudo_terceario = document.querySelector(".portada__conteudo-terceario");

    portada__botao.addEventListener("click", function () {
        //Extender o Reducir Historia
        if (this.style.left == "") {//vetifica si el boton esta del lado izquierdo
            portada__imagem.style.filter = "brightness(0.1)";
            portada__conteudo_secundario.style.width = "95vw";
            portada__subTitulo.style["text-align"] = "center";
            portada__subTitulo.style["font-size"] = "3rem";
            this.style.left = "calc(95% - 150px)";
            this.innerHTML = "Ver menos";
            portada__conteudo_terceario.style["z-index"] = "1";
            portada__parrafo.innerHTML = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim, sit ipsum, voluptas assumenda, vero nisi ea nobis placeat quaerat doloribus rerum molestiae voluptatem ullam rem molestias. Veniam cumque nostrum facere.<br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim, sit ipsum, voluptas assumenda, vero nisi ea nobis placeat quaerat doloribus rerum molestiae voluptatem ullam rem molestias. Veniam cumque nostrum facere.<br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim, sit ipsum, voluptas assumenda, vero nisi ea nobis placeat quaerat doloribus rerum molestiae voluptatem ullam rem molestias. Veniam cumque nostrum facere.";
        }
        else {
            portada__imagem.style.filter = "brightness(1)";
            portada__conteudo_secundario.style.width = "22vw";
            portada__subTitulo.style["font-size"] = "2rem";
            this.style.left = "";
            this.innerHTML = "Ver mais";
            portada__conteudo_terceario.style["z-index"] = "2";
            
            setTimeout(() => {
                portada__parrafo.innerHTML = "Lorem ipsum dolor sit amet consectetur adipisicing elit. Enim, sit ipsum, voluptas assumenda, vero nisi ea nobis placeat quaerat doloribus rerum molestiae voluptatem ullam rem molestias. Veniam cumque nostrum facere.";
            }, 500);
        }

    })

});
