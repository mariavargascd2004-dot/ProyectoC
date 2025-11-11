document.addEventListener('DOMContentLoaded', function () {

    let addModalFiles = [];
    let editModalFiles = [];
    let productoAgregado = false;

    const modalProdutoEl = document.getElementById('modalProduto');
    const formProduto = document.getElementById("formProduto");
    const modalProdutoFile = document.getElementById('modalProdutoFile');
    const modalProdutoPreview = document.getElementById('modalProdutoPreview');
    const modalProdutoRemove = document.getElementById('modalProdutoRemove');
    const selectCategoria = document.getElementById("selectCategoria");
    const selectSubcategoria = document.getElementById("selectSubcategoria");

    const botonEditarProduto = document.getElementById("btnEditarProducto");
    const modalEditar = new bootstrap.Modal(document.getElementById('modalProdutoEditar'));
    const formEditarProduto = document.getElementById("formEditarProduto");
    const modalEditarIdProducto = document.getElementById("modalEditarIdProducto");
    const modalEditarTitulo = document.getElementById("modalEditarTitulo");
    const modalEditarCategoria = document.getElementById("modalEditarCategoria");
    const modalEditarSubcategoria = document.getElementById("modalEditarSubcategoria");
    const modalEditarDescripcion = document.getElementById("modalEditarDescripcion");
    const modalEditarTamanho = document.getElementById("modalEditarTamanho");
    const modalEditarColor = document.getElementById("modalEditarColor");
    const modalEditarPrecio = document.getElementById("modalEditarPrecio");
    const modalEditarImagenesExistentes = document.getElementById("modalEditarImagenesExistentes");
    const modalEditarProdutoFile = document.getElementById('modalEditarProdutoFile');
    const modalEditarProdutoPreview = document.getElementById('modalEditarProdutoPreview');
    const modalEditarProdutoRemove = document.getElementById('modalEditarProdutoRemove');


    if (modalProdutoFile) {
        modalProdutoFile.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files);
            addModalFiles = addModalFiles.concat(newFiles);
            mostrarPreviewNuevasImagenes(addModalFiles, modalProdutoPreview);
            e.target.value = null;
        });
    }

    if (modalProdutoRemove) {
        modalProdutoRemove.addEventListener('click', () => {
            addModalFiles = [];
            mostrarPreviewNuevasImagenes(addModalFiles, modalProdutoPreview);
        });
    }

    if (formProduto) {
        formProduto.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            formData.delete('images[]');
            for (let i = 0; i < addModalFiles.length; i++) {
                formData.append('images[]', addModalFiles[i]);
            }

            fetch("../controllers/ProdutoController.php", {
                method: "POST",
                body: formData,
            })
                .then(response => {
                    if (!response.ok) throw new Error('Erro na rede: ' + response.statusText);
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'ok') {
                        Swal.fire({
                            toast: true,
                            position: 'top-start',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });

                        formProduto.reset();

                        addModalFiles = [];
                        mostrarPreviewNuevasImagenes(addModalFiles, modalProdutoPreview);

                        selectSubcategoria.innerHTML = '<option>Sub-Categoria</option>';

                        productoAgregado = true;

                    } else {
                        Swal.fire({
                            title: "Erro!",
                            text: data.message || "Ocorreu um problema.",
                            icon: "error"
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire("Erro!", "Não foi possível conectar ao servidor.", "error");
                });
        });
    }

    if (modalProdutoEl) {
        modalProdutoEl.addEventListener('hidden.bs.modal', function (event) {
            if (productoAgregado) {
                window.location.reload();
            }
        });
    }

    if (selectCategoria) {
        selectCategoria.addEventListener("change", function () {
            cargarSubcategorias(selectCategoria, selectSubcategoria);
        });
    }

    if (botonEditarProduto) {
        botonEditarProduto.addEventListener("click", function () {
            seleccionarProductoParaEditar();
        });
    }

    if (formEditarProduto) {
        formEditarProduto.addEventListener("submit", function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            formData.delete('images[]');
            for (let i = 0; i < editModalFiles.length; i++) {
                formData.append('images[]', editModalFiles[i]);
            }

            const inputsEliminar = formEditarProduto.querySelectorAll('input[name="imagenesAEliminar[]"]');
            const idsUnicos = new Set();
            inputsEliminar.forEach(input => {
                if (idsUnicos.has(input.value)) {
                    input.remove();
                } else {
                    idsUnicos.add(input.value);
                }
            });

            fetch("../controllers/ProdutoController.php", {
                method: "POST",
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'ok') {

                        modalEditar.hide();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 3500,
                            timerProgressBar: true
                        });

                        actualizarProductoEnDOM(data.producto);

                        actualizarProductoEnListaJS(data.producto);

                        function actualizarProductoEnListaJS(producto) {
                            if (typeof listaDeProductos === 'undefined' || !producto) return;

                            const index = listaDeProductos.findIndex(p => p.idProducto == producto.idProducto);

                            if (index !== -1) {
                                listaDeProductos[index].titulo = producto.titulo;
                                listaDeProductos[index].precio = producto.precio;
                                listaDeProductos[index].imagen_principal = producto.imagen_principal;
                            }
                        }

                    } else {
                        Swal.fire("Erro!", data.message || "Ocorreu um problema.", "error");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire("Erro!", "Não foi possível conectar ao servidor.", "error");
                });
        });
    }

    if (modalEditarProdutoFile) {
        modalEditarProdutoFile.addEventListener('change', (e) => {
            const newFiles = Array.from(e.target.files);
            editModalFiles = editModalFiles.concat(newFiles);
            mostrarPreviewNuevasImagenes(editModalFiles, modalEditarProdutoPreview);
            e.target.value = null;
        });
    }

    if (modalEditarProdutoRemove) {
        modalEditarProdutoRemove.addEventListener('click', () => {
            editModalFiles = [];
            mostrarPreviewNuevasImagenes(editModalFiles, modalEditarProdutoPreview);
        });
    }

    if (modalEditarCategoria) {
        modalEditarCategoria.addEventListener("change", function () {
            cargarSubcategorias(modalEditarCategoria, modalEditarSubcategoria);
        });
    }


    function mostrarPreviewNuevasImagenes(fileArray, previewContainer) {
        previewContainer.innerHTML = '';

        previewContainer.classList.remove('text-muted', 'justify-content-center', 'align-items-center');
        previewContainer.classList.add('d-flex', 'flex-wrap');
        previewContainer.style.gap = '10px';
        previewContainer.style.minHeight = '100px';
        previewContainer.style.maxHeight = '280px';
        previewContainer.style.overflowY = 'auto';


        if (fileArray.length === 0) {
            previewContainer.innerHTML = 'Nenhuma imagem selecionada';
            previewContainer.classList.add('text-muted', 'justify-content-center', 'align-items-center');
            previewContainer.style.minHeight = '150px';
            return;
        }

        fileArray.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const imgWrapper = document.createElement('div');
                imgWrapper.className = 'position-relative';
                imgWrapper.style.margin = '5px';

                const reader = new FileReader();
                reader.onload = (event) => {
                    const img = document.createElement('img');
                    img.src = event.target.result;
                    img.style.cssText = 'height: 100px; width: auto; border-radius: 5px; border: 1px solid #ddd;';

                    const deleteBtn = document.createElement('button');
                    deleteBtn.type = 'button';
                    deleteBtn.innerHTML = '&times;';
                    deleteBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                    deleteBtn.style.cssText = 'line-height: 1; padding: 2px 5px; font-size: 14px; transform: translate(50%, -50%); border-radius: 50%;';

                    deleteBtn.onclick = () => {
                        fileArray.splice(index, 1);
                        mostrarPreviewNuevasImagenes(fileArray, previewContainer);
                    };

                    imgWrapper.appendChild(img);
                    imgWrapper.appendChild(deleteBtn);
                    previewContainer.appendChild(imgWrapper);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function cargarSubcategorias(selectCategoriaEl, selectSubcategoriaEl, idSubcategoriaSeleccionada = null) {
        var idCategoria = selectCategoriaEl.value;
        selectSubcategoriaEl.innerHTML = '<option value="">Carregando...</option>';

        if (idCategoria == "") {
            selectSubcategoriaEl.innerHTML = '<option value="">Selecione uma subcategoria:</option>';
            return;
        }

        fetch("../controllers/SubcategoriaController.php", {
            method: "POST",
            body: new URLSearchParams({
                accion: "subcategoriasConIdCategoria",
                idCategoria: idCategoria,
            })
        })
            .then(res => res.json())
            .then(data => {
                selectSubcategoriaEl.innerHTML = '<option value="">Selecione uma subcategoria:</option>';
                if (data.status === "ok") {
                    data.subcategorias.forEach(sub => {
                        const option = document.createElement("option");
                        option.value = sub.idSubcategoria;
                        option.textContent = sub.nombre;
                        selectSubcategoriaEl.appendChild(option);
                    });
                } else {
                    const option = document.createElement("option");
                    option.value = "";
                    option.textContent = "Sem subcategorias";
                    selectSubcategoriaEl.appendChild(option);
                }

                if (idSubcategoriaSeleccionada) {
                    selectSubcategoriaEl.value = idSubcategoriaSeleccionada;
                }
            })
            .catch(error => {
                console.error("Error al cargar subcategorías:", error);
                selectSubcategoriaEl.innerHTML = '<option value="">Erro ao carregar</option>';
            });
    }


    function seleccionarProductoParaEditar() { 
        if (typeof listaDeProductos === 'undefined' || listaDeProductos.length === 0) {
            Swal.fire("Erro!", "Não há produtos para editar.", "error");
            return;
        }

        const htmlContent = `
            <style>
                #swal-product-list {
                    max-height: 300px;
                    overflow-y: auto;
                    margin-top: 15px;
                    border-radius: 5px;
                    border: 1px solid #eee;
                }
                .swal-product-item {
                    display: flex;
                    align-items: center;
                    padding: 10px 12px;
                    cursor: pointer;
                    border-bottom: 1px solid #eee;
                }
                .swal-product-item:last-child {
                    border-bottom: none;
                }
                .swal-product-item:hover {
                    background-color: #f9f9f9;
                }
                .swal-product-item img {
                    width: 40px;
                    height: 40px;
                    object-fit: cover;
                    border-radius: 5px;
                    margin-right: 10px;
                }
                .swal-product-item-info {
                    display: flex;
                    flex-direction: column;
                }
                .swal-product-item-title {
                    font-weight: 600;
                    color: #333;
                }
                .swal-product-item-price {
                    font-size: 0.9em;
                    color: #555;
                }
            </style>
            <input type="text" id="swal-search-input" class="swal2-input" placeholder="Buscar produto pelo nome...">
            <div id="swal-product-list"></div>
        `;

        Swal.fire({ 
            title: 'Selecciona um produto para editar',
            html: htmlContent,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: 'Cancelar',
            width: '600px',

            didOpen: () => {
                const searchInput = document.getElementById('swal-search-input');
                const listContainer = document.getElementById('swal-product-list');

                const renderList = (filter = '') => {
                    filter = filter.toLowerCase();
                    const filteredProducts = listaDeProductos.filter(p =>
                        p.titulo.toLowerCase().includes(filter)
                    );

                    if (filteredProducts.length === 0) {
                        listContainer.innerHTML = '<div class="swal-product-item"><span class="text-muted">Nenhum produto encontrado.</span></div>';
                        return;
                    }

                    listContainer.innerHTML = filteredProducts.map(p => `
                        <div class="swal-product-item" data-id="${p.idProducto}">
                            <img src="${p.imagen_principal || '../assets/img/produtos/default.png'}" alt="${p.titulo}">
                            <div class="swal-product-item-info">
                                <span class="swal-product-item-title">${p.titulo}</span>
                                <span class="swal-product-item-price">R$ ${p.precio}</span>
                            </div>
                        </div>
                    `).join('');
                };

                renderList('');
                searchInput.addEventListener('keyup', () => renderList(searchInput.value));

                listContainer.addEventListener('click', (e) => {
                    const item = e.target.closest('.swal-product-item');
                    if (item && item.dataset.id) {
                        const id = item.dataset.id;

                        cargarDatosDelProducto(id);

                        Swal.close();
                    }
                });
            }
        });

    }


    function cargarDatosDelProducto(idProducto) {
        formEditarProduto.reset();
        editModalFiles = [];
        mostrarPreviewNuevasImagenes(editModalFiles, modalEditarProdutoPreview);
        modalEditarImagenesExistentes.innerHTML = '';
        formEditarProduto.querySelectorAll('input[name="imagenesAEliminar[]"]').forEach(input => input.remove());

        modalEditar.show();
        modalEditarTitulo.value = "Carregando dados...";

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
                    const p = data.producto;
                    modalEditarIdProducto.value = p.idProducto;
                    modalEditarTitulo.value = p.titulo;
                    modalEditarDescripcion.value = p.descripcion;
                    modalEditarTamanho.value = p.tamano;
                    modalEditarColor.value = p.color;
                    modalEditarPrecio.value = p.precio;

                    modalEditarCategoria.value = p.producto_idCategoria;
                    cargarSubcategorias(modalEditarCategoria, modalEditarSubcategoria, p.producto_idSubcategoria);

                    if (p.imagenes && p.imagenes.length > 0) {
                        p.imagenes.forEach(img => {
                            const imgContainer = document.createElement('div');
                            imgContainer.className = 'position-relative';
                            imgContainer.style.margin = '5px';

                            const imgEl = document.createElement('img');
                            imgEl.src = img.caminho_imagem;
                            imgEl.style.cssText = 'height: 80px; width: auto; border-radius: 5px; border: 1px solid #ddd;';

                            const deleteBtn = document.createElement('button');
                            deleteBtn.type = 'button';
                            deleteBtn.innerHTML = '&times;';
                            deleteBtn.className = 'btn btn-danger btn-sm position-absolute top-0 end-0';
                            deleteBtn.style.cssText = 'line-height: 1; padding: 2px 5px; font-size: 14px; transform: translate(50%, -50%); border-radius: 50%;';

                            deleteBtn.onclick = () => {
                                Swal.fire({
                                    title: 'Eliminar esta imagen?',
                                    text: "La imagen se eliminará permanentemente al guardar los cambios.",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Sí, eliminar',
                                    cancelButtonText: 'Cancelar',
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        imgContainer.style.display = 'none';
                                        const inputOculto = document.createElement('input');
                                        inputOculto.type = 'hidden';
                                        inputOculto.name = 'imagenesAEliminar[]';
                                        inputOculto.value = img.idImagem;
                                        formEditarProduto.appendChild(inputOculto);
                                    }
                                });
                            };

                            imgContainer.appendChild(imgEl);
                            imgContainer.appendChild(deleteBtn);
                            modalEditarImagenesExistentes.appendChild(imgContainer);
                        });
                    } else {
                        modalEditarImagenesExistentes.innerHTML = '<small class="text-muted">Este producto no tiene imágenes.</small>';
                    }

                } else {
                    modalEditar.hide();
                    Swal.fire("Erro!", data.message || "No se pudieron cargar los datos del producto.", "error");
                }
            })
            .catch(error => {
                console.error("Error al cargar producto:", error);
                modalEditar.hide();
                Swal.fire("Erro!", "Error de conexión al cargar el producto.", "error");
            });
    }

    function actualizarProductoEnDOM(producto) {
        if (!producto || !producto.idProducto) return;

        const card = document.getElementById('product-card-' + producto.idProducto);
        if (!card) return;

        const title = card.querySelector('.product-card-title');
        const price = card.querySelector('.product-card-price');
        const image = card.querySelector('.product-card-image');

        card.style.transition = 'opacity 0.3s ease-out';
        card.style.opacity = '0.5';

        setTimeout(() => {
            if (title) title.textContent = producto.titulo;
            if (price) price.textContent = producto.precio;
            if (image && producto.imagen_principal) {
                image.src = producto.imagen_principal;
            }

            card.style.opacity = '1';
        }, 300);


    }

});