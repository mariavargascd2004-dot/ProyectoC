// ================== ELEMENTOS ==================
const acaoSelect = document.getElementById('acao');
const evento__Dados = document.querySelector('.evento--Dados');
const evento__btnFinalizar = document.querySelector('.evento--btnFinalizar');
const eventoSection = document.getElementById('evento').parentElement;
const infoSection = document.querySelectorAll('section.contornoGris')[1]; // sección de info
const imagensBtns = document.querySelectorAll('.img-mini button');
const apagarTodasImgsBtn = document.querySelector('.btn--vermelho');

// ================== VISIBILIDAD ==================
function atualizarVisibilidade() {
    const acao = acaoSelect.value;

    evento__btnFinalizar.classList.remove("fundoAmarelo", "fundoVermelho", "text-dark", "text-white");

    if (acao === 'novo') {          // Criar Novo
        eventoSection.style.display = 'none';
        infoSection.style.display = 'block';
        evento__Dados.style.display = 'block';
        evento__btnFinalizar.classList.add("fundoAmarelo", "text-dark");
        evento__btnFinalizar.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Criar Evento';
    } else if (acao === 'editar') { // Editar
        eventoSection.style.display = 'block';
        infoSection.style.display = 'block';
        evento__Dados.style.display = 'block';
        evento__btnFinalizar.classList.add("fundoAmarelo", "text-dark");
        evento__btnFinalizar.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Atualizar Evento';
    } else if (acao === 'excluir') { // Eliminar
        eventoSection.style.display = 'block';
        evento__Dados.style.display = 'none';
        evento__btnFinalizar.classList.add("fundoVermelho", "text-white");
        evento__btnFinalizar.innerHTML = '<i class="fa-solid fa-trash"></i> Eliminar Evento';
    } else { // Default
        eventoSection.style.display = 'block';
        infoSection.style.display = 'block';
    }
}

acaoSelect.addEventListener('change', atualizarVisibilidade);
window.addEventListener('DOMContentLoaded', atualizarVisibilidade);

// ================== EVENTOS ==================
// Botón de Crear / Editar / Eliminar evento
evento__btnFinalizar.addEventListener('click', () => {
    const acao = acaoSelect.value;
    const nomeEvento = document.querySelector('.evento--Dados input[type="text"]').value || "Evento";

    if (acao === 'novo') {
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: `O evento "${nomeEvento}" foi criado.`,
            confirmButtonColor: '#3085d6',
        });
    } else if (acao === 'editar') {
        Swal.fire({
            icon: 'info',
            title: 'Atualização',
            text: `O evento "${nomeEvento}" foi atualizado com sucesso.`,
            confirmButtonColor: '#3085d6',
        });
    } else if (acao === 'excluir') {
        Swal.fire({
            title: 'Tem certeza?',
            text: `Você está prestes a excluir "${nomeEvento}".`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'success',
                    title: 'Excluído!',
                    text: `O evento "${nomeEvento}" foi eliminado.`,
                    confirmButtonColor: '#3085d6',
                });
            }
        });
    }
});

// ================== IMÁGENES ==================
// Eliminar imagen individual
imagensBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
        Swal.fire({
            title: 'Tem certeza?',
            text: 'Você está prestes a excluir esta imagem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Simular eliminación
                btn.parentElement.remove();
                Swal.fire({
                    icon: 'success',
                    title: 'Excluída!',
                    text: 'A imagem foi removida.',
                    confirmButtonColor: '#3085d6',
                });
            }
        });
    });
});

// Eliminar todas las imágenes
apagarTodasImgsBtn.addEventListener('click', () => {
    Swal.fire({
        title: 'Tem certeza?',
        text: 'Você está prestes a apagar todas as imagens.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, apagar tudo!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const imgs = document.querySelectorAll('.img-mini');
            imgs.forEach(img => img.remove());
            Swal.fire({
                icon: 'success',
                title: 'Tudo apagado!',
                text: 'Todas as imagens foram removidas.',
                confirmButtonColor: '#3085d6',
            });
        }
    });
});
