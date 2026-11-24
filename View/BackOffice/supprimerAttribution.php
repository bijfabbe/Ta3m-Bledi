<?php
/*
 * Action : retirer un badge à un utilisateur (Back Office, admin)
 * Fichier COMPLET.
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/BadgeController.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id && (new BadgeController())->deleteAttribution($id)) {
        setFlash('Badge retiré avec succès.');
    } else {
        setFlash("Impossible de retirer ce badge.", 'danger');
    }
}

header('Location: attribuerBadge.php');
exit;
