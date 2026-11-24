<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Connexion — Taam Bledi PHP</title>
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link href="assets/css/sb-admin-2.css" rel="stylesheet">
</head>
<body class="bg-gradient-primary" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem;">

<div class="card" style="max-width:860px;width:100%;overflow:hidden;">
  <div class="grid-2" style="gap:0;">
    <div style="background:#224abe;color:#fff;padding:2rem;">
      <h2 style="margin-top:0;">Taam Bledi · Backoffice</h2>
      <p style="font-size:0.9rem;opacity:0.9;">
        Connectez-vous en tant qu’<strong>Administrateur</strong> ou <strong>Agriculteur</strong>
        pour gérer les variétés (CRUD) et consulter les témoignages.
      </p>
      <p style="font-size:0.8rem;opacity:0.75;margin-top:2rem;">
        Les comptes « Simple utilisateur » n’ont pas accès au backoffice.
      </p>
    </div>

    <div style="padding:2rem;">
      <?php if (!empty($_SESSION['session_expired'])): unset($_SESSION['session_expired']); ?>
        <div class="alert alert-warning">Votre session a expiré après 20 minutes d'inactivité. Veuillez vous reconnecter.</div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= e($error) ?></div>
      <?php endif; ?>
      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
      <?php endif; ?>

      <?php if ($needsAdmin): ?>
        <h3>1. Créer le 1er Administrateur</h3>
        <form method="post" action="index.php?page=login&action=setupAdmin" style="display:grid;gap:0.6rem;margin-bottom:1.5rem;">
          <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
          <input class="form-control" type="text" name="name" placeholder="Nom Admin" required>
          <input class="form-control" type="email" name="email" placeholder="admin@taambledi.tn" required>
          <input class="form-control" type="password" name="password" placeholder="Mot de passe (8+ caractères, lettre + chiffre)" required>
          <button class="btn btn-success" type="submit">Créer le compte Admin</button>
        </form>
      <?php endif; ?>

      <h3>Connexion (Admin / Agriculteur)</h3>
      <form method="post" action="index.php?page=login&action=login" style="display:grid;gap:0.6rem;margin-bottom:1.5rem;">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input class="form-control" type="email" name="email" placeholder="Adresse e-mail" required>
        <input class="form-control" type="password" name="password" placeholder="Mot de passe" required>
        <button class="btn btn-primary" type="submit">Se connecter</button>
      </form>

    </div>
  </div>
</div>

</body>
</html>
