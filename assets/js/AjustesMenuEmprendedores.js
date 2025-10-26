document.addEventListener("DOMContentLoaded", () => {

    // ALERTA DE MODIFICAÇÃO / SALVAR
    const botoesModificar = document.querySelectorAll(".btn--amarelo");
    botoesModificar.forEach(botao => {
        botao.addEventListener("click", () => {
            Swal.fire({
                icon: "success",
                title: "Modificação realizada!",
                text: "As alterações foram salvas com sucesso.",
                confirmButtonColor: "#f1c40f"
            });
        });
    });

    // ALERTA DE EXCLUSÃO
    const botoesExcluir = document.querySelectorAll(".btn--vermelho");
    botoesExcluir.forEach(botao => {
        botao.addEventListener("click", () => {
            Swal.fire({
                title: "Tem certeza?",
                text: "Essa ação não poderá ser desfeita!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#999",
                confirmButtonText: "Sim, eliminar!",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: "success",
                        title: "Eliminado!",
                        text: "A operação foi concluída com sucesso.",
                        confirmButtonColor: "#d33"
                    });
                }
            });
        });
    });

});
