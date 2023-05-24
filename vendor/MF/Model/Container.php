<?php

namespace MF\Model;

use App\Connection;

class Container {

    public static function get_Model($model) {

        $class = "\\App\\Models\\".ucfirst($model);

        $connect = Connection::getDb();

        return new $class($connect);
    }
}