<?php
namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class ProductsModel extends ShopModel
{
    public function __construct(Adapter $adapter)
    {
        $this->table = 'Products';
        parent::__construct($adapter);
    }
}

