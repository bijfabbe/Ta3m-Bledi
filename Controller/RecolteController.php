<?php
/*
 * Controller : Recolte
 * Toutes les requêtes SQL du module Récolte (requêtes préparées PDO).
 */
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Recolte.php';
require_once __DIR__ . '/TerrainController.php';

class RecolteController
{
    public const UNITES = ['kg', 'quintal', 'tonne'];

    /* =========================================================
     *  LECTURE
     * ========================================================= */

    // Front : toutes les récoltes des terrains d'un utilisateur
    public function listRecoltesByUser(int $idUtilisateur): array
    {
        $sql = "SELECT r.*, t.nom AS terrainNom
                FROM recolte r
                JOIN terrain t ON t.id = r.idTerrain
                WHERE t.idUtilisateur = :idU
                ORDER BY r.dateRecolte DESC";
        $req = config::getConnexion()->prepare($sql);
        $req->execute(['idU' => $idUtilisateur]);
        return $req->fetchAll();
    }

    // Les récoltes d'un terrain (page Détails terrain)
    public function listRecoltesByTerrain(int $idTerrain): array
    {
        $sql = "SELECT * FROM recolte WHERE idTerrain = :idT ORDER BY dateRecolte DESC";
        $req = config::getConnexion()->prepare($sql);
        $req->execute(['idT' => $idTerrain]);
        return $req->fetchAll();
    }

    // Back : toutes les récoltes (+ terrain + propriétaire), avec recherche optionnelle
    public function listAllRecoltes(string $recherche = ''): array
    {
        $sql = "SELECT r.*, t.nom AS terrainNom, CONCAT(u.prenom, ' ', u.nom) AS proprietaire
                FROM recolte r
                JOIN terrain t ON t.id = r.idTerrain
                LEFT JOIN utilisateur u ON u.id = t.idUtilisateur";
        $params = [];
        if ($recherche !== '') {
            $sql .= " WHERE r.culture LIKE :q1 OR t.nom LIKE :q2 OR u.nom LIKE :q3 OR u.prenom LIKE :q4";
            $like = '%' . $recherche . '%';
            $params = ['q1' => $like, 'q2' => $like, 'q3' => $like, 'q4' => $like];
        }
        $sql .= " ORDER BY r.dateRecolte DESC";
        $req = config::getConnexion()->prepare($sql);
        $req->execute($params);
        return $req->fetchAll();
    }

    // Une récolte SEULEMENT si son terrain appartient à cet utilisateur (sinon false)
    public function getRecolteOfUser(int $id, int $idUtilisateur)
    {
        $sql = "SELECT r.*
                FROM recolte r
                JOIN terrain t ON t.id = r.idTerrain
                WHERE r.id = :id AND t.idUtilisateur = :idU";
        $req = config::getConnexion()->prepare($sql);
        $req->execute(['id' => $id, 'idU' => $idUtilisateur]);
        return $req->fetch();
    }

    /* =========================================================
     *  AJOUT / MODIFICATION / SUPPRESSION
     * ========================================================= */

    public function addRecolte(Recolte $recolte): void
    {
        $sql = "INSERT INTO recolte (culture, datePlantation, dateRecolte, quantite, unite, idTerrain)
                VALUES (:culture, :datePlantation, :dateRecolte, :quantite, :unite, :idT)";
        $req = config::getConnexion()->prepare($sql);
        $req->execute([
            'culture'        => $recolte->getCulture(),
            'datePlantation' => $recolte->getDatePlantation(),
            'dateRecolte'    => $recolte->getDateRecolte(),
            'quantite'       => $recolte->getQuantite(),
            'unite'          => $recolte->getUnite(),
            'idT'            => $recolte->getIdTerrain(),
        ]);
    }

    // ⚠️ Appeler seulement après avoir vérifié (getRecolteOfUser + validateRecolte)
    //    que la récolte ET le nouveau terrain appartiennent à l'utilisateur.
    public function updateRecolte(Recolte $recolte): void
    {
        $sql = "UPDATE recolte
                SET culture = :culture, datePlantation = :datePlantation, dateRecolte = :dateRecolte,
                    quantite = :quantite, unite = :unite, idTerrain = :idT
                WHERE id = :id";
        $req = config::getConnexion()->prepare($sql);
        $req->execute([
            'culture'        => $recolte->getCulture(),
            'datePlantation' => $recolte->getDatePlantation(),
            'dateRecolte'    => $recolte->getDateRecolte(),
            'quantite'       => $recolte->getQuantite(),
            'unite'          => $recolte->getUnite(),
            'idT'            => $recolte->getIdTerrain(),
            'id'             => $recolte->getId(),
        ]);
    }

