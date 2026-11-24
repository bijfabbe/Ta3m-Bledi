<?php
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/BadgeController.php';

requireAdmin();
$utilisateurC = new UtilisateurController();
$badgeC       = new BadgeController();

$erreurs = [];
$valeurs = ['idUtilisateur' => '', 'idBadge' => '', 'motif' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $valeurs = [
        'idUtilisateur' => $_POST['idUtilisateur'] ?? '',
        'idBadge'       => $_POST['idBadge'] ?? '',
        'motif'         => trim($_POST['motif'] ?? ''),
    ];
    $erreurs = $badgeC->validateAttribution($valeurs, $utilisateurC);

    if (empty($erreurs)) {
        $badgeC->attribuer(new Attribution((int) $valeurs['idUtilisateur'], (int) $valeurs['idBadge'], $valeurs['motif']));
        setFlash('Badge attribué avec succès.');
        header('Location: attribuerBadge.php');
        exit;
    }
}

$utilisateurs = $utilisateurC->listUtilisateurs();
$badges       = $badgeC->listBadges();
$attributions = $badgeC->listAttributions();
$flash        = getFlash();
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Taam Bledi - Attribuer un badge</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="css/module-utilisateurs.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-seedling"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Taam Bledi</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-fw fa-chart-pie"></i>
                    <span>Statistiques</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Utilisateurs &amp; Badges</div>

            <li class="nav-item">
                <a class="nav-link" href="listeUtilisateurs.php">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Utilisateurs</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="listeBadges.php">
                    <i class="fas fa-fw fa-award"></i>
                    <span>Badges</span></a>
            </li>

            <li class="nav-item active">
                <a class="nav-link" href="attribuerBadge.php">
                    <i class="fas fa-fw fa-medal"></i>
                    <span>Attribuer un badge</span></a>
            </li>

            <hr class="sidebar-divider">
            <div class="sidebar-heading">Terrains &amp; Récoltes</div>

            <li class="nav-item">
                <a class="nav-link" href="listeTerrains.php">
                    <i class="fas fa-fw fa-map-marked-alt"></i>
                    <span>Terrains</span></a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="listeRecoltes.php">
                    <i class="fas fa-fw fa-seedling"></i>
                    <span>Récoltes</span></a>
            </li>

            <hr class="sidebar-divider">

            <li class="nav-item">
                <a class="nav-link" href="../FrontOffice/index.html">
                    <i class="fas fa-fw fa-globe"></i>
                    <span>Voir le site</span></a>
            </li>

            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bell fa-fw"></i>
                                <!-- Counter - Alerts -->
                                <span class="badge badge-danger badge-counter">3+</span>
                            </a>
                            <!-- Dropdown - Alerts -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="alertsDropdown">
                                <h6 class="dropdown-header">
                                    Alerts Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-primary">
                                            <i class="fas fa-file-alt text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 12, 2019</div>
                                        <span class="font-weight-bold">A new monthly report is ready to download!</span>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-success">
                                            <i class="fas fa-donate text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 7, 2019</div>
                                        $290.29 has been deposited into your account!
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="mr-3">
                                        <div class="icon-circle bg-warning">
                                            <i class="fas fa-exclamation-triangle text-white"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="small text-gray-500">December 2, 2019</div>
                                        Spending Alert: We've noticed unusually high spending for your account.
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Show All Alerts</a>
                            </div>
                        </li>

                        <!-- Nav Item - Messages -->
                        <li class="nav-item dropdown no-arrow mx-1">
                            <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-envelope fa-fw"></i>
                                <!-- Counter - Messages -->
                                <span class="badge badge-danger badge-counter">7</span>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-list dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="messagesDropdown">
                                <h6 class="dropdown-header">
                                    Message Center
                                </h6>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_1.svg"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div class="font-weight-bold">
                                        <div class="text-truncate">Hi there! I am wondering if you can help me with a
                                            problem I've been having.</div>
                                        <div class="small text-gray-500">Emily Fowler · 58m</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_2.svg"
                                            alt="...">
                                        <div class="status-indicator"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">I have the photos that you ordered last month, how
                                            would you like them sent to you?</div>
                                        <div class="small text-gray-500">Jae Chun · 1d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="img/undraw_profile_3.svg"
                                            alt="...">
                                        <div class="status-indicator bg-warning"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Last month's report looks great, I am very happy with
                                            the progress so far, keep up the good work!</div>
                                        <div class="small text-gray-500">Morgan Alvarez · 2d</div>
                                    </div>
                                </a>
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <div class="dropdown-list-image mr-3">
                                        <img class="rounded-circle" src="https://source.unsplash.com/Mv9hjnEUHR4/60x60"
                                            alt="...">
                                        <div class="status-indicator bg-success"></div>
                                    </div>
                                    <div>
                                        <div class="text-truncate">Am I a good boy? The reason I ask is because someone
                                            told me that people say this to all dogs, even if they aren't good...</div>
                                        <div class="small text-gray-500">Chicken the Dog · 2w</div>
                                    </div>
                                </a>
                                <a class="dropdown-item text-center small text-gray-500" href="#">Read More Messages</a>
                            </div>
                        </li>

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= e(($_SESSION['prenom'] ?? '') . ' ' . ($_SESSION['nom'] ?? '')) ?></span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-cogs fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Settings
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-list fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Activity Log
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <h1 class="h3 mb-4 text-gray-800">Attribuer un badge</h1>

                    <?php if ($flash): ?>
                        <div class="alert alert-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
                    <?php endif; ?>

                    <div class="row">

                        <!-- Formulaire d'attribution -->
                        <div class="col-lg-5">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Nouvelle attribution</h6>
                                </div>
                                <div class="card-body">
                                    <form id="formAttribution" action="" method="POST" novalidate>
