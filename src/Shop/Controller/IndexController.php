<?php

namespace Shop\Controller;

use Shop\shopRegistry;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Shop\Model;
use Zend\Db\Adapter\Adapter;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {

        //$registry = shopRegistry::getInstance();
        //$registry->info();
        $adapter = $this->getServiceLocator()->get('Adapter');
        $settings_model = new Model\SettingsModel($adapter);

    }
}
