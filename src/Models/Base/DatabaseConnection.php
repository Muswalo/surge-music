<?php

namespace Muswalo\Surgemusic\Models\Base;

// use Dotenv\Dotenv;
use PDO;
use PDOException;

class DatabaseConnection
{
    /**
     * Establishes a database connection using PDO.
     *
     * @return PDO The PDO connection object.
     * @throws PDOException If the connection fails.
     */
    public static function connect(): PDO
    {
        try {
            // $dotenv = Dotenv::createImmutable(__DIR__);
            // $dotenv->load();

            $servername = "localhost";
            $username = "root";
            $password = "";
            $database = "surge";

            // Create a new PDO connection
            $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);

            // Set PDO error mode to exception
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $conn->exec("SET time_zone = '+02:00'");

            // Return the connection object
            return $conn;
        } catch (PDOException $e) {
            die('Connection failed'); 
        }
    }
}
