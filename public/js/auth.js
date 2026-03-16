(function () {
    // Boutons de changement de mode (connexion / inscription).
    var loginButton = document.getElementById("mode-login");
    var signupButton = document.getElementById("mode-signup");
    var switchLink = document.getElementById("switch-link");
    var authTitle = document.getElementById("auth-title");
    var submitButton = document.getElementById("submit-btn");
    var switchText = document.getElementById("switch-text");
    var accountTypeInput = document.getElementById("account-type");
    var roleSwitchButtons = document.querySelectorAll(".role-switch__btn");

    var loginOnlyBlocks = document.querySelectorAll(".login-only");
    var signupOnlyBlocks = document.querySelectorAll(".signup-only");
    var roleStudentBlocks = document.querySelectorAll(".role-student");
    var roleCompanyBlocks = document.querySelectorAll(".role-company");
    var rolePiloteBlocks = document.querySelectorAll(".role-pilote");

    // Affiche ou masque un groupe de champs.
    function setHidden(blocks, isHidden) {
        blocks.forEach(function (block) {
            block.classList.toggle("is-hidden", isHidden)
        });
    }

    // Affiche les champs selon le role choisi.
    function updateRoleFields() {
        if (!accountTypeInput) {
            return;
        }

        var selectedRole = accountTypeInput.value;

        setHidden(roleStudentBlocks, true);
        setHidden(roleCompanyBlocks, true);
        setHidden(rolePiloteBlocks, true);

        if (selectedRole === "etudiant") {
            setHidden(roleStudentBlocks, false);
        }
        else if (selectedRole === "entreprise") {
            setHidden(roleCompanyBlocks, false);
        }
        else if (selectedRole === "pilote") {
            setHidden(rolePiloteBlocks, false);
        }

        roleSwitchButtons.forEach(function (button) {
            button.classList.toggle("is-active", button.getAttribute("data-role") === selectedRole);
        });
    }

    // Bascule entre l'ecran de connexion et d'inscription.
    function setMode(mode) {
        var isLogin = mode === "login";

        document.getElementById("auth-mode").value = mode;

        setHidden(loginOnlyBlocks, !isLogin);
        setHidden(signupOnlyBlocks, isLogin);

        loginButton.classList.toggle("is-active", isLogin);
        signupButton.classList.toggle("is-active", !isLogin);

        loginButton.setAttribute("aria-selected", String(isLogin));
        signupButton.setAttribute("aria-selected", String(!isLogin));

        authTitle.textContent = isLogin ? "CONNEXION" : "INSCRIPTION";
        submitButton.textContent = isLogin ? "CONNEXION" : "JE M'INSCRIS";

        if (!isLogin) {
            accountTypeInput.value = "etudiant";
            updateRoleFields();
        }
    }

    if (loginButton && signupButton) {
        loginButton.addEventListener("click", function () {
            setMode("login");
        });

        signupButton.addEventListener("click", function () {
            setMode("signup");
        });
    }

    if (switchLink) {
        switchLink.addEventListener("click", function (event) {
            event.preventDefault();
            setMode("signup");
        });
    }

    if (accountTypeInput && roleSwitchButtons.length > 0) {
        roleSwitchButtons.forEach(function (button) {
            button.addEventListener("click", function () {
                accountTypeInput.value = button.getAttribute("data-role");
                updateRoleFields();
            });
        });
    }

    setMode("login");
})();