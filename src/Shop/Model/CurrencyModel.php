<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class CurrencyModel extends SortableModel
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
     * @return double
     */
    public function getRate($currency_code = null)
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
        $currency_code = (string) $currency_code;
        $pc = $this->getPrimaryCurrency();
        $temp_currency = $this->getByField("code", $currency_code);
        $temp_currency = array_shift($temp_currency);
        if(empty($temp_currency)){
            throw new \Exception(
                    "Не найдена введенная валюта: $currency_code"
                    );
        }
        if ($currency_code !== $pc['code']){
            $this->move($temp_currency['id']);
            /*нужно сделать преобразование в зависимости от $convert_flag
            в следующих таблицах: Products_sku, Services_variant
            */
        }
        
        elseif ($currency_code == $pc['code']){
            
            $new_pc = $this->adapter->query("SELECT MIN(sort) FROM {$this->table} WHERE sort > 0", Adapter::QUERY_MODE_EXECUTE);
            $new_pc = $new_pc->toArray();
            $new_pc = array_shift($new_pc[0]);
            $new_pc = $this->getByField("sort", $new_pc);
            $new_pc = array_shift($new_pc);
            $this->changePrimaryCurrency($new_pc["code"]);
        /*
            обновить цены и коды валют в таблицах:
            Orders, Services_variant, Products
        */
        }
        $this->deleteByField("code", $currency_code);
        
        
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
        $result = $this->getByField(array("sort" => 0));
        return array_shift($result);
    }

    /**
     * Устанавливаем основную валюту и конвертируем цену
     *
     * @param string $new_currency
     * @throws Exception
     * @returns bool
     */
    public function changePrimaryCurrency($new_currency)
    {
        $old_pc = $this->getPrimaryCurrency();
        $new_currency = (string) $new_currency;
        $old_pc_code = $old_pc["code"];
        if (empty($old_pc)){
            throw new \Exception(
                    "Не найдена основная валюта!"
                    );
                    
        }
        elseif ($old_pc_code == $new_currency) {
            return false;
        }
        $new_pc = $this->getByField(array(
            "code" => $this->escape($new_currency),
        ));
        $new_pc = array_shift($new_pc);
        
        if(empty($new_pc)){
             throw new \Exception(
                    "Валюта $new_currency не найдена"
                    );
        }
        $this->adapter->query("UPDATE {$this->table} SET rate = rate/{$new_pc['rate']}", Adapter::QUERY_MODE_EXECUTE);
        
        
        $this->updateById($old_pc["id"], array(
            "sort" => $new_pc["sort"]
        ));
        $this->updateById($new_pc["id"], array(
            "sort" => 0
        ));
        
        
        /*
            обновить цены и коды валют в таблицах:
            Orders, Services_variant, Products
        */
        return true;
    }

    /**
     * @param string $code
     * @param double $rate
     *
     * return bool
     */
    public function changeRate($code, $rate)
    {
        $code = (string) $code;
        $rate = (int) $rate;
        $old_pc = $this->getPrimaryCurrency();
        if ($code == $old_pc["code"]){
            return false;
        }
        $currency = $this->getByField("code", $code);
        if(empty($currency)){
            throw new \Exception(
                    "Валюта не найдена"
                    );
        }
        if($rate <= 0){
            throw new \Exception(
                    "Некорректный курс: $rate"
                    );
        }
        $this->updateById(array_shift($currency)["id"], array(
            "rate" => $rate
        ));
         /*
            обновить цены и коды валют в таблицах:
            Orders, Services_variant, Products
        */
        return true; 
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