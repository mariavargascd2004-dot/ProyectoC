
/* Vista previa de fotos */
document.querySelectorAll('.foto-input').forEach(input => {
    input.addEventListener('change', function (e) {
        const previewId = this.getAttribute('data-preview');
        const previewImg = document.getElementById(previewId);

        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (event) {
                previewImg.src = event.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(this.files[0]);
        } else {
            previewImg.src = '../assets/img/placeholder.png';
        }
    });
});