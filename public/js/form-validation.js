// =========================================
// 1) RÈGLES GLOBALES (réutilisables partout)
// =========================================
const RULES = {
  email: {
    pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    message:"Veuillez entrer une adresse e-mail valide."
  },
  password: {
    pattern: /^(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]).{8,}$/,
    message: "8 caractères minimum, dont au moins 1 chiffre et 1 caractère spécial."
  },
  name: {
    minLength: 2,
    message: "Entré un nom valide (2 caractères minimum)."
  },
  phone: {
    pattern: /^\+?\d{10,15}$/,
    message: "Veuillez entrer un numéro de téléphone valide."
  },
  companyName: {
    minLength: 2,
    message: "Entré un nom valide (2 caractères minimum)."
  },
    companyAddress: {
    minLength: 5,
    message: "Veuillez entrer une adresse valide."
  },
  ciret: {
    pattern: /^\d{14}$/,
    message: "Le CIRET doit contenir exactement 14 chiffres."
  },
  school: {
    minLength: 2,
    message: "Entré un nom d'école valide (2 caractères minimum)."
  },
  activity_sector: {
    message: "Veuillez sélectionner un secteur d'activité."
  },
  selectPilote: {
    message: "Veuillez sélectionner un pilote."
  }
};


// =========================================
// 2) SCHÉMAS PAR CONTEXTE (auth/profile + rôle)
// =========================================
const SCHEMAS = {
  auth: {
    login: {
      email:    "email",
      password: "password"
    },
    signupCommon: {
      email:    "email",
      password: "password"
    },
    signupByRole: {
      etudiant: {
        name:           "name",
        last_name:      "name",       // name="last_name" dans ton HTML
        training_pilot: "selectPilote" // name="training_pilot"
      },
      entreprise: {
        company_name:    "companyName",
        activity_sector: "activity_sector",
        company_address: "companyAddress",
        company_ciret:   "ciret",
        phone:           "phone"
      },
      pilote: {
        name:      "name",
        last_name: "name",            // name="last_name"
        phone:     "phone",
        school:    "school"
      }
    }
  },
  profile: {
    common: {
      email: "email"
    },
    byRole: {
      etudiant: {
        name: "name",
        last_name: "name"
      },
      pilote: {
        name: "name",
        last_name: "name",
        phone: "phone",
        school: "school"
      },
      entreprise: {
        company_name: "companyName",
        phone: "phone"
      }
    }
  },
  deleteAccount: {
    email: "email",
    password: "password"
  }
};


// =========================================
// 3) HELPERS UI (icône + message)
// =========================================

function getValidationNodes(field) {
  const wrapper = field.closest(".input-wrapper") || field.parentElement;
  const iconEl = wrapper ? wrapper.querySelector(".validation-icon") : null;

  const group = field.closest(".form-group") || (wrapper ? wrapper.parentElement : null);
  const errorEl = group ? group.querySelector(".error-message") : null;

  return { wrapper, iconEl, errorEl };
}

function showValid(field) {
  const { wrapper, iconEl, errorEl } = getValidationNodes(field);

  if (wrapper) {
    wrapper.classList.add("is-valid");
    wrapper.classList.remove("is-invalid");
  }

  if (iconEl)  { iconEl.textContent = "✔"; iconEl.style.display = "block"; iconEl.style.color = "#27ae60"; }
  if (errorEl) { errorEl.textContent = ""; errorEl.style.display = "none"; }
}

function showInvalid(field, message) {
  const { wrapper, iconEl, errorEl } = getValidationNodes(field);

  if (wrapper) {
    wrapper.classList.add("is-invalid");
    wrapper.classList.remove("is-valid");
  }

  if (iconEl)  { iconEl.textContent = "✖"; iconEl.style.display = "block"; iconEl.style.color = "#e74c3c"; }
  if (errorEl) { errorEl.textContent = message; errorEl.style.display = "block"; }
}

function resetState(field) {
  const { wrapper, iconEl, errorEl } = getValidationNodes(field);

  if (wrapper) {
    wrapper.classList.remove("is-valid", "is-invalid");
  }

  if (iconEl)  iconEl.style.display  = "none";
  if (errorEl) errorEl.style.display = "none";
}


// =========================================
// 4) HELPERS MÉTIER (actif/visible/règle)
// =========================================

function isFieldActive(field) {
  // Un champ inactif = inexistant, disabled, ou visuellement masqué
  // offsetParent === null indique que l'élément n'est pas rendu (display:none
  // sur lui ou un de ses parents, comme la div .is-hidden de ton twig)
  return (
    field !== null &&
    field !== undefined &&
    !field.disabled &&
    field.offsetParent !== null
  );
}

function mergeSchemas(...schemas) {
  // Object.assign fusionne tous les objets passés en argument
  // dans un nouvel objet vide, de gauche à droite
  // ex: mergeSchemas({email:"email"}, {name:"name"})
  //   → { email: "email", name: "name" }
  return Object.assign({}, ...schemas);
}

function getRoleFromForm(form) {
  // Cherche d'abord un input[name="account_type"] (radio ou select)
  const roleInput = form.querySelector('[name="account_type"]');
  if (roleInput) return roleInput.value;

  // Fallback : attribut data-role sur le formulaire lui-même
  // ex: <form data-role="etudiant">
  if (form.dataset.role) return form.dataset.role;

  return "";
}

function getAuthModeFromForm(form) {
  // Cherche un input caché qui indique le mode courant
  // ex: <input type="hidden" name="auth_mode" value="login">
  const modeInput = form.querySelector('[name="auth_mode"]');
  if (modeInput) return modeInput.value;

  return "";
}


// =========================================
// 5) COMPOSITION DU SCHÉMA ACTIF
// =========================================

