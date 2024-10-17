<?php
    // Classe Database pour gérer la connexion à la base de données
    class Database {
        // Informations de connexion à la base de données
        private static $dbHost = "localhost"; // Hôte de la base de données
        private static $dbName = "burger_code"; // Nom de la base de données
        private static $dbUser = "root"; // Nom d'utilisateur
        private static $dbUserPassword = ""; // Mot de passe
        private static $connection = null; // Variable pour stocker la connexion PDO

        // Méthode pour établir une connexion à la base de données
        public static function connect() {
            // Vérifie si la connexion n'a pas déjà été établie
            if (self::$connection == null) {
                try {
                    // Crée une nouvelle connexion PDO
                    self::$connection = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbUserPassword);
                    // Configuration des options PDO pour afficher les erreurs
                    self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                }
                // Capture les exceptions en cas d'erreur de connexion
                catch (PDOException $e) {
                    // Arrête l'exécution et affiche le message d'erreur
                    die($e->getMessage());
                }
            }
            // Retourne l'objet de connexion PDO
            return self::$connection;
        }

        // Méthode pour se déconnecter de la base de données
        public static function disconnect() {
            // Réinitialise la variable de connexion à null
            self::$connection = null;
        }
    }

    // Exemple de connexion à la base de données
    Database::connect();
?>
