<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class CurrencyModel extends ShopModel
{
    public function __construct(Adapter $adapter)
    {
        $this->table = 'Currency';
        parent::__construct($adapter);
    }

    /**
     * Получаем курс валюты по коду валюты
     *
     * @param string $currency_code
     *
     * @return doouble
     */
    
   
    public function getRate($currency_code=null)
    {   
                  
        $sql = $this->getSql();
        $select = $sql->select();
        if (is_string($currency_code)){
            $select->columns(array('rate'))->where(array("code"=>$currency_code));
            $result = $this->executeSelect($select);

        return $result->toArray();
        }
        else {
            throw new \Exception("Передан не верный параметр $currency_code");
        }
    }

    /**
     * Конвертация цены на $rate_from/$rate_to
     *
     * @param double $price
     * @param double $rate_from
     * @param double $rate_to
     *
     * @return double;
     */
    public function convertByRate($price, $rate_from, $rate_to)
    {
        if ($rate_from == $rate_to) {
            return $price;
        }
        $rate_from = (float) $rate_from;
        $rate_to   = (float) $rate_to;
        return ($price * $rate_from) / $rate_to;
    }


    /**
     * Конвертация цены из валюты $code_from в
     * валюту $code_to
     *
     * @param double $price
     * @param string $code_from
     * @param string $code_to
     *
     * @return double;
     */
    public function convertByCode($price, $code_from, $code_to)
    {
        $comparison = strcasecmp($code_from, $code_to);
        if ($comparison == 0) {
            return $price;
        }
        $sql = $this->getSql();
        $select = $sql->select();
        
        $select->columns(array('rate', 'code'))->where(array("code"=>array($code_from, $code_to)));
        
        $result = $this->executeSelect($select);
        $result = $result->toArray();
        if (count($result) == 2){
        $tmp_result = array();
            foreach ($result as $key => $value){    
                $tmp_result[$value['code']] = $value['rate'];
            }
        $from = $tmp_result[$code_from];
        $to = $tmp_result[$code_to];;
        
        return $this->convertByRate($price, $from, $to);
        }
        else {
            return false;
        }
    }

    /**
     * Удаление валюты по коду
     *
     * @param string $currency_code
     * @param bool $convert_flag
     *
     * @return int
     */
    public function removeCurrency($currency_code, $convert_flag = true)
    {
        
    }

    /**
     * Добавление валюты
     *
     * @param array|string $currency_code
     * Если array, то, например ('code' => 'GBR', 'rate' => 65, ['sort' => 5])
     * если string, то, 'GBR'
     *
     * return bool
     */
    public function addCurrency($input_currency)
    {
        $sort = null;
        $params_query = array();
        $count = $this->countAll();
        if ($count > 0){
            $tmp_result = $this->adapter->query("SELECT MAX(sort) FROM {$this->table}", Adapter::QUERY_MODE_EXECUTE);
            $tmp_result = $tmp_result->toArray();  
            $sort = array_pop($tmp_result[0]);
        }
        if (!is_array($input_currency) && is_string($input_currency)){
            $params_query['code'] = $this->escape($input_currency);
            $params_query['rate'] = 1;
        }
        elseif(is_array($input_currency)) {
            $params_query['code'] = $this->escape($input_currency['code']);
            if (!isset($input_currency['rate'])){
                    $params_query['rate'] = 1;
                }
            else{
                $params_query['rate'] = (double) $input_currency['rate'];
            }    
        }
        
        if (!is_null($sort)){
            $params_query['sort'] = $sort++;
        }
        else {
            $params_query['sort'] = 0;
        }
        //return $params_query;
                
        return $result = $this->insert($params_query);
    }

    /**
     * Возвращает данные об основной валюте
     *
     * @return array
     */
    public function getPrimaryCurrency()
    {

    }

    /**
     * Меняем основную валюту
     *
     * @param string $new_currency
     * @param bool $convert_flag
     */
    public function setPrimaryCurrency($new_currency, $convert_flag = true)
    {

    }

    /**
     * @param string $code
     * @param double $rate
     *
     * return bool
     */
    public function changeRate($code, $rate)
    {

    }


    /**
     * Изменяет порядок сортировки
     *
     * @param string $code
     * @param null|string $before_code
     *
     * return bool
     */
    public function move($code, $before_code = null)
    {

    }

    /**
     * Перерасчёт цен товаров
     *
     * @param string $currency_from
     * @param string|null $rate_from
     * @param string|null $currency_to
     * @param string|null $rate_to
     *
     * @return int
     */
    private function convertProductPrice($currency_from, $rate_from = null, $currency_to = null, $rate_to = null)
    {

    }

    /**
     * Перерасчёт цен для услуг
     *
     * @param string $currency_from
     * @param string|null $rate_from
     * @param string|null $currency_to
     * @param string|null $rate_to
     *
     * @return int
     */
    private function convertServicePrice($currency_from, $rate_from = null, $currency_to = null, $rate_to = null)
    {

    }

}