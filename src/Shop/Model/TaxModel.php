<?php
namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class TaxModel extends shopModel
{
    /**
     * @var array - тип расчёта налога
     */
    protected $address_type = array(
        'billing', //Адрес плательщика
        'shipping' //Адрес доставки
    );

    public function __construct(Adapter $adapter)
    {
        $this->table = 'Tax';
        parent::__construct($adapter);
    }

    /**
     * Получение данных о налоге по его наименованию
     *
     * @param string $input_name
     * @return array
     */
    public function getTaxByName($input_name)
    {
        $input_name = (string)$input_name;
        $input_name = $this->escape($input_name);

        $result = $this->getByField('name', $input_name);

        return $result;
    }

    /**
     * Задать новый налог
     *
     * @param array(string 'name', int | bool 'included', string 'address_type') $input_data
     * @return int
     * @throws \Exception
     */
    public function setTax(array $input_data)
    {
        try{
            $this->inputDataValidation($input_data);
            return $this->insert($input_data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Валидация входных данных
     *
     * @param array $input_data
     * @throws \Exception
     */
    private function inputDataValidation(array $input_data)
    {
        if (!isset($input_data['name'])) {
            throw new \Exception('Не задано наименование налога!');
        } else  {
            $input_data['name'] = (string)$input_data['name'];

            if (empty($input_data['name']) || (string)$input_data['name'] == ' ') {
                throw new \Exception('Задано некорректное наименование налога!');
            }
        }

        if (!isset($input_data['included'])) {
            throw new \Exception('Не задан тип налога');
        } else {
            $input_data['included'] = (int)$input_data['included'];

            if ($input_data['included'] !== 0 && $input_data['included'] !== 1) {
                throw new \Exception('Задан некорректный тип налога!');
            }
        }

        if (!isset($input_data['address_type'])) {
            throw new \Exception('Не задан тип расчёта налога!');
        } else {
            $input_data['address_type'] = (string)$input_data['address_type'];

            if (!in_array($input_data['address_type'], $this->address_type)) {
                throw new \Exception('Задан некорректный тип расчёта налога!');
            }
        }

    }


}