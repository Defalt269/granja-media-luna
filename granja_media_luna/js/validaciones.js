// Validaciones JavaScript para Granja Media Luna

// Validación general de formularios
function validarFormulario(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;

    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let valido = true;

    inputs.forEach(input => {
        if (!input.value.trim()) {
            mostrarError(input, 'Este campo es obligatorio');
            valido = false;
        } else {
            limpiarError(input);
        }
    });

    return valido;
}

// Validación específica para productos
function validarProducto() {
    const nombre = document.getElementById('nombre');
    const precio = document.getElementById('precio');
    const cantidad = document.getElementById('cantidad');

    let valido = true;

    // Validar nombre
    if (nombre && nombre.value.trim().length < 3) {
        mostrarError(nombre, 'El nombre debe tener al menos 3 caracteres');
        valido = false;
    }

    // Validar precio
    if (precio && (isNaN(precio.value) || parseFloat(precio.value) <= 0)) {
        mostrarError(precio, 'El precio debe ser un número positivo');
        valido = false;
    }

    // Validar cantidad
    if (cantidad && (isNaN(cantidad.value) || parseInt(cantidad.value) < 0)) {
        mostrarError(cantidad, 'La cantidad debe ser un número no negativo');
        valido = false;
    }

    return valido;
}

// Validación específica para clientes
function validarCliente() {
    const nombre = document.getElementById('nombre');
    const cedula = document.getElementById('cedula');
    const email = document.getElementById('correo');

    let valido = true;

    // Validar nombre
    if (nombre && nombre.value.trim().length < 3) {
        mostrarError(nombre, 'El nombre debe tener al menos 3 caracteres');
        valido = false;
    }

    // Validar cédula
    if (cedula && !/^\d{6,12}$/.test(cedula.value)) {
        mostrarError(cedula, 'La cédula debe contener entre 6 y 12 dígitos');
        valido = false;
    }

    // Validar email
    if (email && email.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
        mostrarError(email, 'Ingrese un correo electrónico válido');
        valido = false;
    }

    return valido;
}

// Validación específica para facturación
function validarFactura() {
    const cliente = document.getElementById('cliente');
    const productos = document.querySelectorAll('select[name="productos[]"]');
    const cantidades = document.querySelectorAll('input[name="cantidades[]"]');

    let valido = true;

    // Validar cliente
    if (!cliente || !cliente.value) {
        mostrarError(cliente, 'Debe seleccionar un cliente');
        valido = false;
    }

    // Validar que al menos un producto esté seleccionado
    let productoSeleccionado = false;
    productos.forEach(select => {
        if (select.value) productoSeleccionado = true;
    });

    if (!productoSeleccionado) {
        alert('Debe seleccionar al menos un producto');
        valido = false;
    }

    // Validar cantidades
    cantidades.forEach(input => {
        if (input.value && (isNaN(input.value) || parseInt(input.value) <= 0)) {
            mostrarError(input, 'La cantidad debe ser un número positivo');
            valido = false;
        }
    });

    return valido;
}

// Funciones auxiliares
function mostrarError(elemento, mensaje) {
    limpiarError(elemento);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = mensaje;
    errorDiv.style.color = 'red';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '0.25rem';
    elemento.parentNode.insertBefore(errorDiv, elemento.nextSibling);
    elemento.style.borderColor = 'red';
}

function limpiarError(elemento) {
    const errorDiv = elemento.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
    elemento.style.borderColor = '#ddd';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Validación de formulario de productos
    const formProducto = document.getElementById('formProducto');
    if (formProducto) {
        formProducto.addEventListener('submit', function(e) {
            if (!validarProducto()) {
                e.preventDefault();
            }
        });
    }

    // Validación de formulario de clientes
    const formCliente = document.getElementById('formCliente');
    if (formCliente) {
        formCliente.addEventListener('submit', function(e) {
            if (!validarCliente()) {
                e.preventDefault();
            }
        });
    }

    // Validación de formulario de facturación
    const formFactura = document.getElementById('formFactura');
    if (formFactura) {
        formFactura.addEventListener('submit', function(e) {
            if (!validarFactura()) {
                e.preventDefault();
            }
        });
    }

    // Validación de formulario de contacto
    const formContacto = document.getElementById('formContacto');
    if (formContacto) {
        formContacto.addEventListener('submit', function(e) {
            if (!validarFormulario('formContacto')) {
                e.preventDefault();
            } else {
                alert('Mensaje enviado exitosamente. Nos pondremos en contacto pronto.');
                formContacto.reset();
                e.preventDefault(); // Prevenir envío real para demo
            }
        });
    }

    // Limpiar errores al escribir
    document.addEventListener('input', function(e) {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
            limpiarError(e.target);
        }
    });
});