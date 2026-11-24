<?php
/*
 * Action : supprimer un badge (Back Office, admin)
 * Fichier COMPLET.
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/BadgeController.php';
require_once __DIR__ . '/../../Controller/UploadHelper.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $badgeC = new BadgeController();
    $badge  = $id ? $badgeC->getBadge($id) : false;

    if ($badge && $badgeC->deleteBadge($id)) {
        supprimerImage($badge['icone'], 'badges');
        setFlash('Badge supprimé avec succès.');
    } else {
        setFlash('Impossible de supprimer ce badge.', 'danger');
    }
}

header('Location: listeBadges.php');
exit;
