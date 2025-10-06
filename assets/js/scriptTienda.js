document.addEventListener('DOMContentLoaded', function () {

    /* Rango de precios */
    const minSlider = document.getElementById('minPrice');
    const maxSlider = document.getElementById('maxPrice');
    const minValue = document.getElementById('minValue');
    const maxValue = document.getElementById('maxValue');
    const btnResetear = document.getElementById('price__botao');

    minSlider.addEventListener('input', () => {
        if (parseInt(minSlider.value) > parseInt(maxSlider.value)) {
            minSlider.value = maxSlider.value;
        }
        minValue.textContent = minSlider.value;
    });

    maxSlider.addEventListener('input', () => {
        if (parseInt(maxSlider.value) < parseInt(minSlider.value)) {
            maxSlider.value = minSlider.value;
        }
        maxValue.textContent = maxSlider.value;
    });

    btnResetear.addEventListener('click', () => {
        minSlider.value = 100;
        minValue.textContent = 100;
        maxSlider.value = 400;
        maxValue.textContent = 400;
    })

    /* Vista previa de imagenes del agregar producto  */
    const modalProdutoFile = document.getElementById('modalProdutoFile');
    const modalProdutoPreview = document.getElementById('modalProdutoPreview');
    const modalProdutoRemove = document.getElementById('modalProdutoRemove');

    modalProdutoFile.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                modalProdutoPreview.innerHTML = `<img src="${event.target.result}" class="img-fluid" style="max-height: 280px;">`;
            };
            reader.readAsDataURL(file);
        }
    });

    modalProdutoRemove.addEventListener('click', () => {
        modalProdutoFile.value = '';
        modalProdutoPreview.innerHTML = 'Nenhuma imagem selecionada';
    });


    /* Editar Produto */
    const botonEditarProduto = document.getElementById("btnEditarProducto");
    botonEditarProduto.addEventListener("click", function () {
        seleccionarProducto();
    })

    const productos = [
        "Pate de Pimienta",
        "Pate...",
    ];

    async function seleccionarProducto() {
        const { value: producto } = await Swal.fire({
            title: 'Selecciona un producto',
            input: 'text',
            inputPlaceholder: 'Escribe o selecciona un producto',
            showCancelButton: true,
            confirmButtonText: 'Editar',
            cancelButtonText: 'Cancelar',
            inputAttributes: {
                autocapitalize: 'off',
                autocomplete: 'off'
            },
            didOpen: () => {
                const input = Swal.getInput();
                // Creamos un datalist para sugerencias
                const dataList = document.createElement('datalist');
                dataList.id = 'productos-list';
                productos.forEach(p => {
                    const option = document.createElement('option');
                    option.value = p;
                    dataList.appendChild(option);
                });
                document.body.appendChild(dataList);
                input.setAttribute('list', 'productos-list');
            },
            preConfirm: (value) => {
                if (!value || !productos.includes(value)) {
                    Swal.showValidationMessage('Debes seleccionar un producto válido');
                }
                return value;
            }
        });

        if (producto) {
            var myModalEl = document.getElementById('modalProdutoEditar');
            var modal = new bootstrap.Modal(myModalEl);
            modal.show();
        }
    }

});