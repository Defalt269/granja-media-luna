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
    errorDiv.style.color = '#e74c3c';
    errorDiv.style.fontSize = '0.85rem';
    errorDiv.style.marginTop = '0.5rem';
    errorDiv.style.fontWeight = '500';
    errorDiv.style.animation = 'fadeInError 0.3s ease-in';
    elemento.parentNode.insertBefore(errorDiv, elemento.nextSibling);
    elemento.style.borderColor = '#e74c3c';
    elemento.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.1)';
    elemento.style.animation = 'shake 0.5s ease-in-out';
}

function limpiarError(elemento) {
    const errorDiv = elemento.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.style.animation = 'fadeOutError 0.3s ease-out';
        setTimeout(() => {
            if (errorDiv.parentNode) {
                errorDiv.remove();
            }
        }, 300);
    }
    elemento.style.borderColor = '#e9ecef';
    elemento.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.1)';
}

// Función para mostrar mensaje de éxito moderno
function mostrarMensajeExito(mensaje) {
    // Remover mensaje anterior si existe
    const mensajeAnterior = document.querySelector('.mensaje-exito');
    if (mensajeAnterior) {
        mensajeAnterior.remove();
    }

    // Crear nuevo mensaje
    const mensajeDiv = document.createElement('div');
    mensajeDiv.className = 'mensaje-exito';
    mensajeDiv.innerHTML = `
        <div class="mensaje-contenido">
            <span class="mensaje-icono">✅</span>
            <span class="mensaje-texto">${mensaje}</span>
        </div>
    `;

    // Agregar al DOM
    document.body.appendChild(mensajeDiv);

    // Animar entrada
    setTimeout(() => {
        mensajeDiv.classList.add('mostrar');
    }, 10);

    // Auto-remover después de 5 segundos
    setTimeout(() => {
        mensajeDiv.classList.remove('mostrar');
        setTimeout(() => {
            if (mensajeDiv.parentNode) {
                mensajeDiv.remove();
            }
        }, 300);
    }, 5000);
}

// Animaciones CSS para errores y mensajes de éxito
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInError {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeOutError {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .mensaje-exito {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease;
    }

    .mensaje-exito.mostrar {
        opacity: 1;
        transform: translateY(0);
    }

    .mensaje-contenido {
        background: linear-gradient(135deg, #4caf50 0%, #66bb6a 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(76, 175, 80, 0.3);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        font-weight: 500;
        max-width: 400px;
    }

    .mensaje-icono {
        font-size: 1.5rem;
    }

    .mensaje-texto {
        flex: 1;
    }

    @media (max-width: 480px) {
        .mensaje-exito {
            left: 10px;
            right: 10px;
            top: 10px;
        }

        .mensaje-contenido {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
    }
`;
document.head.appendChild(style);

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
                // Mostrar mensaje de éxito moderno
                mostrarMensajeExito('¡Mensaje enviado exitosamente! Nos pondremos en contacto pronto.');
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