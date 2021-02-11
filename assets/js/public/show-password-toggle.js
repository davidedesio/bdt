if(document.getElementById("password")){
    document.getElementById("password").classList.add("input-password");
    document.getElementById("toggle-password").classList.remove("d-none");
    const passwordInput = document.getElementById("password");
    const togglePasswordButton = document.getElementById("toggle-password");
    togglePasswordButton.addEventListener("click", togglePassword);
    function togglePassword() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePasswordButton.setAttribute("aria-label", "Hide password.");
        } else {
            passwordInput.type = "password";
            togglePasswordButton.setAttribute(
                "aria-label",
                "Show password as plain text. " +
                "Warning: this will display your password on the screen."
            );
        }
    }
}

if(document.getElementById("registration_form_plainPassword_first")){
    document.getElementById("registration_form_plainPassword_first").classList.add("input-password");
    document.getElementById("registration_form_plainPassword_first-toggle-password").classList.remove("d-none");
    const passwordInput = document.getElementById("registration_form_plainPassword_first");
    const togglePasswordButton = document.getElementById("registration_form_plainPassword_first-toggle-password");
    togglePasswordButton.addEventListener("click", togglePasswordFirst);
    function togglePasswordFirst() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePasswordButton.setAttribute("aria-label", "Hide password.");
        } else {
            passwordInput.type = "password";
            togglePasswordButton.setAttribute(
                "aria-label",
                "Show password as plain text. " +
                "Warning: this will display your password on the screen."
            );
        }
    }
}

if(document.getElementById("registration_form_plainPassword_second")){
    document.getElementById("registration_form_plainPassword_second").classList.add("input-password");
    document.getElementById("registration_form_plainPassword_second-toggle-password").classList.remove("d-none");
    const passwordInput = document.getElementById("registration_form_plainPassword_second");
    const togglePasswordButton = document.getElementById("registration_form_plainPassword_second-toggle-password");
    togglePasswordButton.addEventListener("click", togglePasswordSecond);
    function togglePasswordSecond() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePasswordButton.setAttribute("aria-label", "Hide password.");
        } else {
            passwordInput.type = "password";
            togglePasswordButton.setAttribute(
                "aria-label",
                "Show password as plain text. " +
                "Warning: this will display your password on the screen."
            );
        }
    }
}

if(document.getElementById("user_plainPassword_first")){
    document.getElementById("user_plainPassword_first").classList.add("input-password");
    document.getElementById("user_plainPassword_first-toggle-password").classList.remove("d-none");
    const passwordInput = document.getElementById("user_plainPassword_first");
    const togglePasswordButton = document.getElementById("user_plainPassword_first-toggle-password");
    togglePasswordButton.addEventListener("click", togglePasswordUserFirst);
    function togglePasswordUserFirst() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePasswordButton.setAttribute("aria-label", "Hide password.");
        } else {
            passwordInput.type = "password";
            togglePasswordButton.setAttribute(
                "aria-label",
                "Show password as plain text. " +
                "Warning: this will display your password on the screen."
            );
        }
    }
}

if(document.getElementById("user_plainPassword_second")){
    document.getElementById("user_plainPassword_second").classList.add("input-password");
    document.getElementById("user_plainPassword_second-toggle-password").classList.remove("d-none");
    const passwordInput = document.getElementById("user_plainPassword_second");
    const togglePasswordButton = document.getElementById("user_plainPassword_second-toggle-password");
    togglePasswordButton.addEventListener("click", togglePasswordUserSecond);
    function togglePasswordUserSecond() {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            togglePasswordButton.setAttribute("aria-label", "Hide password.");
        } else {
            passwordInput.type = "password";
            togglePasswordButton.setAttribute(
                "aria-label",
                "Show password as plain text. " +
                "Warning: this will display your password on the screen."
            );
        }
    }
}