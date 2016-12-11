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
     * @return double
     */
    public function getRate($currency_code)
    {

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
     * Если array, то, например ('code' => 'GBR', 65, 5)
     * если string, то, 'GBR'
     *
     * return bool
     */
    public function addCurrency($input_currency)
    {

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