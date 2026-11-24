<?php
/* =====================================================================
 *  recommandation.php ET consommationEau.php
 *  PARTIE 1 SEULEMENT : à coller TOUT EN HAUT de chacune des deux pages
 *  (ligne 1, AVANT <!DOCTYPE html>). Le reste de la page ne change pas.
 *  → protège la page : pas connecté = redirection vers la connexion
 * ===================================================================== */
require_once __DIR__ . '/../../Controller/AuthGuard.php';

$idUtilisateur = requireLogin();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Taam Bledi</title>
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

  <!-- =======================================================
  * Template Name: Taam Bledi
  * Template URL: https://bootstrapmade.com/agriculture-bootstrap-website-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

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
              <li><a href="consommationEau.php" class="active">Consommation d'eau</a></li>
            </ul>
          </li>
          <li class="dropdown"><a href="#"><span>Mon compte</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="profil.php">Mon profil</a></li>
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
    <section class="container my-5">
      <h2 class="mb-2">Consommation d'eau</h2>
      <p class="text-muted mb-4">Suivi de l'eau utilisée pour chaque terrain et culture.</p>

      <!-- Filtre -->
      <form action="" method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
          <label for="idTerrain" class="form-label">Terrain</label>
          <select class="form-select" id="idTerrain" name="idTerrain">
            <option value="">Tous les terrains</option>
            <option value="1">Terrain Zaghouan</option>
            <option value="2">Terrain Béja</option>
            <option value="3">Terrain Nabeul</option>
          </select>
        </div>
        <div class="col-md-4">
          <label for="mois" class="form-label">Mois</label>
          <input type="month" class="form-control" id="mois" name="mois" value="2026-11">
        </div>
        <div class="col-md-3 d-flex align-items-end">
          <button type="submit" class="btn btn-success w-100">Afficher</button>
        </div>
      </form>

      <!-- Cartes résumé -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card text-center p-3">
            <i class="bi bi-droplet fs-2 text-primary"></i>
            <h3 class="mt-2 mb-0">1 250 m³</h3>
            <p class="text-muted mb-0">Total ce mois</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center p-3">
            <i class="bi bi-geo-alt fs-2 text-success"></i>
            <h3 class="mt-2 mb-0">3</h3>
            <p class="text-muted mb-0">Terrains suivis</p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card text-center p-3">
            <i class="bi bi-graph-up fs-2 text-warning"></i>
            <h3 class="mt-2 mb-0">Terrain Nabeul</h3>
            <p class="text-muted mb-0">Plus grande consommation</p>
          </div>
        </div>
      </div>
    </section>

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