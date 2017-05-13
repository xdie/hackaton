<?php
// Database
define('DATABASE_DRIVER', "mysql");
define('DATABASE_SERVER', "localhost");
define('DATABASE_PORT', "3306");
define('DATABASE_NAME', "hackaton");
define('DATABASE_USERNAME', "root");
define('DATABASE_PASSWORD', "kreator.22");

define('LOG_DIR', '');

$db = new MysqliDb (Array (
                'host' => DATABASE_SERVER,
                'username' => DATABASE_USERNAME, 
                'password' => DATABASE_PASSWORD,
                'db'=> DATABASE_NAME,
                'port' => DATABASE_PORT,
                'charset' => 'utf8'));
