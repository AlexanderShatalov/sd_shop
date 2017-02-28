<?php
namespace Shop\Model;

use Zend\Db\Adapter\Adapter;

abstract class SortableModel extends  ShopModel
{
    protected $sort = 'sort';

    public function move($id, $after_id = null) {
        $id = (int)$id;
        $result = 0;

        $target_item = $this->getById($id);
        $target_item = array_shift($target_item);

        if (!$target_item) {
            new \Exception(sprintf("Элемент с идентификатором %d не найден", $id ).__CLASS__.' '.__LINE__);
        }

        if (!empty($after_id)) {
            $after_id = (int)$after_id;

            $after_item = $this->getById($after_id);
            $after_item = array_shift($after_item);

            if (!$after_item) {
                new \Exception(sprintf("Элемент с идентификатором %d не найден", $id ).__CLASS__.' '.__LINE__);
            }

            $sort = $after_item[$this->sort];
        } else {
            $sort = -1;
        }

        if ($sort > $target_item[$this->sort]) {
            $result = $this->adapter->query("UPDATE {$this->table} SET {$this->sort} = {$this->sort} - 1 WHERE
              {$this->sort} <= {$sort} AND {$this->sort} > {$target_item[$this->sort]}", Adapter::QUERY_MODE_EXECUTE);
        } else if ($sort < $target_item[$this->sort]) {
            $sort++;
            $result = $this->adapter->query("UPDATE {$this->table} SET {$this->sort} = {$this->sort} + 1 WHERE
              {$this->sort} >= {$sort} AND {$this->sort} < {$target_item[$this->sort]}", Adapter::QUERY_MODE_EXECUTE);
        }

        $this->updateById($id, array('sort' => $sort));
        return !empty($result);
    }

    public function getAll($field = '', $normalize = false, $sort_flag = '')
    {
        $sql = $this->getSql();
        $select = $sql->select();

        $sort_flag = ($sort_flag == 'ASC' || $sort_flag == 'DESC') ? $sort_flag : 'ASC';
        //Если задан вариант сортировки
        $select->order($this->sort.' '.$sort_flag);

        $result = $this->executeSelect($select)->toArray();

        $tmp_item = $result[0];
        $keys_list = array_keys($tmp_item);

        if (!empty($field) && in_array($field, $keys_list) !== false) {
            $tmp_result = array();

            foreach ($result as $item) {
                $tmp_result[$item[$field]] = $item;
            }

            //Если включена нормализация
            if ((bool)$normalize) {
                foreach ($tmp_result as $key => &$val) {
                    unset($val[$field]);
                }
                unset($val);
            }

            $result = $tmp_result;
        }
        return $result;
    }
}