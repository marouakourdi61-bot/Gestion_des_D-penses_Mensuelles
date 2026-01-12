<?php
namespace App\Controllers;

use Core\Database;

class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }
}
