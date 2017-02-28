<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class StorageModel extends SortableModel
{
    const CRITICAL_NUMBER = 2;

    public function __construct(Adapter $adapter)
    {
        $this->table = 'Storage';
        parent::__construct($adapter);
    }

    /**
     * Добавление нового склада
     *
     * @param array $data
     * @return bool
     */
    public function addStorage(array $data)
    {
        if (empty($data)) {
            return false;
        }

        if (!isset($data['critical_count']) || ( $data['critical_count'] !== 0 && empty($data['critical_count']))) {
            $data['critical_count'] = self::CRITICAL_NUMBER;
        }

        $sort = $this->adapter->query("SELECT MAX(sort) FROM {$this->table}", Adapter::QUERY_MODE_EXECUTE);
        $sort = $sort->toArray();
        $sort = array_shift($sort[0]) + 1;

        $data['sort'] = $sort;
        $result = $this->insert($data);

        return (bool)$result;

    }

    /**
     * Удаление счётчика
     *
     * @param int $stock_id
     *
     * @return bool
     */
    public function deleteStorage($stock_id)
    {
        $input_id = (int)$stock_id;
        $result = $this->deleteById($input_id);
        return (bool)$result;
    }


    /**
     * Обновление данных счётчика
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function editStorage($id, array $data)
    {
        $id = (int)$id;
        $result = $this->updateById($id, $data);

        return (bool)$result;
    }

    /**
     * @param int $storage_id
     *
     * @return bool
     */
    protected function exists($storage_id)
    {
       return !!count($this->getById((int)$storage_id));
    }
}