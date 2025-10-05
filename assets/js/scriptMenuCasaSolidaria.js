document.addEventListener('DOMContentLoaded', function () {

    // Animar campana de notificaciones
    const btnFlotante__LojaSolidaria = document.getElementById("btnFlotante--LojaSolidaria");

    setInterval(() => {
        btnFlotante__LojaSolidaria.style.animation = "moverCampana 0.6s ease";
        setTimeout(() => {
            btnFlotante__LojaSolidaria.style.animation = "none";
        }, 600);
    }, 3000);

    /* Modal Aceptar Registros de Emprendedores  */
    const modal__botonMostrarMas = document.querySelector(".modal--botonMostrarMas");
    const modal__NuevoEmprendedor = document.querySelector(".modal--NuevoEmprendedor");
    const modal__contenidoMasInfo = document.querySelector(".modal--contenidoMasInfo");
    modal__botonMostrarMas.addEventListener("click", function () {
        if (modal__contenidoMasInfo.style.display != "block") {
            this.innerHTML = "Mostrar detalhes <i class='fa-solid fa-angle-up'></i>";
            modal__contenidoMasInfo.style.display = "block";
        }
        else {
            this.innerHTML = "Mostrar detalhes <i class='fa-solid fa-angle-down'></i>";
            modal__contenidoMasInfo.style.display = "none";
        }
    })


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



    /* Cambiar el Pooster Inicial - Mostrar más infomración de la historia */
    let portada__botao = document.getElementById("portada__botao");
    let portada__imagem = document.querySelector(".portada__imagem");
    let portada__conteudo_secundario = document.querySelector(".portada__conteudo-secundario");
    let portada__subTitulo = document.querySelector(".portada__subTitulo");
    let portada__parrafo = document.querySelector(".portada__parrafo");

    portada__botao.addEventListener("click", function () {
        //Extender o Reducir Historia
        if (this.style.left == "") {//vetifica si el boton esta del lado izquierdo
            portada__imagem.style.filter = "brightness(0.1)";
            portada__conteudo_secundario.style.width = "95vw";
            portada__subTitulo.style["text-align"] = "center";
            portada__subTitulo.style["font-size"] = "3rem";
            this.style.left = "calc(95% - 150px)";
            this.innerHTML = "Ver menos";
            portada__parrafo.innerHTML = "A Economia Solidária, ou ECOSOL, nasce como alternativa às debilidades impostas pelas economias de mercado. Desde o século XIX, empreendimentos como as cooperativas se destacam por sua atuação coletiva, seja na produção de bens, prestação de serviços, fundos de crédito, comercialização ou consumo solidário. Além das cooperativas, os Empreendimentos Econômicos Solidários (EES) também se apresentam como grupos informais, associações e sociedades mercantis, todos guiados pela cooperação, solidariedade e autogestão.<br> Em abril de 2013, a SESAMPE inaugurou a primeira Casa de Economia Solidária (ECOSOL) em Santana do Livramento, tornando o município pioneiro no Rio Grande do Sul. O espaço foi criado com o objetivo de realizar a comercialização de produtos, formação e certificação dos EES mediante o CADSOL (Cadastro Nacional de EES). <br> Hoje, a Casa de Economia Solidária reúne dez empreendimentos nos setores de artesanato, confecção, alimentação e serviços, atuando como um espaço autogestionado e democrático. Ao longo de sua trajetória, estabeleceu parcerias com instituições como a Prefeitura de Livramento, Unipampa, UERGS, IFSUL e UNISOL RS, promovendo formações, qualificações e atividades comunitárias que fortalecem a Economia Solidária na Fronteira Oeste.";
        }
        else {
            portada__imagem.style.filter = "brightness(1)";
            portada__conteudo_secundario.style.width = "22vw";
            portada__subTitulo.style["font-size"] = "2rem";
            this.style.left = "";
            this.innerHTML = "Ver mais";

            setTimeout(() => {
                portada__parrafo.innerHTML = "Desde 2013, a Casa de Economia Solidária de Santana do Livramento é um espaço autogestionado que reúne empreendimentos locais de artesanato, alimentação, serviços e confecção, promovendo cooperação, formação e desenvolvimento comunitário na Fronteira Oeste.";
            }, 500);
        }

    })

});
