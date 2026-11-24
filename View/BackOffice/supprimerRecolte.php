<?php
/*
 * Action : supprimer une récolte (Back Office, admin)
 * Fichier COMPLET : à mettre tel quel dans View/BackOffice/
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/RecolteController.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id && (new RecolteController())->deleteRecolte($id)) {
        setFlash('Récolte supprimée avec succès.');
    } else {
        setFlash("Impossible de supprimer cette récolte.", 'danger');
    }
}

header('Location: listeRecoltes.php');
exit;
