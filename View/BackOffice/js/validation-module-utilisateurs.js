/*
 * Contrôle de saisie (JavaScript) : module Utilisateurs et Badges (Back Office)
 * Mêmes règles que UtilisateurController.php et BadgeController.php.
 * Formulaires : formUtilisateur, formBadge, formAttribution.
 */
document.addEventListener('DOMContentLoaded', function () {

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

  function verifierTexte(id, libelle, min, max, regex, messageRegex) {
    const v = valeur(id);
    if (v === '') return afficherErreur(id, libelle + ' est obligatoire.'), false;
    if (v.length < min || v.length > max) {
      return afficherErreur(id, libelle + ' doit contenir entre ' + min + ' et ' + max + ' caractères.'), false;
    }
    if (regex && !regex.test(v)) return afficherErreur(id, messageRegex), false;
    afficherErreur(id, '');
    return true;
  }

  function verifierChoix(id, message) {
    if (valeur(id) === '') return afficherErreur(id, message), false;
    afficherErreur(id, '');
    return true;
  }

  function brancher(idFormulaire, verification) {
    const form = document.getElementById(idFormulaire);
    if (!form) return;
    form.addEventListener('submit', function (event) {
      if (!verification(form)) event.preventDefault();
    });
  }

  /* ---------- Utilisateur ---------- */
  brancher('formUtilisateur', function (form) {
    const lettres = /^[\p{L}\s'\-]+$/u;
    const r = [
      verifierTexte('nom', 'Le nom', 2, 50, lettres, 'Le nom ne doit contenir que des lettres.'),
      verifierTexte('prenom', 'Le prénom', 2, 50, lettres, 'Le prénom ne doit contenir que des lettres.'),
    ];

    const email = valeur('email');
    if (email === '') { afficherErreur('email', "L'email est obligatoire."); r.push(false); }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) { afficherErreur('email', "L'email n'est pas valide (exemple@mail.com)."); r.push(false); }
    else { afficherErreur('email', ''); }

    const tel = valeur('telephone');
    if (tel === '') { afficherErreur('telephone', 'Le téléphone est obligatoire.'); r.push(false); }
    else if (!/^\d{8}$/.test(tel)) { afficherErreur('telephone', 'Le téléphone doit contenir exactement 8 chiffres.'); r.push(false); }
    else { afficherErreur('telephone', ''); }

    // Mot de passe : obligatoire à l'ajout, facultatif en modification
    const edition = form.dataset.edition === '1';
    const mdp = document.getElementById('motDePasse').value;
    const conf = document.getElementById('confirmation').value;
    if (!edition || mdp !== '' || conf !== '') {
      if (mdp === '') { afficherErreur('motDePasse', 'Le mot de passe est obligatoire.'); r.push(false); }
      else if (mdp.length < 8 || mdp.length > 64) { afficherErreur('motDePasse', 'Le mot de passe doit contenir entre 8 et 64 caractères.'); r.push(false); }
      else if (!/[A-Za-z]/.test(mdp) || !/\d/.test(mdp)) { afficherErreur('motDePasse', 'Le mot de passe doit contenir au moins une lettre et un chiffre.'); r.push(false); }
      else { afficherErreur('motDePasse', ''); }

      if (conf !== mdp) { afficherErreur('confirmation', 'Les deux mots de passe ne correspondent pas.'); r.push(false); }
      else { afficherErreur('confirmation', ''); }
    }

    return r.every(Boolean);
  });

  /* ---------- Badge ---------- */
  brancher('formBadge', function () {
    const r = [
      verifierTexte('nom', 'Le nom du badge', 3, 50, /^[\p{L}0-9\s'\-]+$/u,
        'Le nom ne doit contenir que des lettres, chiffres, espaces, apostrophes ou tirets.'),
      verifierTexte('description', 'La description', 10, 255),
      verifierTexte('critere', "Le critère d'obtention", 3, 100),
    ];

    const points = valeur('points');
    if (points === '') { afficherErreur('points', 'Les points sont obligatoires.'); r.push(false); }
    else if (!/^\d+$/.test(points) || Number(points) < 1 || Number(points) > 1000) {
      afficherErreur('points', 'Les points doivent être un nombre entier entre 1 et 1000.'); r.push(false);
    } else { afficherErreur('points', ''); }

    const icone = document.getElementById('icone');
    if (icone && icone.files.length > 0) {
      const f = icone.files[0];
      if (!['image/jpeg', 'image/png', 'image/webp', 'image/gif'].includes(f.type)) {
        afficherErreur('icone', 'Format accepté : JPG, PNG, WEBP ou GIF.'); r.push(false);
      } else if (f.size > 2 * 1024 * 1024) {
        afficherErreur('icone', "L'image ne doit pas dépasser 2 Mo."); r.push(false);
      } else { afficherErreur('icone', ''); }
    }

    return r.every(Boolean);
  });

  /* ---------- Attribution ---------- */
  brancher('formAttribution', function () {
    const r = [
      verifierChoix('idUtilisateur', 'Veuillez choisir un utilisateur.'),
      verifierChoix('idBadge', 'Veuillez choisir un badge.'),
      verifierTexte('motif', 'Le motif', 3, 255),
    ];
    return r.every(Boolean);
  });
});
