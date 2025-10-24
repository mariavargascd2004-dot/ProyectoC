//Registro
function registrarUsuario(nombre, email, password, tipo) {
    fetch("../controllers/UsuarioController.php", {
        method: "POST",
        body: new URLSearchParams({
            accion: "registrar",
            nombre: nombre,
            email: email,
            password: password,
            tipo: tipo
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'error') {
                Swal.fire({
                    title: '¡Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#B2442E',
                    color: '#FFFFFF',
                    confirmButtonColor: '#FDCB29',
                    iconColor: '#FDCB29'
                });
            } else {
                Swal.fire({
                    title: 'Sucesso!',
                    html: data.message + "<br><br> redirecionando...",
                    icon: 'success',
                    showConfirmButton: false,
                    background: '#DCA700',
                    color: '#000000',
                    confirmButtonColor: '#B2442E',
                    iconColor: '#FFFFFF',
                    timer: 3000,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    window.location.href = "../";
                }, 2980);
            }
        })
        .catch(err => console.error("Error en la petición:", err));
}
function registrarAdminAssociado(nombre, apellido, descripcion, file, email, password, tipo) {
    const formData = new FormData();
    formData.append("accion", "registrarAdmin");
    formData.append("nombre", nombre);
    formData.append("apellido", apellido);
    formData.append("descripcion", descripcion);
    if (file) {
        formData.append("fotoPerfil", file);
    }
    formData.append("email", email);
    formData.append("password", password);
    formData.append("tipo", tipo);

    fetch("../controllers/AdminAssociadoController.php", {
        method: "POST",
        body: formData
    })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'error') {
                Swal.fire({
                    title: '¡Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#B2442E',
                    color: '#FFFFFF',
                    confirmButtonColor: '#FDCB29',
                    iconColor: '#FDCB29'
                });
            } else {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    confirmButtonText: 'Aceptar',
                    background: '#DCA700',
                    color: '#000000',
                    confirmButtonColor: '#B2442E',
                    iconColor: '#FFFFFF'
                });
            }
        })
        .catch(err => console.error("Error en la petición:", err));
}

//Login
function loginUsuario(email, password) {
    fetch("../controllers/UsuarioController.php", {
        method: "POST",
        body: new URLSearchParams({
            accion: "login",
            email: email,
            password: password,
        })
    })
        .then(res => res.json())
        .then(data => {
            console.log(data)
            if (data.status === 'error') {
                Swal.fire({
                    title: '¡Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'Aceptar',
                    background: '#B2442E',
                    color: '#FFFFFF',
                    confirmButtonColor: '#FDCB29',
                    iconColor: '#FDCB29'
                });
            } else {
                Swal.fire({
                    title: 'Sucesso!',
                    html: data.message + "<br><br> redirecionando...",
                    icon: 'success',
                    showConfirmButton: false,
                    background: '#DCA700',
                    color: '#000000',
                    confirmButtonColor: '#B2442E',
                    iconColor: '#FFFFFF',
                    timer: 3000,
                    timerProgressBar: true
                });

                setTimeout(() => {
                    window.location.href = "../";
                }, 2980);
            }
        })
        .catch(err => console.error("Error en la petición:", err));

}
