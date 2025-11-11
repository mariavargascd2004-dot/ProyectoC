document.addEventListener("DOMContentLoaded", () => {

    const eventosContainer = document.getElementById("eventos-container");

    if (!eventosContainer) {
        return;
    }

    eventosContainer.addEventListener("click", (e) => {

        const btn = e.target.closest(".ver-detalhes-btn");

        if (btn) {
            e.preventDefault();

            try {
                const eventoData = JSON.parse(btn.dataset.evento);

                mostrarModalEvento(eventoData);

            } catch (error) {
                console.error("Error al leer los datos del evento:", error);
                Swal.fire('Erro', 'Não foi possível carregar os detalhes do evento.', 'error');
            }
        }
    });


    function mostrarModalEvento(evento) {

        const dataInicio = formatarDataLegivelJS(evento.fechaInicio);
        const horaInicio = formatarHoraJS(evento.fechaInicio);
        const dataFinal = formatarDataLegivelJS(evento.fechaFinal);
        const horaFinal = formatarHoraJS(evento.fechaFinal);

        Swal.fire({
            title: `<span style="font-family: 'Alegreya SC', serif; font-weight: 700;">${evento.titulo}</span>`,

            html: `
                <div style="text-align: left; font-family: 'Work Sans', sans-serif;">
                    <img src="${evento.imagen || '../assets/img/CasaSolidaria/defaultProduct.png'}" 
                         alt="${evento.titulo}" 
                         style="width: 100%; height: 250px; object-fit: cover; border-radius: 8px; margin-bottom: 15px;">
                    
                    <p style="font-size: 1.1rem; white-space: pre-wrap;">${evento.descripcion || 'Sem descrição.'}</p>
                    
                    <hr>

                    ${evento.ubicacion ? `
                        <p style="font-size: 1rem; margin-bottom: 8px;">
                            <strong><i class="fa-solid fa-map-marker-alt"></i> Local:</strong> ${evento.ubicacion}
                        </p>` : ''}

                    <p style="font-size: 1rem; margin-bottom: 8px;">
                        <strong><i class="fa-solid fa-calendar-day"></i> Início:</strong> ${dataInicio}
                        ${horaInicio ? ` às ${horaInicio} hs` : ''}
                    </p>

                    <p style="font-size: 1rem;">
                        <strong><i class="fa-solid fa-calendar-check"></i> Final:</strong> ${dataFinal}
                        ${horaFinal ? ` às ${horaFinal} hs` : ''}
                    </p>
                </div>
            `,

            showConfirmButton: true,
            confirmButtonText: 'Fechar',
            confirmButtonColor: '#B2442E',
            width: '600px'
        });
    }

    function formatarDataLegivelJS(dataStr) {
        if (!dataStr || dataStr.startsWith('0000-00-00')) return "N/A";
        const meses = [
            'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
        ];
        const data = new Date(dataStr.replace(' ', 'T'));
        const dia = data.getDate().toString().padStart(2, '0');
        const mesIndex = data.getMonth();
        const ano = data.getFullYear();
        return `${dia} de ${meses[mesIndex]} de ${ano}`;
    }

    function formatarHoraJS(dataStr) {
        if (!dataStr || dataStr.startsWith('0000-00-00')) return null;
        const data = new Date(dataStr.replace(' ', 'T'));
        const hora = data.getHours().toString().padStart(2, '0');
        const minuto = data.getMinutes().toString().padStart(2, '0');
        const horaCompleta = `${hora}:${minuto}`;
        return (horaCompleta !== '00:00') ? horaCompleta : null;
    }

});