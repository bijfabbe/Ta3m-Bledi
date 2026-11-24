<?php
/*
 * Controller : Utilisateur
 * Inscription, connexion, profil, mot de passe oublié + gestion admin (CRUD, blocage).
 * Toutes les requêtes sont préparées (PDO).
 */
require_once __DIR__ . '/../Config/config.php';
require_once __DIR__ . '/../Model/Utilisateur.php';

class UtilisateurController
{
    public const ROLES   = ['AGRICULTEUR' => 'Agriculteur', 'ADMIN' => 'Admin'];
    public const STATUTS = ['ACTIF' => 'Actif', 'BLOQUE' => 'Bloqué'];

    /* =========================================================
     *  LECTURE
     * ========================================================= */

    public function listUtilisateurs(string $recherche = ''): array
    {
        $sql = "SELECT id, nom, prenom, email, telephone, role, statut, dateInscription FROM utilisateur";
        $params = [];
        if ($recherche !== '') {
            $sql .= " WHERE nom LIKE :q1 OR prenom LIKE :q2 OR email LIKE :q3";
            $like = '%' . $recherche . '%';
            $params = ['q1' => $like, 'q2' => $like, 'q3' => $like];
        }
        $sql .= " ORDER BY id DESC";
        $req = config::getConnexion()->prepare($sql);
        $req->execute($params);
        return $req->fetchAll();
    }

    public function getUtilisateur(int $id)
    {
        $req = config::getConnexion()->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $req->execute(['id' => $id]);
        return $req->fetch();
    }

    public function getByEmail(string $email)
    {
        $req = config::getConnexion()->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $req->execute(['email' => $email]);
        return $req->fetch();
    }

    // L'email est-il déjà pris (par quelqu'un d'autre que $exceptId) ?
    public function emailExiste(string $email, ?int $exceptId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email";
        $params = ['email' => $email];
        if ($exceptId !== null) {
            $sql .= " AND id <> :id";
            $params['id'] = $exceptId;
        }
        $req = config::getConnexion()->prepare($sql);
        $req->execute($params);
        return (int) $req->fetchColumn() > 0;
    }

    /* =========================================================
     *  AJOUT / MODIFICATION / SUPPRESSION
     * ========================================================= */

    public function addUtilisateur(Utilisateur $u): int
    {
        $sql = "INSERT INTO utilisateur (nom, prenom, email, telephone, motDePasse, role, statut)
                VALUES (:nom, :prenom, :email, :telephone, :motDePasse, :role, :statut)";
        $pdo = config::getConnexion();
        $req = $pdo->prepare($sql);
        $req->execute([
            'nom'        => $u->getNom(),
            'prenom'     => $u->getPrenom(),
            'email'      => $u->getEmail(),
            'telephone'  => $u->getTelephone(),
            'motDePasse' => $u->getMotDePasse(),
            'role'       => $u->getRole(),
            'statut'     => $u->getStatut(),
        ]);
        return (int) $pdo->lastInsertId();
    }

    // Modifie les informations (pas le mot de passe, pas la photo)
    public function updateUtilisateur(Utilisateur $u): void
    {
        $sql = "UPDATE utilisateur
                SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone,
                    role = :role, statut = :statut
                WHERE id = :id";
        $req = config::getConnexion()->prepare($sql);
        $req->execute([
            'nom'       => $u->getNom(),
            'prenom'    => $u->getPrenom(),
            'email'     => $u->getEmail(),
            'telephone' => $u->getTelephone(),
            'role'      => $u->getRole(),
            'statut'    => $u->getStatut(),
            'id'        => $u->getId(),
        ]);
    }

    public function updateMotDePasse(int $id, string $motDePasseClair): void
    {
        $req = config::getConnexion()->prepare(
            "UPDATE utilisateur SET motDePasse = :mdp, resetToken = NULL, resetExpiration = NULL WHERE id = :id"
        );
        $req->execute(['mdp' => password_hash($motDePasseClair, PASSWORD_DEFAULT), 'id' => $id]);
    }

    public function updatePhoto(int $id, ?string $photo): void
    {
        $req = config::getConnexion()->prepare("UPDATE utilisateur SET photo = :photo WHERE id = :id");
        $req->execute(['photo' => $photo, 'id' => $id]);
    }

    public function changerStatut(int $id, string $statut): bool
    {
        $req = config::getConnexion()->prepare("UPDATE utilisateur SET statut = :statut WHERE id = :id");
        $req->execute(['statut' => $statut, 'id' => $id]);
        return $req->rowCount() > 0;
    }

    // Supprime aussi ses terrains, récoltes et badges (ON DELETE CASCADE)
    public function deleteUtilisateur(int $id): bool
    {
        $req = config::getConnexion()->prepare("DELETE FROM utilisateur WHERE id = :id");
        $req->execute(['id' => $id]);
        return $req->rowCount() > 0;
    }

    /* =========================================================
     *  CONNEXION
     *  Retourne ['ok' => true, 'user' => [...]] ou ['ok' => false, 'erreur' => '...']
     * ========================================================= */

