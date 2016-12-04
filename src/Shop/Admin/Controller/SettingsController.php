<?php
namespace Shop\Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SettingsController extends AbstractActionController
{
    function indexAction()
    {
        $view = new ViewModel();
        $view->setTemplate('shop/admin/settings.phtml');
        return $view;
    }
}