<?php
namespace Shop;

use Shop\Model\CouponModel;
use Shop\Model\CurrencyModel;
use Shop\Model\StorageModel;
use Shop\Model\TaxModel;
use Shop\Model\TaxRegionsModel;

//use Shop\Model\ProductsModel;
use Shop\Model\SkusModel;

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
            'aliases' => array(
                'Shop\Model\CurrencyModel' => 'CurrencyModel',
                'Shop\Model\StorageModel' => 'StorageModel',
                'Shop\Model\CouponModel' => 'CouponModel',
                'Shop\Model\TaxModel' => 'TaxModel',
                'Shop\shopRegistry' => 'ShopRegistry',
                'Shop\Model\TaxRegionsModel' => 'TaxRegionsModel',
                'Shop\Model\SkusModel' => 'SkusModel',
            ),

            'factories' => array(
                'CurrencyModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new CurrencyModel($dbAdapter);
                    return $table;
                },

                'StorageModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new StorageModel($dbAdapter);
                    return $table;
                },

                'CouponModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new CouponModel($dbAdapter);
                    return $table;
                },

                'TaxModel' => function ($sm) {
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new TaxModel($dbAdapter);
                    return $table;
                },

                'ShopRegistry' => function ($sm) {
                    $adapter = $sm->get('ZendDbAdapterAdapter');
                    $shop_registry = shopRegistry::getInstance($adapter);
                    return $shop_registry;
                },

                'TaxRegionsModel' => function ($sm) {
                    $adapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new TaxRegionsModel($adapter);
                    return $table;
                },
                /*'Shop\Model\ProductsModel' => function($sm){
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new ProductsModel($dbAdapter);
                    return $table;
                },*/

                'SkusModel' => function($sm){
                    $dbAdapter = $sm->get('ZendDbAdapterAdapter');
                    $table = new SkusModel($dbAdapter);
                    return $table;
                }
            ),

        );
    }
}