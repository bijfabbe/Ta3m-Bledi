<?php
/*
 * Action : déconnexion (Front et Back Office)
 * Vide la session puis renvoie vers la page de connexion.
 */
require_once __DIR__ . '/../../Controller/AuthGuard.php';

destroyTa3mSession();
header('Location: connexion.php');
exit;
