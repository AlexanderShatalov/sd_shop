<?php

namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

class StorageModel extends ShopModel
{
    const CRITICAL_NUMBER = 2;

    public function __construct(Adapter $adapter)
    {
        $this->table = 'Storage';
        parent::__construct($adapter);
    }

    /**
     * Изменение сортировочного коэффициента
     *
     * @param int $id
     * @param int|null $before_id
     * @return bool
     */
    public function move($id, $before_id = null)
    {
        $id = (int)$id;
        $exists = $this->exists($id);
        $result = true;

        if (!$exists) {
            return false;
        }

        if (!$before_id) {
            $sort = $this->adapter->query('SELECT id,sort FROM Storage WHERE sort = (SELECT MAX(sort) FROM Storage)', Adapter::QUERY_MODE_EXECUTE);
            $sort = $sort->toArray();
            $max_id = array_shift($sort[0]);

            if ($id != $max_id) {
                $sort = array_shift($sort[0]);
                $this->updateById($id, array('sort'=>$sort));
                $result = $this->adapter->query("UPDATE Storage SET sort = sort - 1 WHERE sort > 1 AND sort <= $sort AND id <> $id", Adapter::QUERY_MODE_EXECUTE);
            }
        } else {
            $before_id = (int)$before_id;

            $sort = $this->adapter->query("SELECT id, sort FROM Storage WHERE id in ($id, $before_id)", Adapter::QUERY_MODE_EXECUTE);
            $sort = $sort->toArray();

            if (empty($sort) || count($sort) != 2) {
                return false;
            }

            foreach ($sort as $key => $val) {
                if ($val['id'] == $before_id) {
                    $new_sort = $val['sort'];
                    break;
                }
            }

            foreach ($sort as $key => $val) {
                if ($val['id'] == $id) {
                    $target_sort = $val['sort'];
                    break;
                }
            }

            $this->updateById($id, array('sort' => $new_sort));

            if ($target_sort > $new_sort) {
                $this->adapter->query("UPDATE Storage SET sort = sort + 1 WHERE  sort >= $new_sort AND id <> $id" , Adapter::QUERY_MODE_EXECUTE);
            } else if ($target_sort < $new_sort) {
                $this->adapter->query("UPDATE Storage SET sort = sort + 1 WHERE  sort >= $new_sort AND id <> $id" , Adapter::QUERY_MODE_EXECUTE);
                $this->adapter->query("UPDATE Storage SET sort = sort - 1 WHERE sort > $target_sort AND sort < $new_sort", Adapter::QUERY_MODE_EXECUTE);
            }
        }
        return !empty($result);
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