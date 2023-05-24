<?php

namespace App;

use PDOException;

class Connection {

    public  static function getDb() {

        try {
            $connect = new \PDO(
                "mysql:host=localhost;dbname=twitter_clone;charset=utf8",
                "root",
                ""
            );

            return $connect;

        } catch (\PDOException $e) {
        }
    }
}