    // Front : $idUtilisateur obligatoire / Back (admin) : null
    public function deleteRecolte(int $id, ?int $idUtilisateur = null): bool
    {
        if ($idUtilisateur === null) {
            $req = config::getConnexion()->prepare("DELETE FROM recolte WHERE id = :id");
            $req->execute(['id' => $id]);
        } else {
            $sql = "DELETE r FROM recolte r
                    JOIN terrain t ON t.id = r.idTerrain
                    WHERE r.id = :id AND t.idUtilisateur = :idU";
            $req = config::getConnexion()->prepare($sql);
            $req->execute(['id' => $id, 'idU' => $idUtilisateur]);
        }
        return $req->rowCount() > 0;
    }

    /* =========================================================
     *  CONTRÔLE DE SAISIE (côté serveur)
     * ========================================================= */

    public function validateRecolte(array $data, int $idUtilisateur): array
    {
        $erreurs = [];

        // Terrain : obligatoire ET doit appartenir à l'utilisateur
        $idTerrain = filter_var($data['idTerrain'] ?? '', FILTER_VALIDATE_INT);
        if (!$idTerrain) {
            $erreurs['idTerrain'] = 'Veuillez choisir un terrain.';
        } elseif (!(new TerrainController())->getTerrainOfUser($idTerrain, $idUtilisateur)) {
            $erreurs['idTerrain'] = 'Ce terrain ne vous appartient pas.';
        }

        // Culture : obligatoire, 2 à 100 caractères, lettres / espaces / ' -
        $culture = trim($data['culture'] ?? '');
        if ($culture === '') {
            $erreurs['culture'] = 'La culture est obligatoire.';
        } elseif (mb_strlen($culture) < 2 || mb_strlen($culture) > 100) {
            $erreurs['culture'] = 'La culture doit contenir entre 2 et 100 caractères.';
        } elseif (!preg_match("/^[\p{L}\s'\-]+$/u", $culture)) {
            $erreurs['culture'] = 'La culture ne doit contenir que des lettres, espaces, apostrophes ou tirets.';
        }

        // Dates : valides, plantation pas dans le futur, récolte après plantation
        $datePlantation = $this->dateValide($data['datePlantation'] ?? '');
        $dateRecolte    = $this->dateValide($data['dateRecolte'] ?? '');
        $aujourdhui     = new DateTime('today');

        if (!$datePlantation) {
            $erreurs['datePlantation'] = 'Veuillez saisir une date de plantation valide.';
        } elseif ($datePlantation > $aujourdhui) {
            $erreurs['datePlantation'] = 'La date de plantation ne peut pas être dans le futur.';
        }

        if (!$dateRecolte) {
            $erreurs['dateRecolte'] = 'Veuillez saisir une date de récolte valide.';
        } elseif ($datePlantation && $dateRecolte <= $datePlantation) {
            $erreurs['dateRecolte'] = 'La date de récolte doit être après la date de plantation.';
        }

        // Quantité : nombre > 0 et raisonnable
        $quantite = str_replace(',', '.', trim($data['quantite'] ?? ''));
        if ($quantite === '') {
            $erreurs['quantite'] = 'La quantité est obligatoire.';
        } elseif (!is_numeric($quantite)) {
            $erreurs['quantite'] = 'La quantité doit être un nombre.';
        } elseif ((float) $quantite <= 0) {
            $erreurs['quantite'] = 'La quantité doit être supérieure à 0.';
        } elseif ((float) $quantite > 1000000) {
            $erreurs['quantite'] = 'La quantité ne peut pas dépasser 1 000 000.';
        }

        // Unité : une valeur de la liste
        if (!in_array($data['unite'] ?? '', self::UNITES, true)) {
            $erreurs['unite'] = 'Veuillez choisir une unité.';
        }

        return $erreurs;
    }

    // Retourne un DateTime si la chaîne est une vraie date AAAA-MM-JJ, sinon null
    private function dateValide(string $valeur): ?DateTime
    {
        $date = DateTime::createFromFormat('!Y-m-d', $valeur);
        if ($date && $date->format('Y-m-d') === $valeur) {
            return $date;
        }
        return null;
    }

    /* =========================================================
     *  STATISTIQUES (Back Office)
     * ========================================================= */

    public function countRecoltes(): int
    {
        return (int) config::getConnexion()->query("SELECT COUNT(*) FROM recolte")->fetchColumn();
    }

    public function dernieresRecoltes(int $limite = 5): array
    {
        $sql = "SELECT r.culture, r.dateRecolte, t.nom AS terrainNom
                FROM recolte r
                JOIN terrain t ON t.id = r.idTerrain
                ORDER BY r.dateRecolte DESC
                LIMIT :limite";
        $req = config::getConnexion()->prepare($sql);
        $req->bindValue('limite', $limite, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll();
    }
}
