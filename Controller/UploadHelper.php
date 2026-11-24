<?php
/*
 * Upload d'images sécurisé (photo de profil, icône de badge)
 * - vérifie la taille (2 Mo max) et le VRAI type du fichier (pas seulement l'extension)
 * - accepte seulement JPG, PNG, WEBP, GIF
 * - donne un nom aléatoire au fichier (impossible d'écraser un autre fichier)
 */

const UPLOAD_DOSSIER = __DIR__ . '/../View/FrontOffice/assets/img/uploads/';

/**
 * Retourne ['ok' => true, 'fichier' => 'nom.png'] ou ['ok' => false, 'erreur' => 'message'].
 * $sousDossier : 'profils' ou 'badges'
 */
function uploadImage(array $fichier, string $sousDossier): array
{
    if (($fichier['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'erreur' => "Erreur pendant l'envoi de l'image."];
    }
    if ($fichier['size'] > 2 * 1024 * 1024) {
        return ['ok' => false, 'erreur' => "L'image ne doit pas dépasser 2 Mo."];
    }

    $typesAutorises = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    $typeReel = (new finfo(FILEINFO_MIME_TYPE))->file($fichier['tmp_name']);
    if (!isset($typesAutorises[$typeReel]) || @getimagesize($fichier['tmp_name']) === false) {
        return ['ok' => false, 'erreur' => 'Format accepté : JPG, PNG, WEBP ou GIF.'];
    }

    $dossier = UPLOAD_DOSSIER . $sousDossier . '/';
    if (!is_dir($dossier)) {
        mkdir($dossier, 0775, true);
    }

    $nom = bin2hex(random_bytes(8)) . '.' . $typesAutorises[$typeReel];
    if (!move_uploaded_file($fichier['tmp_name'], $dossier . $nom)) {
        return ['ok' => false, 'erreur' => "Impossible d'enregistrer l'image."];
    }
    return ['ok' => true, 'fichier' => $nom];
}

/** Un fichier a-t-il été choisi dans le champ ? */
function fichierEnvoye(?array $fichier): bool
{
    return $fichier !== null && ($fichier['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
}

/** Supprime une ancienne image (nom de fichier simple uniquement) */
function supprimerImage(?string $nomFichier, string $sousDossier): void
{
    if ($nomFichier && preg_match('/^[a-f0-9]{16}\.(jpg|png|webp|gif)$/', $nomFichier)) {
        $chemin = UPLOAD_DOSSIER . $sousDossier . '/' . $nomFichier;
        if (is_file($chemin)) {
            unlink($chemin);
        }
    }
}
