<?php
namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class SettingsModel extends shopModel
{
    private $fields = [];

    public function __construct(Adapter $adapter)
    {
        $this->table = 'Shop_Settings';
        $this->id = 'name';
        parent::__construct($adapter);
        $this->init();
    }

    /**
     * Получение значения настройки
     *
     * @param string $name
     * @return array
     */
    public function get($name = 'all')
    {
        $name = $this->escape((string)$name);

        if ($name != 'all') {
            $result = $this->getById($name);
        } else {
            $result = $this->getAll();
        }

        return $result;
    }


    /**
     * Установка/обновление значения настройки
     *
     * @param string $name
     * @param string $value
     * @return bool|int
     */
    public function set($name, $value)
    {
        $name = $this->escape((string)$name);
        $value = $this->escape((string)$value);

        if (!in_array($name, $this->fields)) {
            return $this->insert(array('name' => $name, 'value' => $value));
        } else {
            return $this->updateById($name, $value);
        }
    }

    /**
     * Инициализируем список существующих настроек
     */
    private function init()
    {
        $query = $this->getAll();

        foreach ($query as $item) {
            $this->fields[] = $item['name'];
        }
    }
}