    public function connecter(string $email, string $motDePasse): array
    {
        $user = $this->getByEmail($email);
        // Même message si l'email n'existe pas ou si le mot de passe est faux (sécurité)
        if (!$user || !password_verify($motDePasse, $user['motDePasse'])) {
            return ['ok' => false, 'erreur' => 'Email ou mot de passe incorrect.'];
        }
        if ($user['statut'] === 'BLOQUE') {
            return ['ok' => false, 'erreur' => "Votre compte est bloqué. Contactez l'administrateur."];
        }
        return ['ok' => true, 'user' => $user];
    }

    /* =========================================================
     *  MOT DE PASSE OUBLIÉ
     * ========================================================= */

    // Crée un jeton valable 1 heure. Retourne le jeton (à mettre dans le lien) ou null.
    public function creerJetonReinitialisation(string $email): ?string
    {
        $user = $this->getByEmail($email);
        if (!$user) {
            return null;
        }
        $jeton = bin2hex(random_bytes(32));
        $req = config::getConnexion()->prepare(
            "UPDATE utilisateur
             SET resetToken = :hash, resetExpiration = DATE_ADD(NOW(), INTERVAL 1 HOUR)
             WHERE id = :id"
        );
        // On stocke seulement le hash du jeton (si la base fuit, les liens restent inutilisables)
        $req->execute(['hash' => hash('sha256', $jeton), 'id' => $user['id']]);
        return $jeton;
    }

    public function getByJeton(string $jeton)
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $jeton)) {
            return false;
        }
        $req = config::getConnexion()->prepare(
            "SELECT * FROM utilisateur WHERE resetToken = :hash AND resetExpiration > NOW()"
        );
        $req->execute(['hash' => hash('sha256', $jeton)]);
        return $req->fetch();
    }

    /* =========================================================
     *  CONTRÔLE DE SAISIE (côté serveur)
     * ========================================================= */

    // Champs communs : nom, prénom, email, téléphone
    public function validateInfos(array $data, ?int $exceptId = null): array
    {
        $erreurs = [];

        foreach (['nom' => 'Le nom', 'prenom' => 'Le prénom'] as $champ => $libelle) {
            $valeur = trim($data[$champ] ?? '');
            if ($valeur === '') {
                $erreurs[$champ] = "$libelle est obligatoire.";
            } elseif (mb_strlen($valeur) < 2 || mb_strlen($valeur) > 50) {
                $erreurs[$champ] = "$libelle doit contenir entre 2 et 50 caractères.";
            } elseif (!preg_match("/^[\p{L}\s'\-]+$/u", $valeur)) {
                $erreurs[$champ] = "$libelle ne doit contenir que des lettres.";
            }
        }

        $email = trim($data['email'] ?? '');
        if ($email === '') {
            $erreurs['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 100) {
            $erreurs['email'] = "L'email n'est pas valide (exemple@mail.com).";
        } elseif ($this->emailExiste($email, $exceptId)) {
            $erreurs['email'] = 'Cet email est déjà utilisé.';
        }

        $telephone = trim($data['telephone'] ?? '');
        if ($telephone === '') {
            $erreurs['telephone'] = 'Le téléphone est obligatoire.';
        } elseif (!preg_match('/^\d{8}$/', $telephone)) {
            $erreurs['telephone'] = 'Le téléphone doit contenir exactement 8 chiffres.';
        }

        return $erreurs;
    }

    // Mot de passe : 8 caractères min, au moins une lettre et un chiffre, + confirmation
    public function validateMotDePasse(string $motDePasse, string $confirmation, string $champ = 'motDePasse', string $champConfirmation = 'confirmation'): array
    {
        $erreurs = [];
        if ($motDePasse === '') {
            $erreurs[$champ] = 'Le mot de passe est obligatoire.';
        } elseif (strlen($motDePasse) < 8 || strlen($motDePasse) > 64) {
            $erreurs[$champ] = 'Le mot de passe doit contenir entre 8 et 64 caractères.';
        } elseif (!preg_match('/[A-Za-z]/', $motDePasse) || !preg_match('/\d/', $motDePasse)) {
            $erreurs[$champ] = 'Le mot de passe doit contenir au moins une lettre et un chiffre.';
        }
        if ($confirmation !== $motDePasse) {
            $erreurs[$champConfirmation] = 'Les deux mots de passe ne correspondent pas.';
        }
        return $erreurs;
    }

    public function validateRoleStatut(array $data): array
    {
        $erreurs = [];
        if (!array_key_exists($data['role'] ?? '', self::ROLES)) {
            $erreurs['role'] = 'Veuillez choisir un rôle.';
        }
        if (!array_key_exists($data['statut'] ?? '', self::STATUTS)) {
            $erreurs['statut'] = 'Veuillez choisir un statut.';
        }
        return $erreurs;
    }

    /* =========================================================
     *  STATISTIQUES
     * ========================================================= */

    public function countUtilisateurs(): int
    {
        return (int) config::getConnexion()->query("SELECT COUNT(*) FROM utilisateur")->fetchColumn();
    }
}
