<?php
/*
 * Connexion à la base de données (PDO)
 * ⚠️ Une seule config pour toute l'équipe :
 *    si un membre a déjà créé Config/config.php, garde le sien
 *    et vérifie juste que la classe s'appelle "config" et la méthode "getConnexion()".
 */
class config
{
    private static $pdo = null;

    public static function getConnexion()
    {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=ta3m_bledi;charset=utf8mb4', // ← nom de la base de l'équipe
                    'root',                                                    // ← utilisateur XAMPP
                    '',                                                        // ← mot de passe XAMPP (vide par défaut)
                    [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erreurs SQL = exceptions
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // résultats en tableaux associatifs
                        PDO::ATTR_EMULATE_PREPARES   => false,                  // vraies requêtes préparées
                    ]
                );
            } catch (PDOException $e) {
                die('Erreur de connexion à la base : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}
