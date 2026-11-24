/*
 * Contrôle de saisie (JavaScript) : formulaire Récolte
 * Mêmes règles que validateRecolte() dans RecolteController.php
 */
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('formRecolte');
  if (!form) return;

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

  // Vraie date au format AAAA-MM-JJ ? (refuse par ex. 2026-02-31)
  function dateValide(texte) {
    if (!/^\d{4}-\d{2}-\d{2}$/.test(texte)) return null;
    const [a, m, j] = texte.split('-').map(Number);
    const d = new Date(a, m - 1, j);
    if (d.getFullYear() !== a || d.getMonth() !== m - 1 || d.getDate() !== j) return null;
    return d;
  }

  form.addEventListener('submit', function (event) {
    let valide = true;

    // Terrain
    if (document.getElementById('idTerrain').value === '') {
      afficherErreur('idTerrain', 'Veuillez choisir un terrain.');
      valide = false;
    } else {
      afficherErreur('idTerrain', '');
    }

    // Culture
    const culture = document.getElementById('culture').value.trim();
    const regexCulture = /^[\p{L}\s'\-]+$/u;
    if (culture === '') {
      afficherErreur('culture', 'La culture est obligatoire.');
      valide = false;
    } else if (culture.length < 2 || culture.length > 100) {
      afficherErreur('culture', 'La culture doit contenir entre 2 et 100 caractères.');
      valide = false;
    } else if (!regexCulture.test(culture)) {
      afficherErreur('culture', 'La culture ne doit contenir que des lettres, espaces, apostrophes ou tirets.');
      valide = false;
    } else {
      afficherErreur('culture', '');
    }

    // Dates
    const aujourdhui = new Date();
    aujourdhui.setHours(0, 0, 0, 0);
    const datePlantation = dateValide(document.getElementById('datePlantation').value);
    const dateRecolte = dateValide(document.getElementById('dateRecolte').value);

    if (!datePlantation) {
      afficherErreur('datePlantation', 'Veuillez saisir une date de plantation valide.');
      valide = false;
    } else if (datePlantation > aujourdhui) {
      afficherErreur('datePlantation', 'La date de plantation ne peut pas être dans le futur.');
      valide = false;
    } else {
      afficherErreur('datePlantation', '');
    }

    if (!dateRecolte) {
      afficherErreur('dateRecolte', 'Veuillez saisir une date de récolte valide.');
      valide = false;
    } else if (datePlantation && dateRecolte <= datePlantation) {
      afficherErreur('dateRecolte', 'La date de récolte doit être après la date de plantation.');
      valide = false;
    } else {
      afficherErreur('dateRecolte', '');
    }

    // Quantité
    const quantiteTexte = document.getElementById('quantite').value.trim().replace(',', '.');
    const quantite = Number(quantiteTexte);
    if (quantiteTexte === '') {
      afficherErreur('quantite', 'La quantité est obligatoire.');
      valide = false;
    } else if (isNaN(quantite)) {
      afficherErreur('quantite', 'La quantité doit être un nombre.');
      valide = false;
    } else if (quantite <= 0) {
      afficherErreur('quantite', 'La quantité doit être supérieure à 0.');
      valide = false;
    } else if (quantite > 1000000) {
      afficherErreur('quantite', 'La quantité ne peut pas dépasser 1 000 000.');
      valide = false;
    } else {
      afficherErreur('quantite', '');
    }

    // Unité
    if (document.getElementById('unite').value === '') {
      afficherErreur('unite', 'Veuillez choisir une unité.');
      valide = false;
    } else {
      afficherErreur('unite', '');
    }

    if (!valide) {
      event.preventDefault();
    }
  });
});