<?= csrfField() ?>
                                        <div class="form-group">
                                            <label for="idUtilisateur">Utilisateur</label>
                                            <select id="idUtilisateur" name="idUtilisateur" class="form-control <?= isset($erreurs['idUtilisateur']) ? 'is-invalid' : '' ?>">
                                                <option value="">-- Choisir un utilisateur --</option>
                                                <?php foreach ($utilisateurs as $u): ?>
                                                    <option value="<?= e($u['id']) ?>" <?= (string) $valeurs['idUtilisateur'] === (string) $u['id'] ? 'selected' : '' ?>>
                                                        <?= e($u['prenom'] . ' ' . $u['nom']) ?> (<?= e($u['email']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="ub-erreur" id="erreur-idUtilisateur"><?= e($erreurs['idUtilisateur'] ?? '') ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="idBadge">Badge</label>
                                            <select id="idBadge" name="idBadge" class="form-control <?= isset($erreurs['idBadge']) ? 'is-invalid' : '' ?>">
                                                <option value="">-- Choisir un badge --</option>
                                                <?php foreach ($badges as $b): ?>
                                                    <option value="<?= e($b['id']) ?>" <?= (string) $valeurs['idBadge'] === (string) $b['id'] ? 'selected' : '' ?>>
                                                        <?= e($b['nom']) ?> (<?= e($b['points']) ?> pts)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="ub-erreur" id="erreur-idBadge"><?= e($erreurs['idBadge'] ?? '') ?></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="motif">Motif</label>
                                            <textarea class="form-control <?= isset($erreurs['motif']) ? 'is-invalid' : '' ?>"
                                                      id="motif" name="motif" rows="2"><?= e($valeurs['motif']) ?></textarea>
                                            <div class="ub-erreur" id="erreur-motif"><?= e($erreurs['motif'] ?? '') ?></div>
                                        </div>
                                        <button type="submit" class="btn btn-success btn-block">Attribuer</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Historique -->
                        <div class="col-lg-7">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-success">Historique des attributions (<?= count($attributions) ?>)</h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Utilisateur</th>
                                                    <th>Badge</th>
                                                    <th>Date</th>
                                                    <th>Motif</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($attributions)): ?>
                                                    <tr><td colspan="5" class="text-center">Aucune attribution.</td></tr>
                                                <?php endif; ?>
                                                <?php foreach ($attributions as $a): ?>
                                                    <tr>
                                                        <td><?= e($a['utilisateurNom']) ?></td>
                                                        <td><?= e($a['badgeNom']) ?></td>
                                                        <td><?= e(date('d/m/Y', strtotime($a['dateAttribution']))) ?></td>
                                                        <td><?= e($a['motif']) ?></td>
                                                        <td>
                                                            <form action="supprimerAttribution.php" method="POST" class="d-inline"
                                                                  onsubmit="return confirm('Retirer ce badge à cet utilisateur ?');">
<?= csrfField() ?>
                                                                <input type="hidden" name="id" value="<?= e($a['id']) ?>">
                                                                <button type="submit" class="btn btn-danger btn-sm" title="Retirer"><i class="fas fa-trash"></i></button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
                <script src="js/validation-module-utilisateurs.js"></script>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Your Website 2020</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">Cliquez sur « Se déconnecter » pour fermer votre session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="index.php?page=logout">Se déconnecter</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

</body>

</html>