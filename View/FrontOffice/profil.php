<?php
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/UploadHelper.php';

$idUtilisateur = requireLogin();
$utilisateurC  = new UtilisateurController();
$user          = $utilisateurC->getUtilisateur($idUtilisateur);

if (!$user) {               // compte supprimé entre-temps
    header('Location: deconnexion.php');
    exit;
}

$erreurs    = [];   // formulaire "informations"
$erreursMdp = [];   // formulaire "mot de passe"
$valeurs    = [
    'nom'       => $user['nom'],
    'prenom'    => $user['prenom'],
    'email'     => $user['email'],
    'telephone' => $user['telephone'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    /* ---------- 1) Modifier mes informations ---------- */
    if ($action === 'infos') {
        $valeurs = [
            'nom'       => trim($_POST['nom'] ?? ''),
            'prenom'    => trim($_POST['prenom'] ?? ''),
            'email'     => trim($_POST['email'] ?? ''),
            'telephone' => trim($_POST['telephone'] ?? ''),
        ];
        $erreurs = $utilisateurC->validateInfos($valeurs, $idUtilisateur);

        // Photo (facultative)
        $nouvellePhoto = null;
        if (empty($erreurs) && fichierEnvoye($_FILES['photo'] ?? null)) {
            $upload = uploadImage($_FILES['photo'], 'profils');
            if ($upload['ok']) {
                $nouvellePhoto = $upload['fichier'];
            } else {
                $erreurs['photo'] = $upload['erreur'];
            }
        }

        if (empty($erreurs)) {
            $utilisateurC->updateUtilisateur(new Utilisateur(
                $valeurs['nom'], $valeurs['prenom'], $valeurs['email'], $valeurs['telephone'],
                '', $user['role'], $user['statut'], null, $idUtilisateur   // rôle et statut inchangés
            ));
            if ($nouvellePhoto) {
                supprimerImage($user['photo'], 'profils');
                $utilisateurC->updatePhoto($idUtilisateur, $nouvellePhoto);
            }
            $_SESSION['nom']    = $valeurs['nom'];
            $_SESSION['prenom'] = $valeurs['prenom'];
            setFlash('Profil mis à jour avec succès.');
            header('Location: profil.php');
            exit;
        }
    }

    /* ---------- 2) Changer mon mot de passe ---------- */
    if ($action === 'motdepasse') {
        $ancien = $_POST['ancienMdp'] ?? '';
        if ($ancien === '') {
            $erreursMdp['ancienMdp'] = "L'ancien mot de passe est obligatoire.";
        } elseif (!password_verify($ancien, $user['motDePasse'])) {
            $erreursMdp['ancienMdp'] = "L'ancien mot de passe est incorrect.";
        }
        $erreursMdp += $utilisateurC->validateMotDePasse(
            $_POST['nouveauMdp'] ?? '', $_POST['confirmMdp'] ?? '', 'nouveauMdp', 'confirmMdp'
        );

        if (empty($erreursMdp)) {
            $utilisateurC->updateMotDePasse($idUtilisateur, $_POST['nouveauMdp']);
            setFlash('Mot de passe modifié avec succès.');
            header('Location: profil.php');
            exit;
        }
    }
}

$flash = getFlash();
$initiales = mb_strtoupper(mb_substr($user['prenom'], 0, 1) . mb_substr($user['nom'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Taam Bledi - Mon profil</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&family=Marcellus:wght@400&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">
  <link href="assets/css/utilisateurs.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: AgriCulture
  * Template URL: https://bootstrapmade.com/agriculture-bootstrap-website-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="about-page">

  <header id="header" class="header d-flex align-items-center position-relative">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="assets/img/logo.png" alt="Taam Bledi">
        <!-- <h1 class="sitename">Taam Bledi</h1>  -->
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html">Home</a></li>
          <li><a href="about.html">About Us</a></li>
          <li><a href="services.html">Our Services</a></li>
          <li><a href="testimonials.html">Testimonials</a></li>
          <li><a href="blog.html">Blog</a></li>
          <li class="dropdown"><a href="#"><span>Terrains &amp; Récoltes</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="mesTerrains.php">Mes terrains</a></li>
              <li><a href="mesRecoltes.php">Mes récoltes</a></li>
              <li><a href="recommandation.php">Recommandation IA</a></li>
              <li><a href="consommationEau.php">Consommation d'eau</a></li>
            </ul>
          </li>
          <li class="dropdown"><a href="#"><span>Mon compte</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="profil.php" class="active">Mon profil</a></li>
              <li><a href="mesBadges.php">Mes badges</a></li>
              <li><a href="connexion.php">Connexion</a></li>
              <li><a href="inscription.php">Inscription</a></li>
              <li><a href="deconnexion.php">Déconnexion</a></li>
            </ul>
          </li>
          <li><a href="contact.html">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">
    <section class="ub-page">
      <div class="container">

        <div class="ub-hero">
          <span class="ub-hero-label">Espace utilisateur</span>
          <h1>Mon profil</h1>
        </div>

        <div class="ub-card ub-card-large">
          <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
          <?php endif; ?>

          <div class="row">
            <!-- Colonne gauche : photo -->
            <div class="col-md-4 text-center mb-4">
              <?php if (!empty($user['photo'])): ?>
                <img src="assets/img/uploads/profils/<?= e($user['photo']) ?>" alt="Photo de profil" class="ub-profil-photo mb-3">
              <?php else: ?>
                <div class="ub-avatar mb-3"><?= e($initiales) ?></div>
              <?php endif; ?>
              <h3><?= e($user['prenom'] . ' ' . $user['nom']) ?></h3>
              <p class="text-muted"><?= e(UtilisateurController::ROLES[$user['role']] ?? $user['role']) ?></p>
              <a href="mesBadges.php" class="btn btn-outline-success">Voir mes badges</a>
              <p class="mt-3 mb-0"><a href="deconnexion.php">Se déconnecter</a></p>
            </div>

            <!-- Colonne droite : formulaires -->
            <div class="col-md-8">
              <h4>Modifier mes informations</h4>
              <form id="formProfil" action="" method="POST" enctype="multipart/form-data" novalidate>
<?= csrfField() ?>
                <input type="hidden" name="action" value="infos">
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control <?= isset($erreurs['nom']) ? 'is-invalid' : '' ?>"
                           id="nom" name="nom" value="<?= e($valeurs['nom']) ?>">
                    <div class="ub-erreur" id="erreur-nom"><?= e($erreurs['nom'] ?? '') ?></div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control <?= isset($erreurs['prenom']) ? 'is-invalid' : '' ?>"
                           id="prenom" name="prenom" value="<?= e($valeurs['prenom']) ?>">
                    <div class="ub-erreur" id="erreur-prenom"><?= e($erreurs['prenom'] ?? '') ?></div>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="text" class="form-control <?= isset($erreurs['email']) ? 'is-invalid' : '' ?>"
                         id="email" name="email" value="<?= e($valeurs['email']) ?>">
                  <div class="ub-erreur" id="erreur-email"><?= e($erreurs['email'] ?? '') ?></div>
                </div>
                <div class="mb-3">
                  <label for="telephone" class="form-label">Téléphone</label>
                  <input type="text" class="form-control <?= isset($erreurs['telephone']) ? 'is-invalid' : '' ?>"
                         id="telephone" name="telephone" value="<?= e($valeurs['telephone']) ?>">
                  <div class="ub-erreur" id="erreur-telephone"><?= e($erreurs['telephone'] ?? '') ?></div>
                </div>
                <div class="mb-3">
                  <label for="photo" class="form-label">Photo de profil (facultatif)</label>
                  <input type="file" class="form-control <?= isset($erreurs['photo']) ? 'is-invalid' : '' ?>"
                         id="photo" name="photo">
                  <small class="text-muted">JPG, PNG, WEBP ou GIF, 2 Mo maximum.</small>
                  <div class="ub-erreur" id="erreur-photo"><?= e($erreurs['photo'] ?? '') ?></div>
                </div>
                <button type="submit" class="ub-btn">Enregistrer</button>
              </form>

              <hr class="my-4">

              <h4>Changer mon mot de passe</h4>
              <form id="formMotDePasse" action="" method="POST" novalidate>
<?= csrfField() ?>
                <input type="hidden" name="action" value="motdepasse">
                <div class="mb-3">
                  <label for="ancienMdp" class="form-label">Ancien mot de passe</label>
                  <input type="password" class="form-control <?= isset($erreursMdp['ancienMdp']) ? 'is-invalid' : '' ?>"
                         id="ancienMdp" name="ancienMdp">
                  <div class="ub-erreur" id="erreur-ancienMdp"><?= e($erreursMdp['ancienMdp'] ?? '') ?></div>
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label for="nouveauMdp" class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control <?= isset($erreursMdp['nouveauMdp']) ? 'is-invalid' : '' ?>"
                           id="nouveauMdp" name="nouveauMdp">
                    <div class="ub-erreur" id="erreur-nouveauMdp"><?= e($erreursMdp['nouveauMdp'] ?? '') ?></div>
                  </div>
                  <div class="col-md-6 mb-3">
                    <label for="confirmMdp" class="form-label">Confirmer</label>
                    <input type="password" class="form-control <?= isset($erreursMdp['confirmMdp']) ? 'is-invalid' : '' ?>"
                           id="confirmMdp" name="confirmMdp">
                    <div class="ub-erreur" id="erreur-confirmMdp"><?= e($erreursMdp['confirmMdp'] ?? '') ?></div>
                  </div>
                </div>
                <button type="submit" class="ub-btn">Changer le mot de passe</button>
              </form>
            </div>
          </div>
        </div>

      </div>
    </section>
    <script src="assets/js/validation-utilisateurs.js"></script>
  </main>

  <footer id="footer" class="footer dark-background">

    <div class="footer-top">
      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-4 col-md-6 footer-about">
            <a href="index.html" class="logo d-flex align-items-center">
              <span class="sitename">Taam Bledi</span>
            </a>
            <div class="footer-contact pt-3">
              <p>A108 Adam Street</p>
              <p>New York, NY 535022</p>
              <p class="mt-3"><strong>Phone:</strong> <span>+1 5589 55488 55</span></p>
              <p><strong>Email:</strong> <span>info@example.com</span></p>
            </div>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Useful Links</h4>
            <ul>
              <li><a href="#">Home</a></li>
              <li><a href="#">About us</a></li>
              <li><a href="#">Services</a></li>
              <li><a href="#">Terms of service</a></li>
              <li><a href="#">Privacy policy</a></li>
            </ul>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Our Services</h4>
            <ul>
              <li><a href="#">Web Design</a></li>
              <li><a href="#">Web Development</a></li>
              <li><a href="#">Product Management</a></li>
              <li><a href="#">Marketing</a></li>
              <li><a href="#">Graphic Design</a></li>
            </ul>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Hic solutasetp</h4>
            <ul>
              <li><a href="#">Molestiae accusamus iure</a></li>
              <li><a href="#">Excepturi dignissimos</a></li>
              <li><a href="#">Suscipit distinctio</a></li>
              <li><a href="#">Dilecta</a></li>
              <li><a href="#">Sit quas consectetur</a></li>
            </ul>
          </div>

          <div class="col-lg-2 col-md-3 footer-links">
            <h4>Nobis illum</h4>
            <ul>
              <li><a href="#">Ipsam</a></li>
              <li><a href="#">Laudantium dolorum</a></li>
              <li><a href="#">Dinera</a></li>
              <li><a href="#">Trodelas</a></li>
              <li><a href="#">Flexo</a></li>
            </ul>
          </div>

        </div>
      </div>
    </div>

    <div class="copyright text-center">
      <div class="container d-flex flex-column flex-lg-row justify-content-center justify-content-lg-between align-items-center">

        <div class="d-flex flex-column align-items-center align-items-lg-start">
          <div>
            © Copyright <strong><span>MyWebsite</span></strong>. All Rights Reserved
          </div>
          <div class="credits">
            <!-- All the links in the footer should remain intact. -->
            <!-- You can delete the links only if you purchased the pro version. -->
            <!-- Licensing information: https://bootstrapmade.com/license/ -->
            <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/herobiz-bootstrap-business-template/ -->
            Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a> | <a href="https://bootstrapmade.com/tools/">DevTools</a>
          </div>
        </div>

        <div class="social-links order-first order-lg-last mb-3 mb-lg-0">
          <a href=""><i class="bi bi-twitter-x"></i></a>
          <a href=""><i class="bi bi-facebook"></i></a>
          <a href=""><i class="bi bi-instagram"></i></a>
          <a href=""><i class="bi bi-linkedin"></i></a>
        </div>

      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>