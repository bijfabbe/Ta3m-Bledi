<?php
/*
 * Action : supprimer un utilisateur (Back Office, admin)
 * Fichier COMPLET.
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';
require_once __DIR__ . '/../../Controller/UploadHelper.php';

$idAdmin = requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $utilisateurC = new UtilisateurController();
    $user         = $id ? $utilisateurC->getUtilisateur($id) : false;

    if (!$user) {
        setFlash("Cet utilisateur n'existe pas.", 'danger');
    } elseif ($id === $idAdmin) {
        setFlash('Vous ne pouvez pas supprimer votre propre compte.', 'danger');
    } else {
        $utilisateurC->deleteUtilisateur($id);
        supprimerImage($user['photo'] ?? null, 'profils');
        setFlash('Utilisateur supprimé avec succès.');
    }
}

header('Location: listeUtilisateurs.php');
exit;
