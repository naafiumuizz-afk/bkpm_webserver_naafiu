<?php

namespace App\Models;

use App\Core\Database;
use PDO;

require_once __DIR__ . '/../Core/Database.php';

abstract class Model
{
    protected static function connection(): PDO
    {
        return Database::connection();
    }
}