<?php
namespace Shop;

use Shop\Model;
use Zend\Db\Adapter\Adapter;

class shopRegistry
{
    private static $instance = null;

    private function __construct(){

    }

    private function __clone(){}

    static public function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function info()
    {
        echo '<pre>';
            print_r('Class shopRegistry');
        echo '</pre>';
    }
}