    document.addEventListener('DOMContentLoaded', function () {

 	//Variables Globales

    let portada__botao = document.getElementById("portada__botao");
    let portada__parrafo = document.querySelector(".hero-business__history-text");

    const navBar = document.getElementById("navBar");
    const modalDetalheProduto = document.getElementById('modalDetalheProduto');
    const filtroLinks = document.querySelectorAll('#filtroCategorias a[href^="#"]');
    const searchInput = document.getElementById('filtroBuscaInput');

   
    /////////////////////////////////////////////////
    ///                    INICIO                  //
    /////////////////////////////////////////////////

    if (portada__botao && portada__parrafo) {
        portada__botao.addEventListener("click", function () {
            const isTruncated = portada__parrafo.classList.contains('parrafo-truncado');
            
            if (isTruncated) {
                this.innerHTML = "Ver menos";
                portada__parrafo.classList.remove('parrafo-truncado');
                portada__parrafo.classList.add('parrafo-expandido');
            } else {
                this.innerHTML = "Ver mais";
                portada__parrafo.classList.add('parrafo-truncado');
                portada__parrafo.classList.remove('parrafo-expandido');
                
                // Opcional: Scroll suave hacia arriba si el texto era muy largo
                window.scrollTo({
                    top: document.querySelector('.hero-business').offsetTop - 20,
                    behavior: 'smooth'
                });
            }
        });
    }

   

    if (navBar) {
        function checkScroll() {
            if (window.scrollY > 50) {
                navBar.classList.add("navbar--scrolled");
            } else {
                navBar.classList.remove("navbar--scrolled");
            }
        }
        window.addEventListener("scroll", checkScroll);
        checkScroll();
    }

    if (modalDetalheProduto) {
        modalDetalheProduto.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const idProducto = button.getAttribute('data-product-id');

            resetModalDetalhe();
            carregarDetalhesProduto(idProducto);
        });
    }

    filtroLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            let targetId = this.getAttribute('href');
            let targetElement = document.querySelector(targetId);

            if (targetElement) {
                let headerOffset = document.getElementById('filtroContainer').offsetHeight + 20;
                let elementPosition = targetElement.getBoundingClientRect().top;
                let offsetPosition = elementPosition + window.scrollY - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: "smooth"
                });
            }
        });
    });

    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchTerm = this.value.toLowerCase().trim();
            filtrarProductos(searchTerm);
        });
    }

    // Funcionalidad de Colapsables (Acordeón)
    const collapsibles = document.querySelectorAll('.collapsible-header');
    collapsibles.forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const target = document.querySelector(targetId);
            const icon = this.querySelector('.toggle-icon');
            
            if (target.classList.contains('show')) {
                target.classList.remove('show');
                icon.classList.replace('fa-chevron-up', 'fa-chevron-down');
                setTimeout(() => { target.style.display = 'none'; }, 10);
            } else {
                target.style.display = 'block';
                setTimeout(() => { 
                    target.classList.add('show');
                    icon.classList.replace('fa-chevron-down', 'fa-chevron-up');
                }, 10);
            }
        });
    });

});

function filtrarProductos(searchTerm) {
    const productItems = document.querySelectorAll('.product-item-col');
    const categorySections = document.querySelectorAll('.category-section');
    const globalNoResults = document.getElementById('globalNoResults');
    let totalProductosVisibles = 0;

    productItems.forEach(item => {
        const searchTerms = item.dataset.searchTerms.toLowerCase();
        
        const matchesSearch = searchTerms.includes(searchTerm);

        if (matchesSearch) {
            item.style.display = 'block';
            totalProductosVisibles++;
        } else {
            item.style.display = 'none';
        }
    });
    categorySections.forEach(catSection => {
        let productosVisiblesEnCategoria = 0;
        const subcategorySections = catSection.querySelectorAll('.subcategory-section');

        subcategorySections.forEach(subSection => {
            const grid = subSection.nextElementSibling; 
            const visibleProductsInGrid = grid.querySelectorAll('.product-item-col[style*="display: block"]');

            if (visibleProductsInGrid.length > 0) {
                subSection.style.display = 'block'; 
                productosVisiblesEnCategoria += visibleProductsInGrid.length;
            } else {
                subSection.style.display = 'none'; 
            }
        });

        const categoryTitle = catSection.querySelector('.category-title-wrapper');
        const noResultsMessage = catSection.querySelector('.no-results-message');

        if (productosVisiblesEnCategoria > 0) {
            categoryTitle.style.display = 'block'; 
            if (noResultsMessage) noResultsMessage.style.display = 'none';
        } else {
            categoryTitle.style.display = 'none';
            if (searchTerm.length > 0 && noResultsMessage) {
                noResultsMessage.style.display = 'block';
            }
        }
    });

    if (totalProductosVisibles === 0 && searchTerm.length > 0) {
        globalNoResults.style.display = 'block';
    } else {
        globalNoResults.style.display = 'none';
    }
}

