<?php
return array(
       'controllers' => array(
           'invokables' => array(
               'Shop\Controller\Index' => 'Shop\Controller\IndexController',
               'Shop\Controller\Category' => 'Shop\Controller\CategoryController',
               'Shop\Controller\Cart' => 'Shop\Controller\CartController',
               'Shop\Controller\Checkout' => 'Shop\Controller\CheckoutController',
               'Shop\Controller\Success' => 'Shop\Controller\SuccessController',
               'Shop\Admin\Controller\Index' => 'Shop\Admin\Controller\AdminController',
               'Shop\Admin\Controller\Orders' => 'Shop\Admin\Controller\OrdersController',
               'Shop\Admin\Controller\Customers' => 'Shop\Admin\Controller\CustomersController',
               'Shop\Admin\Controller\Products' => 'Shop\Admin\Controller\ProductsController',
               'Shop\Admin\Controller\Importexport' => 'Shop\Admin\Controller\ImportexportController',
               'Shop\Admin\Controller\Settings' => 'Shop\Admin\Controller\SettingsController'
           ),
       ),

        'router' => array(
            'routes' => array(
                'shop' => array(
                    'type' => 'Literal',
                    'options' => array(
                        'route' => '/',
                        'defaults' => array(
                            'controller' => 'Shop\Controller\Index',
                            'action' => 'index'
                        ),
                    ),

                    'may_terminate' => true,
                    'child_routes' => array(
                        'admin' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => 'admin/',
                                'defaults' => array(
                                    'controller' => 'Shop\Admin\Controller\Index',
                                    'action' => 'index'
                                ),
                            ),

                            'may_terminate' => true,
                            'child_routes' => array(
                                'orders' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => 'orders/',
                                        'defaults' => array(
                                            'controller' => 'Shop\Admin\Controller\Orders',
                                            'action' => 'index'
                                        ),
                                    ),
                                ),

                                'customers' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => 'customers/',
                                        'defaults' => array(
                                            'controller' => 'Shop\Admin\Controller\Customers',
                                            'action' => 'index'
                                        ),
                                    ),
                                ),

                                'products' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => 'products/',
                                        'defaults' => array(
                                            'controller' => 'Shop\Admin\Controller\Products',
                                            'action' => 'index'
                                        ),
                                    ),
                                ),

                                'importexport' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => 'importexport/',
                                        'defaults' => array(
                                            'controller' => 'Shop\Admin\Controller\Importexport',
                                            'action' => 'index'
                                        ),
                                    ),
                                ),

                                'settings' => array(
                                    'type' => 'Literal',
                                    'options' => array(
                                        'route' => 'settings/',
                                        'defaults' => array(
                                            'controller' => 'Shop\Admin\Controller\Settings',
                                            'action' => 'index'
                                        ),
                                    ),
                                ),
                            ),
                        ),

                        'category' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route' => 'category/[:category_url/[:product_url/]]',

                                'constraints' => array(
                                        'category_url' => '[a-zA-Z][a-zA-Z0-9-_\+]*',
                                        'product_url' => '[a-zA-Z][a-zA-Z0-9-_\+]*'
                                ),

                                'defaults' => array(
                                    '__NAMESPACE__' => 'Shop\Controller',
                                    'controller' => 'Category',
                                    'action' => 'index'
                                ),
                            ),

                            'may_terminate' => true,
                            'child_routes' => array(

                            ),
                        ),

                        'cart' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => 'cart/',
                                'defaults' => array(
                                    'controller' => 'Shop\Controller\Cart',
                                    'action' => 'index'
                                ),
                            ),
                        ),

                        'checkout' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => 'checkout/',
                                'defaults' => array(
                                    '__NAMESPACE__' => 'Shop\Controller',
                                    'controller' => 'Checkout',
                                    'action' => 'index'
                                ),
                            ),
                        ),

                        'success' => array(
                            'type' => 'Literal',
                            'options' => array(
                                'route' => 'success/',
                                'defaults' => array(
                                    'controller' => 'Shop\Controller\Success',
                                    'action' => 'index',
                                ),
                            ),
                        ),

                        // Это маршрут, предлагаемый по умолчанию. Его разумно
                        // использовать при разработке модуля;
                        // с появлением определенности в отношении
                        // маршрутов для модуля, возможно, появится
                        // смысл указать здесь более точные пути.
                        /*'default' => array(
                            'type' => 'Segment',
                            'options' => array(
                                'route' =>'[:controller[/:action]]',
                                'constraints' => array(
                                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                    'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                ),
                                //'defaults' => array(),
                            ),
                        ),*/
                    ),
                ),
            ),
        ),

        'view_manager' => array(
            'template_path_stack' => array(
                'shop' => __DIR__.'/../view',
            ),
        ),
    );