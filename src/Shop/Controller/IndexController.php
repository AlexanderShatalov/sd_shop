<?php

namespace Shop\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{

    public function indexAction()
    {
        $cm = $this->getServiceLocator()->get('Shop\Model\CouponModel');

        /*echo '<pre>';
        print_r($cm->incUsed(1));
        echo '</pre>';*/

        $query = [
            'code' => '9ROK2INJ',
            //'type' => '%',
            'limit' => '100',
            'used' => 0,
            'value' => 15,
            'comment' => "<script>alert('zxc');</script>",
            'create_datetime' => '',
            'edit_datetime' => '',
            'expire_datetime' => '2017-08-12',
            'contact_id' => 1
        ];

        //$cm->addCoupon($query);
        //$cm->removeCoupon(14);

        $tmp = date('Y-m-d H:i:s');
        echo '<pre>';
            print_r(gettype($tmp));
        echo '</pre>';

        $result = $cm->getAll();
        echo '<pre>';
            print_r($result);
        echo '</pre>';

        /**
         * 9ROK2INJ
         * TK8WZJ48
         * FVKG2IFF
         * GU21H9FV
         * O2R4L7E5
         *
         * code
         * type
         * limit
         * used
         * value
         * comment
         * create_datetime
         * edit_datetime
         * expire_datetime
         * contact_id
         */


    }
}
