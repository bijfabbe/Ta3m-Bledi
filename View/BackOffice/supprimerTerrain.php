<?php
/*
 * Action : supprimer un terrain (Back Office, admin)
 * Fichier COMPLET : à mettre tel quel dans View/BackOffice/
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';
require_once __DIR__ . '/../../Controller/TerrainController.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    if ($id && (new TerrainController())->deleteTerrain($id)) {   // admin : pas de filtre propriétaire
        setFlash('Terrain supprimé avec succès.');
    } else {
        setFlash("Impossible de supprimer ce terrain.", 'danger');
    }
}

header('Location: listeTerrains.php');
exit;
