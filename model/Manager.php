<?php
namespace App\model;
use \PDO;

class Manager 
{
    protected function MySQLConnect()
    {
        $db = new PDO('mysql:host=localhost;dbname=virginielw123;charset=utf8mb4', 'root', '');
        return $db;
    }
}