/*
 * Contrôle de saisie (JavaScript) : formulaire Terrain
 * Mêmes règles que validateTerrain() dans TerrainController.php
 */
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formTerrain');
  if (!form) return;

  // Affiche (ou efface) le message d'erreur sous un champ
  function afficherErreur(idChamp, message) {
    const champ = document.getElementById(idChamp);
    const zone = document.getElementById('erreur-' + idChamp);
    zone.textContent = message;
    if (message) {
      champ.classList.add('is-invalid');
    } else {
      champ.classList.remove('is-invalid');
    }
  }

  form.addEventListener('submit', function (event) {
    let valide = true;

    // Nom
    const nom = document.getElementById('nom').value.trim();
    const regexNom = /^[\p{L}0-9\s'\-]+$/u;
    if (nom === '') {
      afficherErreur('nom', 'Le nom du terrain est obligatoire.');
      valide = false;
    } else if (nom.length < 3 || nom.length > 100) {
      afficherErreur('nom', 'Le nom doit contenir entre 3 et 100 caractères.');
      valide = false;
    } else if (!regexNom.test(nom)) {
      afficherErreur('nom', 'Le nom ne doit contenir que des lettres, chiffres, espaces, apostrophes ou tirets.');
      valide = false;
    } else {
      afficherErreur('nom', '');
    }

    // Localisation
    if (document.getElementById('localisation').value === '') {
      afficherErreur('localisation', 'Veuillez choisir une wilaya.');
      valide = false;
    } else {
      afficherErreur('localisation', '');
    }

    // Superficie
    const superficieTexte = document.getElementById('superficie').value.trim().replace(',', '.');
    const superficie = Number(superficieTexte);
    if (superficieTexte === '') {
      afficherErreur('superficie', 'La superficie est obligatoire.');
      valide = false;
    } else if (isNaN(superficie)) {
      afficherErreur('superficie', 'La superficie doit être un nombre.');
      valide = false;
    } else if (superficie <= 0) {
      afficherErreur('superficie', 'La superficie doit être supérieure à 0.');
      valide = false;
    } else if (superficie > 100000) {
      afficherErreur('superficie', 'La superficie ne peut pas dépasser 100 000 ha.');
      valide = false;
    } else {
      afficherErreur('superficie', '');
    }

    // Type de sol
    if (document.getElementById('typeSol').value === '') {
      afficherErreur('typeSol', 'Veuillez choisir un type de sol.');
      valide = false;
    } else {
      afficherErreur('typeSol', '');
    }

    // Une erreur → on bloque l'envoi du formulaire
    if (!valide) {
      event.preventDefault();
    }
  });
});
