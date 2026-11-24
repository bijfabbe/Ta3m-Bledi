<?php
/*
 * Action : supprimer un terrain (Front Office)
 * Fichier COMPLET : à mettre tel quel dans View/FrontOffice/
 * Pas de HTML ici : on supprime puis on redirige.
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/TerrainController.php';

$idUtilisateur = requireLogin();

// Suppression seulement via le bouton (POST), jamais en tapant l'URL
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: mesTerrains.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// deleteTerrain vérifie que le terrain appartient bien à l'utilisateur
if ($id && (new TerrainController())->deleteTerrain($id, $idUtilisateur)) {
    setFlash('Terrain supprimé avec succès.');
} else {
    setFlash("Impossible de supprimer ce terrain.", 'danger');
}

header('Location: mesTerrains.php');
exit;
