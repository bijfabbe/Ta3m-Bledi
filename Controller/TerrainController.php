<?php
/*
 * Controller : Terrain
 * Toutes les requêtes SQL du module Terrain (requêtes préparées PDO).
 */
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Terrain.php';

class TerrainController
{
    public const WILAYAS = [
        'Ariana', 'Béja', 'Ben Arous', 'Bizerte', 'Gabès', 'Gafsa', 'Jendouba', 'Kairouan',
        'Kasserine', 'Kébili', 'Le Kef', 'Mahdia', 'La Manouba', 'Médenine', 'Monastir', 'Nabeul',
        'Sfax', 'Sidi Bouzid', 'Siliana', 'Sousse', 'Tataouine', 'Tozeur', 'Tunis', 'Zaghouan',
    ];

    public const TYPES_SOL = ['Argileux', 'Sableux', 'Calcaire', 'Limoneux', 'Humifère'];

    /* =========================================================
     *  LECTURE
     * ========================================================= */

    // Front : les terrains d'un seul utilisateur (+ nombre de récoltes)
    public function listTerrainsByUser(int $idUtilisateur): array
    {
        $sql = "SELECT t.*,
                       (SELECT COUNT(*) FROM recolte r WHERE r.idTerrain = t.id) AS nbRecoltes
                FROM terrain t
                WHERE t.idUtilisateur = :idU
                ORDER BY t.id DESC";
        $req = config::getConnexion()->prepare($sql);
        $req->execute(['idU' => $idUtilisateur]);
        return $req->fetchAll();
    }

