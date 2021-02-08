<?php
/** User: Sabo */

namespace app\core\db;
use app\core\Application;

/** 
 * Class Database
 * 
 * @author Essam Abed <abedissam95@gmail.com>
 * @package app\core\db
*/

class Database{

    public \PDO $pdo;

    /**
     * Database constructor
     * 
     * @param $config
     */
    public function __construct(array $config){
        $dsn = $config['dsn'] ?? '';
        $user = $config['user'] ?? '';
        $password = $config['password'] ?? '';
        $this->pdo = new \PDO($dsn, $user, $password);
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    public function applyMigrations(){
        $this->createMigrationsTable();
        $appliedMigrations = $this->getAppliedMigrations();

        $newMigrations = [];

        //scandir = returns a list of all the files inside a specified directory
        $files = scandir(Application::$ROOT_DIRECTORY.'/migrations');
        $toAppliedMigrations = array_diff($files, $appliedMigrations);

        foreach($toAppliedMigrations as $migration){
            if ($migration === '.' || $migration === '..'){
                continue;
            }
            
            require_once Application::$ROOT_DIRECTORY.'/migrations/'. $migration;
            $className = pathinfo($migration, PATHINFO_FILENAME); //return filename of the file migration_name.php
            //var_dump($className);
            $instance = new $className();
            $this->log("Applying migration $migration".PHP_EOL);
            $instance->up();
            $this->log("Applied migration $migration".PHP_EOL);

            $newMigrations [] = $migration;
        }
        if (!empty($newMigrations))
            $this->saveMigrations($newMigrations);
        else
            $this->log("All migrations were applied successfully");
    }
    
    public function createMigrationsTable(){
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS migrations(
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(255),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=INNODB;"
        );
    }
    
    public function getAppliedMigrations(){
        $st = $this->pdo->prepare("SELECT migration FROM migrations");
        $st->execute();
        
        return $st->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function saveMigrations(array $migrations){
        $migrations = array_map(fn($m)=> "('$m')", $migrations);
        $str = implode(",", $migrations); 
        $st = $this->pdo->prepare(
            "INSERT INTO migrations (migration)
            VALUES $str"
        );
        $st->execute();
    }

    protected function log($message) {
        echo '['.date('Y-m-d H:i:s').'] - '.$message . PHP_EOL;
    }

    public static function prepare($query){
        return Application::$app->db->pdo->prepare($query);
    }
}