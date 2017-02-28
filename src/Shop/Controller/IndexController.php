<?php

namespace Shop\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Shop\Model;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $sm = $this->getServiceLocator()->get('SkusModel');

        /*$input_data = array(
            'sku' => 'TEST',
            'name' => 'TEST',
            'products_id' => 1,
            'available' => 1
        );*/
        //$sm->deleteByProducts(array(1));

        echo '<pre>';
            print_r($sm->deleteByProducts(array(12)));
        echo '</pre>';


        /*echo '<pre>';
            print_r($sm->getAll('id', true));
        echo '</pre>';*/
    }
}