function carregarDetalhesProduto(idProducto) {

    const imgPrincipal = document.getElementById('modalDetalheImagemPrincipal');
    const thumbnailsContainer = document.getElementById('modalDetalheThumbnails');

    fetch("../controllers/ProdutoController.php", {
        method: "POST",
        body: new URLSearchParams({
            accion: "obtenerProducto",
            idProducto: idProducto,
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'ok') {
                preencherModalDetalhe(data.producto);
            } else {
                document.getElementById('modalDetalheTitulo').textContent = 'Erro ao carregar';
                document.getElementById('modalDetalheDescricao').textContent = data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('modalDetalheTitulo').textContent = 'Erro de conexão';
        });
}

function preencherModalDetalhe(producto) {
    const imgPrincipal = document.getElementById('modalDetalheImagemPrincipal');
    const thumbnailsContainer = document.getElementById('modalDetalheThumbnails');

    document.getElementById('modalDetalheTitulo').textContent = producto.titulo;
    document.getElementById('modalDetalhePreco').textContent = "R$ " + parseFloat(producto.precio).toFixed(2);
    document.getElementById('modalDetalheDescricao').textContent = producto.descripcion;
    document.getElementById('modalDetalheCor').textContent = producto.color || 'N/A';
    document.getElementById('modalDetalheTamanho').textContent = producto.tamano || 'N/A';

    thumbnailsContainer.innerHTML = '';

    if (producto.imagenes && producto.imagenes.length > 0) {
        imgPrincipal.src = producto.imagenes[0].caminho_imagem;

        producto.imagenes.forEach((img, index) => {
            const thumb = document.createElement('img');
            thumb.src = img.caminho_imagem;
            thumb.className = 'modal-detalhe-thumbnail';
            if (index === 0) {
                thumb.classList.add('active');
            }

            thumb.addEventListener('click', function () {
                imgPrincipal.src = img.caminho_imagem;
                thumbnailsContainer.querySelectorAll('.modal-detalhe-thumbnail').forEach(t => t.classList.remove('active'));
                thumb.classList.add('active');
            });

            thumbnailsContainer.appendChild(thumb);
        });
    } else {
        imgPrincipal.src = '../assets/img/CasaSolidaria/defaultProduct.png';
    }

    const btnWhatsapp = document.getElementById('modalDetalheBtnWhatsapp');
    btnWhatsapp.href = gerarLinkWhatsApp(producto);
}

function resetModalDetalhe() {
    document.getElementById('modalDetalheTitulo').textContent = 'Carregando...';
    document.getElementById('modalDetalhePreco').textContent = 'R$ ...';
    document.getElementById('modalDetalheDescricao').textContent = '...';
    document.getElementById('modalDetalheCor').textContent = '...';
    document.getElementById('modalDetalheTamanho').textContent = '...';
    document.getElementById('modalDetalheImagemPrincipal').src = '';
    document.getElementById('modalDetalheThumbnails').innerHTML = '<div class="spinner-border text-primary" role="status"></div>';
    document.getElementById('modalDetalheBtnWhatsapp').href = '#';
}

function gerarLinkWhatsApp(producto) {
    const telefone = (whatsappEmprendedor || '').replace(/[^0-9]/g, '');
    const nomeProduto = producto.titulo;

    let msg = `Olá! Tenho interesse no produto: *${nomeProduto}* (Preço: R$ ${producto.precio}).`;

    if (nomeUsuarioLogado) {
        msg += `\nMeu nome é ${nomeUsuarioLogado}.`;
    }

    const link = `https://wa.me/55${telefone}?text=${encodeURIComponent(msg)}`;

    return link;
}

function filtrarProductos(searchTerm) {
    const productItems = document.querySelectorAll('.product-item-col');
    const categorySections = document.querySelectorAll('.category-section');
    const globalNoResults = document.getElementById('globalNoResults');
    let totalProductosVisibles = 0;

    productItems.forEach(item => {
        const searchTerms = item.dataset.searchTerms.toLowerCase();
        if (searchTerms.includes(searchTerm)) {
            item.style.display = 'block';
            totalProductosVisibles++;
        } else {
            item.style.display = 'none';
        }
    });

    categorySections.forEach(catSection => {
        let productosVisiblesEnCategoria = 0;
        const subcategorySections = catSection.querySelectorAll('.subcategory-section');

        subcategorySections.forEach(subSection => {
            const grid = subSection.nextElementSibling; 
            const visibleProductsInGrid = grid.querySelectorAll('.product-item-col[style*="display: block"]');

            if (visibleProductsInGrid.length > 0) {
                subSection.style.display = 'block'; 
                productosVisiblesEnCategoria += visibleProductsInGrid.length;
            } else {
                subSection.style.display = 'none'; 
            }
        });

        const categoryTitle = catSection.querySelector('.category-title-wrapper');
        const noResultsMessage = catSection.querySelector('.no-results-message');

        if (productosVisiblesEnCategoria > 0) {
            categoryTitle.style.display = 'block'; 
            if (noResultsMessage) noResultsMessage.style.display = 'none';
        } else {
            categoryTitle.style.display = 'none';
            if (searchTerm.length > 0 && noResultsMessage) {
                noResultsMessage.style.display = 'block';
            }
        }
    });

    if (totalProductosVisibles === 0 && searchTerm.length > 0) {
        globalNoResults.style.display = 'block';
    } else {
        globalNoResults.style.display = 'none';
    }
}