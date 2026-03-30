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
  ciret: {
    pattern: /^\d{14}$/,
    message: "Le SIRET doit contenir exactement 14 chiffres."
  },
  school: {
    minLength: 2,
    message: "Entré un nom d'école valide (2 caractères minimum)."
  }
  activity_sector: {
    message: "Veuillez sélectionner un secteur d'activité."
  }
  pilote: {
    message: "Veuillez sélectionner un pilote."
  }
};


// =========================================
// 2) SCHÉMAS PAR CONTEXTE (auth/profile + rôle)
// =========================================
const SCHEMAS = {
  auth: {
    signupCommon: {
      email: "email",
      password: "password"
    },
    signupByRole: {
      etudiant: {
        name: "name",
        lastName: "name",
        school: "school"
      },
      entreprise: {
        companyName: "companyName",
        phone: "phone",
        ciret: "ciret"
      },
      pilote: {
        name: "name",
        lastName: "name",
        phone: "phone"
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
        lastName: "name",
        school: "school"
      },
      entreprise: {
        companyName: "companyName",
        phone: "phone"
      },
      pilote: {
        name: "name",
        lastName: "name",
        phone: "phone"
      }
    }
  }
};


// =========================================
// 3) HELPERS UI (icône + message)
// =========================================
function getValidationNodes(field) {
  // TODO:
  // retourner { iconEl, errorEl } en cherchant dans le parent du champ
}

function showValid(field) {
  // TODO:
  // afficher la coche verte
  // vider le message d'erreur
}

function showInvalid(field, message) {
  // TODO:
  // afficher le X rouge
  // afficher le message
}

function resetState(field) {
  // TODO:
  // masquer icône/message quand champ vide
}


// =========================================
// 4) HELPERS MÉTIER (actif/visible/règle)
// =========================================
function isFieldActive(field) {
  // TODO:
  // true si champ existe, pas disabled, et visible
  // astuce visibilité: offsetParent !== null
  return true;
}

function mergeSchemas() {
  // TODO:
  // fusionner plusieurs objets champs->règles en un seul
  // exemple attendu: { email: "email", password: "password", ... }
  return {};
}

function getRoleFromForm(form) {
  // TODO:
  // lire le rôle courant depuis account_type
  // fallback possible depuis data-role ou hidden input
  return "";
}

function getAuthModeFromForm(form) {
  // TODO:
  // lire auth_mode (login/signup)
  return "";
}


// =========================================
// 5) COMPOSITION DU SCHÉMA ACTIF
// =========================================
function buildActiveSchema(form, pageType) {
  // pageType: "auth" ou "profile"
  // TODO:
  // - si auth + login => schema login
  // - si auth + signup => signupCommon + signupByRole[role]
  // - si profile => common + byRole[role]
  // - retourner objet final champs->règles
  return {};
}


// =========================================
// 6) VALIDATION D’UN CHAMP
// =========================================
function validateField(field, ruleKey) {
  // TODO:
  // 1) ignorer si champ inactif
  // 2) récupérer RULES[ruleKey]
  // 3) value = field.value.trim()
  // 4) si vide => resetState + return true/false selon stratégie
  // 5) tester pattern ou minLength
  // 6) showValid ou showInvalid
  // 7) retourner booléen final
  return true;
}


// =========================================
// 7) VALIDATION D’UN FORMULAIRE ENTIER
// =========================================
function validateForm(form, schema) {
  let formIsValid = true;

  // TODO:
  // parcourir schema (fieldName -> ruleKey)
  // retrouver chaque champ (id ou name)
  // valider
  // accumuler le résultat global

  return formIsValid;
}


// =========================================
// 8) BRANCHEMENT DES ÉVÉNEMENTS
// =========================================
function attachValidation(form, pageType) {
  // TODO:
  // 1) construire schema actif
  // 2) pour chaque champ:
  //    - blur => validateField
  //    - input => revalidate seulement si déjà feedback affiché
  // 3) submit => rebuild schema actif + validateForm
  //    - si invalide => event.preventDefault()

  // IMPORTANT:
  // en auth, le mode et le rôle peuvent changer dynamiquement
  // donc recalculer schema au submit (et éventuellement au switch de rôle)
}


// =========================================
// 9) INITIALISATION GLOBALE
// =========================================
function initFormValidation() {
  // TODO:
  // détecter les formulaires présents sur la page
  // exemple:
  // - formulaire auth
  // - formulaire profile edit
  // - formulaire delete account
  // appeler attachValidation(form, "auth"|"profile")
}

document.addEventListener("DOMContentLoaded", initFormValidation);