    // Back : tous les terrains (+ propriétaire), avec recherche optionnelle
    public function listAllTerrains(string $recherche = ''): array
    {
        $sql = "SELECT t.*,
                       CONCAT(u.prenom, ' ', u.nom) AS proprietaire,
                       (SELECT COUNT(*) FROM recolte r WHERE r.idTerrain = t.id) AS nbRecoltes
                FROM terrain t
                LEFT JOIN utilisateur u ON u.id = t.idUtilisateur";
        $params = [];
        if ($recherche !== '') {
            $sql .= " WHERE t.nom LIKE :q1 OR t.localisation LIKE :q2 OR u.nom LIKE :q3 OR u.prenom LIKE :q4";
            $like = '%' . $recherche . '%';
            $params = ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like];
        }
        $sql .= " ORDER BY t.id DESC";
        $req = config::getConnexion()->prepare($sql);
        $req->execute($params);
        return $req->fetchAll();
    }

    // Un terrain SEULEMENT s'il appartient à cet utilisateur (sinon false)
    public function getTerrainOfUser(int $id, int $idUtilisateur)
    {
        $sql = "SELECT * FROM terrain WHERE id = :id AND idUtilisateur = :idU";
        $req = config::getConnexion()->prepare($sql);
        $req->execute(['id' => $id, 'idU' => $idUtilisateur]);
        return $req->fetch();
    }

    /* =========================================================
     *  AJOUT / MODIFICATION / SUPPRESSION
     * ========================================================= */

    public function addTerrain(Terrain $terrain): void
    {
        $sql = "INSERT INTO terrain (nom, localisation, superficie, typeSol, idUtilisateur)
                VALUES (:nom, :localisation, :superficie, :typeSol, :idU)";
        $req = config::getConnexion()->prepare($sql);
        $req->execute([
            'nom'          => $terrain->getNom(),
            'localisation' => $terrain->getLocalisation(),
            'superficie'   => $terrain->getSuperficie(),
            'typeSol'      => $terrain->getTypeSol(),
            'idU'          => $terrain->getIdUtilisateur(),
        ]);
    }

    // Modifie seulement si le terrain appartient à l'utilisateur
    public function updateTerrain(Terrain $terrain): void
    {
        $sql = "UPDATE terrain
                SET nom = :nom, localisation = :localisation, superficie = :superficie, typeSol = :typeSol
                WHERE id = :id AND idUtilisateur = :idU";
        $req = config::getConnexion()->prepare($sql);
        $req->execute([
            'nom'          => $terrain->getNom(),
            'localisation' => $terrain->getLocalisation(),
            'superficie'   => $terrain->getSuperficie(),
            'typeSol'      => $terrain->getTypeSol(),
            'id'           => $terrain->getId(),
            'idU'          => $terrain->getIdUtilisateur(),
        ]);
    }

    // Front : $idUtilisateur obligatoire (il ne supprime que les siens)
    // Back (admin) : $idUtilisateur = null → supprime n'importe quel terrain
    // Les récoltes du terrain sont supprimées automatiquement (ON DELETE CASCADE)
    public function deleteTerrain(int $id, ?int $idUtilisateur = null): bool
    {
        if ($idUtilisateur === null) {
            $req = config::getConnexion()->prepare("DELETE FROM terrain WHERE id = :id");
            $req->execute(['id' => $id]);
        } else {
            $req = config::getConnexion()->prepare("DELETE FROM terrain WHERE id = :id AND idUtilisateur = :idU");
            $req->execute(['id' => $id, 'idU' => $idUtilisateur]);
        }
        return $req->rowCount() > 0;
    }

    /* =========================================================
     *  CONTRÔLE DE SAISIE (côté serveur)
     *  Retourne un tableau d'erreurs : ['champ' => 'message']
     *  Tableau vide = tout est correct.
     * ========================================================= */

    public function validateTerrain(array $data): array
    {
        $erreurs = [];

        // Nom : obligatoire, 3 à 100 caractères, lettres / chiffres / espaces / ' -
        $nom = trim($data['nom'] ?? '');
        if ($nom === '') {
            $erreurs['nom'] = 'Le nom du terrain est obligatoire.';
        } elseif (mb_strlen($nom) < 3 || mb_strlen($nom) > 100) {
            $erreurs['nom'] = 'Le nom doit contenir entre 3 et 100 caractères.';
        } elseif (!preg_match("/^[\p{L}0-9\s'\-]+$/u", $nom)) {
            $erreurs['nom'] = 'Le nom ne doit contenir que des lettres, chiffres, espaces, apostrophes ou tirets.';
        }

        // Localisation : une des 24 wilayas
        if (!in_array($data['localisation'] ?? '', self::WILAYAS, true)) {
            $erreurs['localisation'] = 'Veuillez choisir une wilaya.';
        }

        // Superficie : nombre > 0 et raisonnable
        $superficie = str_replace(',', '.', trim($data['superficie'] ?? ''));
        if ($superficie === '') {
            $erreurs['superficie'] = 'La superficie est obligatoire.';
        } elseif (!is_numeric($superficie)) {
            $erreurs['superficie'] = 'La superficie doit être un nombre.';
        } elseif ((float) $superficie <= 0) {
            $erreurs['superficie'] = 'La superficie doit être supérieure à 0.';
        } elseif ((float) $superficie > 100000) {
            $erreurs['superficie'] = 'La superficie ne peut pas dépasser 100 000 ha.';
        }

        // Type de sol : une valeur de la liste
        if (!in_array($data['typeSol'] ?? '', self::TYPES_SOL, true)) {
            $erreurs['typeSol'] = 'Veuillez choisir un type de sol.';
        }

        return $erreurs;
    }

    /* =========================================================
     *  STATISTIQUES (Back Office)
     * ========================================================= */

    public function countTerrains(): int
    {
        return (int) config::getConnexion()->query("SELECT COUNT(*) FROM terrain")->fetchColumn();
    }

    public function sumSuperficie(): float
    {
        return (float) config::getConnexion()->query("SELECT COALESCE(SUM(superficie), 0) FROM terrain")->fetchColumn();
    }

    public function countAgriculteurs(): int
    {
        return (int) config::getConnexion()->query("SELECT COUNT(DISTINCT idUtilisateur) FROM terrain")->fetchColumn();
    }

    public function terrainsParWilaya(): array
    {
        $sql = "SELECT localisation, COUNT(*) AS nb, SUM(superficie) AS superficie
                FROM terrain
                GROUP BY localisation
                ORDER BY nb DESC, localisation";
        return config::getConnexion()->query($sql)->fetchAll();
    }
}
