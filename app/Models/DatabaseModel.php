<?php

namespace app\Models;

use Nette\Database\Connection;
use Nette\SmartObject;

class DatabaseModel
{

    use SmartObject;

    private $database;

    // connect with Nette\Database\Connection
    public function __construct(Connection $database)
    {
        $this->database = $database;
    }
}
