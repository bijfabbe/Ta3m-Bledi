<?php
/*
 * Controller : Badge + Attribution (badge donné à un utilisateur)
 * Toutes les requêtes sont préparées (PDO).
 */
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Badge.php';
require_once __DIR__ . '/../Model/Attribution.php';

class BadgeController
{
    /* =========================================================
     *  BADGES : LECTURE
     * ========================================================= */

    public function listBadges(): array
    {
        $sql = "SELECT b.*, (SELECT COUNT(*) FROM utilisateur_badge ub WHERE ub.idBadge = b.id) AS nbAttributions
                FROM badge b
                ORDER BY b.points, b.nom";
        return config::getConnexion()->query($sql)->fetchAll();
    }

    public function getBadge(int $id)
    {
        $req = config::getConnexion()->prepare("SELECT * FROM badge WHERE id = :id");
        $req->execute(['id' => $id]);
        return $req->fetch();
    }

    public function nomExiste(string $nom, ?int $exceptId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM badge WHERE nom = :nom";
        $params = ['nom' => $nom];
        if ($exceptId !== null) {
            $sql .= " AND id <> :id";
            $params['id'] = $exceptId;
        }
        $req = config::getConnexion()->prepare($sql);
        $req->execute($params);
        return (int) $req->fetchColumn() > 0;
    }

    /* =========================================================
     *  BADGES : AJOUT / MODIFICATION / SUPPRESSION
     * ========================================================= */

    public function addBadge(Badge $b): void
    {
        $sql = "INSERT INTO badge (nom, description, critere, points, icone)
                VALUES (:nom, :description, :critere, :points, :icone)";
        $req = config::getConnexion()->prepare($sql);
        $req->execute([
            'nom'         => $b->getNom(),
            'description' => $b->getDescription(),
            'critere'     => $b->getCritere(),
            'points'      => $b->getPoints(),
            'icone'       => $b->getIcone(),
        ]);
    }

    public function updateBadge(Badge $b): void
    {
        $sql = "UPDATE badge
                SET nom = :nom, description = :description, critere = :critere, points = :points, icone = :icone
                WHERE id = :id";
        $req = config::getConnexion()->prepare($sql);
        $req->execute([
            'nom'         => $b->getNom(),
            'description' => $b->getDescription(),
            'critere'     => $b->getCritere(),
            'points'      => $b->getPoints(),
            'icone'       => $b->getIcone(),
            'id'          => $b->getId(),
        ]);
    }

    // Les attributions de ce badge sont supprimées automatiquement (ON DELETE CASCADE)
    public function deleteBadge(int $id): bool
    {
        $req = config::getConnexion()->prepare("DELETE FROM badge WHERE id = :id");
        $req->execute(['id' => $id]);
        return $req->rowCount() > 0;
    }

    /* =========================================================
     *  ATTRIBUTIONS
     * ========================================================= */

    public function listAttributions(): array
    {
        $sql = "SELECT ub.*, b.nom AS badgeNom, CONCAT(u.prenom, ' ', u.nom) AS utilisateurNom
                FROM utilisateur_badge ub
                JOIN badge b ON b.id = ub.idBadge
                JOIN utilisateur u ON u.id = ub.idUtilisateur
                ORDER BY ub.dateAttribution DESC";
        return config::getConnexion()->query($sql)->fetchAll();
    }

    public function dejaAttribue(int $idUtilisateur, int $idBadge): bool
    {
        $req = config::getConnexion()->prepare(
            "SELECT COUNT(*) FROM utilisateur_badge WHERE idUtilisateur = :u AND idBadge = :b"
        );
        $req->execute(['u' => $idUtilisateur, 'b' => $idBadge]);
        return (int) $req->fetchColumn() > 0;
    }

    public function attribuer(Attribution $a): void
    {
        $req = config::getConnexion()->prepare(
            "INSERT INTO utilisateur_badge (idUtilisateur, idBadge, motif) VALUES (:u, :b, :motif)"
        );
        $req->execute(['u' => $a->getIdUtilisateur(), 'b' => $a->getIdBadge(), 'motif' => $a->getMotif()]);
    }

