<?php
namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class CouponModel extends shopModel
{
    const TYPE_COUPON_PROCENT = '%';
    const TYPE_COUPON_CURRENCY = 'RUB';

    public function __construct(Adapter $adapter)
    {
        $this->table = 'Discount_coupon';
        parent::__construct($adapter);
    }


    /**
     * Увеличить количество использований купона на единицу
     *
     * @param $id
     *
     */
    public function incUsed($id)
    {
        $id = (int)$id;
        $this->adapter->query("UPDATE {$this->table} SET `used` = `used` + 1 WHERE `id` = $id AND `used` <= `limit` - 1", Adapter::QUERY_MODE_EXECUTE);
    }

    /**
     * Добавить купон
     *
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function addCoupon(array $data)
    {
        if (!is_array($data)) {
            throw new \Exception('Некорректный формат входных данных!');
        }

        $data['type'] = (isset($data['type'])) ? $data['type'] : '%';

        if (!$this->checkFieldType($data['type'])) {
            throw new \Exception('Не указан тип скидки');
        }

        ///////Проверка присутствия обязательных полей///////
        if (!isset($data['code']) || empty($data['code']) || $data['code'] == ' ') {
            throw new \Exception('Не указан код купона');
        }

        $data['limit'] = (isset($data['limit'])) ? (int)$data['limit'] : 0;

        if (empty($data['limit'])) {
            if (!isset($data['expire_datetime']) || empty($data['expire_datetime']) || $data['expire_datetime'] == ' ') {
                throw new \Exception('Не указано условие окончания действия купона!');
            }
        }

        /////////Обработка входных данных/////////////
        foreach ($data as $key => $val) {
            if (is_string($val)) {
                $data[$key] = $this->escape($val);
            }
        }

        $data['create_datetime'] = date('Y-m-d H:i:s');

        $result = $this->insert($data);
        return $result;
    }

    /**
     * Изменение количества использований купона
     *
     * @param int $id
     * @param int $limit
     *
     * @return bool|int
     */
    public function editLimit($id, $limit)
    {
        $id = (int)$id;
        $limit = (int)$limit;

        return $this->updateById($id, array('limit' => $limit));
    }


    /**
     * @param int $id
     * @param string $expire_datetime
     * @return bool|int
     */
    public function editExpireDatetime($id, $expire_datetime)
    {
        $id = (int)$id;
        $expire_datetime = (string)$expire_datetime;

        return $this->updateById($id, array('expire_datetime' => $expire_datetime));
    }

    /**
     * @param int $id
     */
    public function removeCoupon($id)
    {

        /**
         * ОБработка изменений скидки по данному купону в заказах
         */

        return $this->deleteById($id);
    }

    /**
     * Проверка поля type на допустимость
     *
     * @param array | string $data
     * @return bool
     */
    protected function checkFieldType($data)
    {
        $type = '';

        if (is_string($data)) {
            $type = $data;
        } else if (is_array($data)) {
            if (!isset($data['type'])) {
                return false;
            } else {
                $type = $data['type'];
            }
        }


        /**
         * Здесь проверка на соответствие $type знаку процента,
         * или строке кода основной валюты магазина. И возврат логического
         * значения
         */

        if ($type == self::TYPE_COUPON_CURRENCY || $type == self::TYPE_COUPON_PROCENT) {
            return true;
        } else {
            return false;
        }

    }
}