function buildActiveSchema(form, pageType) {
  const role = getRoleFromForm(form);

  if (pageType === "auth") {
    const mode = getAuthModeFromForm(form);

    if (mode === "login") {
      // Login : pas de validation front demandée
      return {};
    }

    if (mode === "signup") {
      // Signup : champs communs + champs spécifiques au rôle
      const roleSchema = SCHEMAS.auth.signupByRole[role] || {};
      return mergeSchemas(SCHEMAS.auth.signupCommon, roleSchema);
    }
  }

  if (pageType === "profile") {
    // Profile : email commun + champs spécifiques au rôle
    const roleSchema = SCHEMAS.profile.byRole[role] || {};
    return mergeSchemas(SCHEMAS.profile.common, roleSchema);
  }

  if (pageType === "deleteAccount") {
    return SCHEMAS.deleteAccount;
  }

  // Cas non reconnu : schéma vide, rien ne sera validé
  return {};
}


// =========================================
// 6) VALIDATION D'UN CHAMP
// =========================================

function validateField(field, ruleKey) {
  // Étape 1 : champ inactif → on l'ignore complètement
  if (!isFieldActive(field)) return true;

  // Étape 2 : règle introuvable → on ne bloque pas
  const rule = RULES[ruleKey];
  if (!rule) return true;

  // Étape 3 : lecture + nettoyage de la valeur
  const value = field.value.trim();

  // Étape 4 : valeur vide → reset silencieux
  if (value === "") {
    showInvalid(field, rule.message);
    return false;
  }

  // Étape 5 : test selon le type de règle
  let isValid = false;

  if (rule.pattern) {
    isValid = rule.pattern.test(value);
  } else if (rule.minLength) {
    isValid = value.length >= rule.minLength;
  } else if (rule.allowed) {
    isValid = rule.allowed.includes(value);
  } else {
    // Pas de critère technique (ex: select secteur)
    // Une valeur non vide suffit
    isValid = true;
  }

  // Étape 6 : mise à jour de l'UI
  if (isValid) {
    showValid(field);
  } else {
    showInvalid(field, rule.message);
  }

  // Étape 7 : retour du résultat pour validateForm
  return isValid;
}


// =========================================
// 7) VALIDATION D'UN FORMULAIRE ENTIER
// =========================================

function validateForm(form, schema) {
  let formIsValid = true;

  // On parcourt chaque entrée du schéma : { fieldName → ruleKey }
  for (const [fieldName, ruleKey] of Object.entries(schema)) {

    // On cherche le champ par id d'abord, puis par name en fallback
    // Le fallback [name=...] est indispensable pour les <select>
    // dont le name diffère de l'id (ex: activity_sector)
    const field =
      form.querySelector(`#${fieldName}`) ||
      form.querySelector(`[name="${fieldName}"]`);

    // Champ absent du DOM : on ignore sans bloquer
    if (!field) continue;

    const fieldValid = validateField(field, ruleKey);

    // On n'utilise pas un && direct pour ne pas court-circuiter :
    // on veut que TOUS les champs affichent leur erreur simultanément
    if (!fieldValid) formIsValid = false;
  }

  return formIsValid;
}


// =========================================
// 8) BRANCHEMENT DES ÉVÉNEMENTS
// =========================================

function attachValidation(form, pageType) {
  const getRuleKeyForField = (field) => {
    const schema = buildActiveSchema(form, pageType);
    if (!schema) return null;
    return schema[field.id] || schema[field.name] || null;
  };

  // blur : valide quand l'utilisateur quitte le champ
  form.addEventListener("blur", (event) => {
    const field = event.target;
    if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
      return;
    }

    const ruleKey = getRuleKeyForField(field);
    if (!ruleKey) return;
    validateField(field, ruleKey);
  }, true);

  // input/change : revalide en temps réel uniquement après premier feedback
  const liveRevalidate = (event) => {
    const field = event.target;
    if (!(field instanceof HTMLInputElement || field instanceof HTMLSelectElement || field instanceof HTMLTextAreaElement)) {
      return;
    }

    const ruleKey = getRuleKeyForField(field);
    if (!ruleKey) return;

    const { wrapper, errorEl } = getValidationNodes(field);
    const hasFeedback =
      (errorEl && errorEl.style.display !== "none") ||
      (wrapper && (wrapper.classList.contains("is-valid") || wrapper.classList.contains("is-invalid")));

    if (hasFeedback) {
      validateField(field, ruleKey);
    }
  };

  form.addEventListener("input", liveRevalidate);
  form.addEventListener("change", liveRevalidate);

  // Étape 3 : validation complète à la soumission
  form.addEventListener("submit", (event) => {
    // On recalcule le schéma car le rôle ou le mode
    // peuvent avoir changé dynamiquement depuis l'init
    const schema = buildActiveSchema(form, pageType);
    const isValid = validateForm(form, schema);

    if (!isValid) {
      // On bloque la soumission native du formulaire
      event.preventDefault();
    }
  });
}


// =========================================
// 9) INITIALISATION GLOBALE
// =========================================

function initFormValidation() {
  // Formulaire d'authentification
  const authForm = document.querySelector('form.auth-form[action="/loginregister"]');
  if (authForm) attachValidation(authForm, "auth");

  // Formulaire de suppression de compte
  const deleteForm = document.querySelector('form.auth-form[action="/delete_account"]');
  if (deleteForm) attachValidation(deleteForm, "deleteAccount");

  // Formulaire de modification du profil
  const profileForms = document.querySelectorAll('form[action="/modify_profile"]');
  profileForms.forEach((profileForm) => attachValidation(profileForm, "profile"));
}

document.addEventListener("DOMContentLoaded", initFormValidation);