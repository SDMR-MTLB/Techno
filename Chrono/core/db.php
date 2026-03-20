<?php
// core/db.php

require_once __DIR__ . '/../config/database.php';

class DB {
    private static $pdo = null;

    public static function getConnection() {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../config/database.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
            self::$pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);
        }
        return self::$pdo;
    }
}