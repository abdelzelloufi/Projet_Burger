<?php
    class Database{
        private static $dbHost = "localhost";
        private static $dbName = "burger_code";
        private static $dbUser = "root";
        private static $dbUserPassword = "";
        private static $connection = null;

        // Méthode pour se connecter à la base de données
        public static function connect()
        {
            if(self::$connection == null) {
                try {
                    self::$connection = new PDO("mysql:host=" . self::$dbHost . ";dbname=" . self::$dbName, self::$dbUser, self::$dbUserPassword);
                }
                catch(PDOException $e) {
                    die($e->getMessage());
                }
            }
            return self::$connection;
        }

        // Méthode pour se déconnecter
        public static function disconnect() {
            self::$connection = null;
        }
    }

    // Connexion à la base de données
    Database::connect();
?>
