/*
 * Contrôle de saisie (JavaScript) : module Utilisateurs (Front Office)
 * Mêmes règles que UtilisateurController.php (côté serveur).
 * Formulaires : connexion, inscription, profil, mot de passe, mot de passe oublié, réinitialisation.
 */
document.addEventListener('DOMContentLoaded', function () {

  /* ---------- Outils ---------- */

  function valeur(id) {
    const champ = document.getElementById(id);
    return champ ? champ.value.trim() : '';
  }

  function afficherErreur(id, message) {
    const champ = document.getElementById(id);
    const zone = document.getElementById('erreur-' + id);
    if (!champ || !zone) return;
    zone.textContent = message;
    champ.classList.toggle('is-invalid', message !== '');
  }

  // Chaque fonction "verifier..." retourne true si le champ est correct
  function verifierNomPrenom(id, libelle) {
    const v = valeur(id);
    if (v === '') return afficherErreur(id, libelle + ' est obligatoire.'), false;
    if (v.length < 2 || v.length > 50) return afficherErreur(id, libelle + ' doit contenir entre 2 et 50 caractères.'), false;
    if (!/^[\p{L}\s'\-]+$/u.test(v)) return afficherErreur(id, libelle + ' ne doit contenir que des lettres.'), false;
    afficherErreur(id, '');
    return true;
  }

  function verifierEmail(id) {
    const v = valeur(id);
    if (v === '') return afficherErreur(id, "L'email est obligatoire."), false;
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v) || v.length > 100) {
      return afficherErreur(id, "L'email n'est pas valide (exemple@mail.com)."), false;
    }
    afficherErreur(id, '');
    return true;
  }

  function verifierTelephone(id) {
    const v = valeur(id);
    if (v === '') return afficherErreur(id, 'Le téléphone est obligatoire.'), false;
    if (!/^\d{8}$/.test(v)) return afficherErreur(id, 'Le téléphone doit contenir exactement 8 chiffres.'), false;
    afficherErreur(id, '');
    return true;
  }

  function verifierNonVide(id, message) {
    if (document.getElementById(id).value === '') return afficherErreur(id, message), false;
    afficherErreur(id, '');
    return true;
  }

  // Mot de passe : 8 à 64 caractères, au moins une lettre et un chiffre, + confirmation identique
  function verifierMotDePasse(idMdp, idConfirmation) {
    let ok = true;
    const mdp = document.getElementById(idMdp).value;
    const conf = document.getElementById(idConfirmation).value;

    if (mdp === '') {
      afficherErreur(idMdp, 'Le mot de passe est obligatoire.'); ok = false;
    } else if (mdp.length < 8 || mdp.length > 64) {
      afficherErreur(idMdp, 'Le mot de passe doit contenir entre 8 et 64 caractères.'); ok = false;
    } else if (!/[A-Za-z]/.test(mdp) || !/\d/.test(mdp)) {
      afficherErreur(idMdp, 'Le mot de passe doit contenir au moins une lettre et un chiffre.'); ok = false;
    } else {
      afficherErreur(idMdp, '');
    }

    if (conf !== mdp) {
      afficherErreur(idConfirmation, 'Les deux mots de passe ne correspondent pas.'); ok = false;
    } else {
      afficherErreur(idConfirmation, '');
    }
    return ok;
  }

  function verifierPhoto(id) {
    const champ = document.getElementById(id);
    if (!champ || champ.files.length === 0) { afficherErreur(id, ''); return true; }
    const fichier = champ.files[0];
    if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(fichier.type)) {
      return afficherErreur(id, 'Format accepté : JPG, PNG, WEBP ou GIF.'), false;
    }
    if (fichier.size > 2 * 1024 * 1024) return afficherErreur(id, "L'image ne doit pas dépasser 2 Mo."), false;
    afficherErreur(id, '');
    return true;
  }

  // Branche une fonction de vérification sur un formulaire (bloque l'envoi si erreur)
  function brancher(idFormulaire, verification) {
    const form = document.getElementById(idFormulaire);
    if (!form) return;
    form.addEventListener('submit', function (event) {
      if (!verification()) event.preventDefault();
    });
  }

  /* ---------- Formulaires ---------- */

  brancher('formConnexion', function () {
    const a = verifierEmail('email');
    const b = verifierNonVide('motDePasse', 'Le mot de passe est obligatoire.');
    return a && b;
  });

  brancher('formInscription', function () {
    const resultats = [
      verifierNomPrenom('nom', 'Le nom'),
      verifierNomPrenom('prenom', 'Le prénom'),
      verifierEmail('email'),
      verifierTelephone('telephone'),
      verifierMotDePasse('motDePasse', 'confirmation'),
    ];
    return resultats.every(Boolean);
  });

  brancher('formProfil', function () {
    const resultats = [
      verifierNomPrenom('nom', 'Le nom'),
      verifierNomPrenom('prenom', 'Le prénom'),
      verifierEmail('email'),
      verifierTelephone('telephone'),
      verifierPhoto('photo'),
    ];
    return resultats.every(Boolean);
  });

  brancher('formMotDePasse', function () {
    const a = verifierNonVide('ancienMdp', "L'ancien mot de passe est obligatoire.");
    const b = verifierMotDePasse('nouveauMdp', 'confirmMdp');
    return a && b;
  });

  brancher('formMotDePasseOublie', function () {
    return verifierEmail('email');
  });

  brancher('formReinitialiser', function () {
    return verifierMotDePasse('nouveauMdp', 'confirmMdp');
  });
});
