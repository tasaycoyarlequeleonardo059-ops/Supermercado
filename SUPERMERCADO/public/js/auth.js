document.addEventListener("DOMContentLoaded", function () {
    const tipoDOIElect = document.getElementById("tipoDOI");
    const numDOIInput = document.getElementById("numDOI");
    const telefonoInput = document.getElementById("telefono");
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById("confirmPassword");
    const togglePassword = document.getElementById("togglePassword");
    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    const registroForm = document.getElementById("registroForm");

    // --- 1. Mostrar / Ocultar Contraseña Principal ---
    if (togglePassword && passwordInput) {
        togglePassword.addEventListener("click", function (e) {
            e.preventDefault();
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;
            this.textContent = type === "password" ? "Mostrar" : "Ocultar";
        });
    }

    // --- 2. Mostrar / Ocultar Confirmar Contraseña ---
    if (toggleConfirmPassword && confirmPasswordInput) {
        toggleConfirmPassword.addEventListener("click", function (e) {
            e.preventDefault();
            const type = confirmPasswordInput.type === "password" ? "text" : "password";
            confirmPasswordInput.type = type;
            this.textContent = type === "password" ? "Mostrar" : "Ocultar";
        });
    }

    // --- 3. Cambiar dinámica de campos según el Tipo de Documento ---
    if (tipoDOIElect && numDOIInput) {
        tipoDOIElect.addEventListener("change", function () {
            numDOIInput.value = ""; 
            const seleccion = this.value.toUpperCase(); 

            switch (seleccion) {
                case "DNI":
                    numDOIInput.placeholder = "8 dígitos numéricos";
                    numDOIInput.maxLength = 8;
                    break;
                case "CE":
                    numDOIInput.placeholder = "12 dígitos numéricos";
                    numDOIInput.maxLength = 12;
                    break;
                case "PASAPORTE":
                    numDOIInput.placeholder = "9 a 10 caracteres alfanuméricos";
                    numDOIInput.maxLength = 10;
                    break;
                case "CPP/PTP":
                case "CPC/PTP":
                    numDOIInput.value = "C"; 
                    numDOIInput.placeholder = "C + 14 números";
                    numDOIInput.maxLength = 15; 
                    break;
            }
        });

        numDOIInput.addEventListener("input", function () {
            const tipo = tipoDOIElect.value.toUpperCase();
            let valor = this.value;

            if (tipo === "DNI" || tipo === "CE") {
                this.value = valor.replace(/\D/g, "");
            } 
            else if (tipo === "PASAPORTE") {
                this.value = valor.replace(/[^A-Za-z0-9]/g, "").toUpperCase();
            } 
            else if (tipo === "CPP/PTP" || tipo === "CPC/PTP") {
                if (valor.length === 0 || valor.charAt(0).toUpperCase() !== 'C') {
                    this.value = "C";
                } else {
                    const parteNumerica = valor.slice(1).replace(/\D/g, "");
                    this.value = "C" + parteNumerica;
                }
            }
        });
    }

    // --- 4. Restricción para el Teléfono ---
    if (telefonoInput) {
        telefonoInput.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, "");
        });
    }

    // --- 5. Validaciones de envío (Submit) ---
    if (registroForm) {
        registroForm.addEventListener("submit", function (e) {
            const tipo = tipoDOIElect.value.toUpperCase();
            const doiValor = numDOIInput.value;
            const telValor = telefonoInput.value;

            // Validación de Teléfono
            if (telValor.length !== 8) {
                e.preventDefault();
                alert("⚠️ El número de teléfono debe tener exactamente 8 dígitos.");
                telefonoInput.focus();
                return;
            }

            // Validaciones de Documentos
            if (tipo === "DNI" && doiValor.length !== 8) {
                e.preventDefault();
                alert("⚠️ El DNI debe tener exactamente 8 dígitos.");
                numDOIInput.focus();
                return;
            }
            
            if (tipo === "CE" && doiValor.length !== 12) {
                e.preventDefault();
                alert("⚠️ El Carnet de Extranjería (C.E.) debe tener exactamente 12 dígitos.");
                numDOIInput.focus();
                return;
            }

            if (tipo === "PASAPORTE" && (doiValor.length < 9 || doiValor.length > 10)) {
                e.preventDefault();
                alert("⚠️ El Pasaporte debe tener entre 9 y 10 caracteres.");
                numDOIInput.focus();
                return;
            }

            if ((tipo === "CPP/PTP" || tipo === "CPC/PTP") && doiValor.length !== 15) {
                e.preventDefault();
                alert("⚠️ El código CPP/PTP debe empezar con 'C' seguido de 14 números.");
                numDOIInput.focus();
                return;
            }

            // --- NUEVA VALIDACIÓN: Coincidencia de Contraseñas ---
            if (passwordInput && confirmPasswordInput) {
                const passValue = passwordInput.value;
                const confirmValue = confirmPasswordInput.value;

                // 1. Validar políticas de seguridad básicas (Opcional, pero recomendado)
                const tieneLongitud  = passValue.length >= 9;
                const tieneMayuscula = /[A-Z]/.test(passValue);
                const tieneMinuscula = /[a-z]/.test(passValue);
                const tieneNumero    = /\d/.test(passValue);
                const tieneEspecial  = passValue.replace(/[A-Za-z0-9]/g, '').length > 0;

                if (!tieneLongitud || !tieneMayuscula || !tieneMinuscula || !tieneNumero || !tieneEspecial) {
                    e.preventDefault();
                    alert("❌ La contraseña debe tener al menos 9 caracteres, una Mayúscula, una Minúscula, un Número y un Símbolo.");
                    passwordInput.focus();
                    return;
                }

                // 2. Validar que coincidan exactamente
                if (passValue !== confirmValue) {
                    e.preventDefault(); // Detiene el envío del formulario si no coinciden
                    alert("⚠️ Las contraseñas ingresadas no coinciden. Por favor, verifíquelas.");
                    confirmPasswordInput.focus();
                    return;
                }
            }
        });
    }
});