<?php
/*
 * Action : supprimer une récolte (Front Office)
 * Fichier COMPLET : à mettre tel quel dans View/FrontOffice/
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/RecolteController.php';

$idUtilisateur = requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mesRecoltes.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// deleteRecolte vérifie que la récolte est sur un terrain de l'utilisateur
if ($id && (new RecolteController())->deleteRecolte($id, $idUtilisateur)) {
    setFlash('Récolte supprimée avec succès.');
} else {
    setFlash("Impossible de supprimer cette récolte.", 'danger');
}

// Retour : page "Détails terrain" si on venait de là, sinon "Mes récoltes"
// (on accepte seulement ces deux pages, pour éviter les redirections piégées)
$retour = $_POST['retour'] ?? '';
if (!preg_match('/^detailsTerrain\.php\?id=\d+$/', $retour)) {
    $retour = 'mesRecoltes.php';
}

header('Location: ' . $retour);
exit;
