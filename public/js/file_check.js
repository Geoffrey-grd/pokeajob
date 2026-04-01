input.addEventListener("change", () => {
  const file = input.files[0];

  if (!file) return;

  // Taille max : 2 MB
  const maxSize = 2 * 1024 * 1024;

  // Types autorisés
  const allowedExtensions = [".pdf", ".doc", ".docx", ".odt"];

  if (file.size > maxSize) {
    alert("Fichier trop volumineux (max 2MB)");
    return;
  }

  if (!allowedExtensions.includes(file.type)) {
    alert("Format invalide (PNG ou JPEG uniquement)");
    return;
  }

  console.log("Fichier valide !");
});