<?php

namespace Shop\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $cm = $this->getServiceLocator()->get("Shop\Model\CurrencyModel");
        echo "<pre>";
        print_r($cm->move(1, 10));
        echo "</pre>";
       
    }
}
