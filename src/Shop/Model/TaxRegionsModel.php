<?php

namespace Shop\Model;

use Shop\shopRegistry;
use Zend\Db\Adapter\Adapter;

class TaxRegionsModel extends ShopModel
{
    public function __construct(Adapter $adapter)
    {

        $this->table = 'Tax_regions';
        $this->id = array('tax_id', 'country', 'region_code');
        parent::__construct($adapter);
    }

    /**
     * Получение региональных данных налогов по
     * идентификатору налога
     *
     * @param int $tax_id
     * @return array
     */
    public function getByTax($tax_id)
    {
        $tax_id = (int)$tax_id;
        $result = $this->getByField('tax_id', $tax_id);
        return $result;
    }


    /**
     * @param int $tax_id
     * @param array $address - ['country', 'region', ]
     *
     * @return array|bool
     */
    public function getByTaxAddress($tax_id, array $address)
    {
        $tax_id = (int)$tax_id;
        $result = false;
        $shop_registry = shopRegistry::getInstance($this->adapter);

        $country = (!empty($address['country'])) ? $address['country'] : $shop_registry->getSetting('country');

        if (!empty($country)) {
            $data = array('tax_id' => $tax_id, 'country' => $country);

            if (!empty($address['region_code'])) {
                $data['region_code'] = $address['region_code'];
            }
            $result = $this->getByField($data);
        }

        /**
         * Возможно придёться добавить функционал для идентификаторов
         * %ALL - все страны
         * %RW - все остальные страны
         * %EU - все европейские страны
         */

        return $result;
    }
}
