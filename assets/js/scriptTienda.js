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


});