    public function deleteAttribution(int $id): bool
    {
        $req = config::getConnexion()->prepare("DELETE FROM utilisateur_badge WHERE id = :id");
        $req->execute(['id' => $id]);
        return $req->rowCount() > 0;
    }

    // Front "Mes badges" : TOUS les badges + date d'obtention pour CET utilisateur (NULL si pas obtenu)
    public function badgesDeUtilisateur(int $idUtilisateur): array
    {
        $sql = "SELECT b.*, ub.dateAttribution
                FROM badge b
                LEFT JOIN utilisateur_badge ub ON ub.idBadge = b.id AND ub.idUtilisateur = :u
                ORDER BY (ub.dateAttribution IS NULL), ub.dateAttribution DESC, b.points";
        $req = config::getConnexion()->prepare($sql);
        $req->execute(['u' => $idUtilisateur]);
        return $req->fetchAll();
    }

    /* =========================================================
     *  CONTRÔLE DE SAISIE (côté serveur)
     * ========================================================= */

    public function validateBadge(array $data, ?int $exceptId = null): array
    {
        $erreurs = [];

        $nom = trim($data['nom'] ?? '');
        if ($nom === '') {
            $erreurs['nom'] = 'Le nom du badge est obligatoire.';
        } elseif (mb_strlen($nom) < 3 || mb_strlen($nom) > 50) {
            $erreurs['nom'] = 'Le nom doit contenir entre 3 et 50 caractères.';
        } elseif (!preg_match("/^[\p{L}0-9\s'\-]+$/u", $nom)) {
            $erreurs['nom'] = 'Le nom ne doit contenir que des lettres, chiffres, espaces, apostrophes ou tirets.';
        } elseif ($this->nomExiste($nom, $exceptId)) {
            $erreurs['nom'] = 'Un badge porte déjà ce nom.';
        }

        $points = trim($data['points'] ?? '');
        if ($points === '') {
            $erreurs['points'] = 'Les points sont obligatoires.';
        } elseif (!ctype_digit($points) || (int) $points < 1 || (int) $points > 1000) {
            $erreurs['points'] = 'Les points doivent être un nombre entier entre 1 et 1000.';
        }

        $description = trim($data['description'] ?? '');
        if ($description === '') {
            $erreurs['description'] = 'La description est obligatoire.';
        } elseif (mb_strlen($description) < 10 || mb_strlen($description) > 255) {
            $erreurs['description'] = 'La description doit contenir entre 10 et 255 caractères.';
        }

        $critere = trim($data['critere'] ?? '');
        if ($critere === '') {
            $erreurs['critere'] = "Le critère d'obtention est obligatoire.";
        } elseif (mb_strlen($critere) < 3 || mb_strlen($critere) > 100) {
            $erreurs['critere'] = 'Le critère doit contenir entre 3 et 100 caractères.';
        }

        return $erreurs;
    }

    public function validateAttribution(array $data, UtilisateurController $utilisateurC): array
    {
        $erreurs = [];

        $idU = filter_var($data['idUtilisateur'] ?? '', FILTER_VALIDATE_INT);
        $idB = filter_var($data['idBadge'] ?? '', FILTER_VALIDATE_INT);

        if (!$idU || !$utilisateurC->getUtilisateur($idU)) {
            $erreurs['idUtilisateur'] = 'Veuillez choisir un utilisateur.';
        }
        if (!$idB || !$this->getBadge($idB)) {
            $erreurs['idBadge'] = 'Veuillez choisir un badge.';
        }
        if (empty($erreurs) && $this->dejaAttribue($idU, $idB)) {
            $erreurs['idBadge'] = 'Cet utilisateur possède déjà ce badge.';
        }

        $motif = trim($data['motif'] ?? '');
        if ($motif === '') {
            $erreurs['motif'] = 'Le motif est obligatoire.';
        } elseif (mb_strlen($motif) < 3 || mb_strlen($motif) > 255) {
            $erreurs['motif'] = 'Le motif doit contenir entre 3 et 255 caractères.';
        }

        return $erreurs;
    }
}
