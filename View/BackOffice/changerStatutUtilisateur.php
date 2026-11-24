<?php
/*
 * Action : bloquer / débloquer un utilisateur (Back Office, admin)
 * Fichier COMPLET.
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/UtilisateurController.php';

$idAdmin = requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $utilisateurC = new UtilisateurController();
    $user         = $id ? $utilisateurC->getUtilisateur($id) : false;

    if (!$user) {
        setFlash("Cet utilisateur n'existe pas.", 'danger');
    } elseif ($id === $idAdmin) {
        setFlash('Vous ne pouvez pas bloquer votre propre compte.', 'danger');
    } else {
        $nouveau = $user['statut'] === 'ACTIF' ? 'BLOQUE' : 'ACTIF';
        $utilisateurC->changerStatut($id, $nouveau);
        setFlash($nouveau === 'BLOQUE' ? 'Utilisateur bloqué.' : 'Utilisateur débloqué.');
    }
}

header('Location: listeUtilisateurs.php');
exit;
