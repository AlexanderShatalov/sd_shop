<?php
namespace Shop;

use Shop\Model\CouponModel;
use Shop\Model\CurrencyModel;
use Shop\Model\StorageModel;
use Shop\Model\TaxModel;
//use Shop\Model\ProductsModel;
//use Shop\Model\SkusModel;

class Module
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => [
                    __NAMESPACE__ => __DIR__.'/src/'.__NAMESPACE__,
                ],

            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__.'/config/module.config.php';
    }

    function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Shop\Model\CurrencyModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new CurrencyModel($dbAdapter);
                    return $table;
                },

                'Shop\Model\StorageModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new StorageModel($dbAdapter);
                    return $table;
                },

                'Shop\Model\CouponModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new CouponModel($dbAdapter);
                    return $table;
                },

                'Shop\Model\TaxModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new TaxModel($dbAdapter);
                    return $table;
                },
                /*'Shop\Model\ProductsModel' => function($sm){
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new ProductsModel($dbAdapter);
                    return $table;
                },

                'Shop\Model\SkusModel' => function($sm){
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new SkusModel($dbAdapter);
                    return $table;
                }*/
            )
        );
    }
}