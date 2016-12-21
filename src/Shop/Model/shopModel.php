<?php
namespace Shop\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;

class ShopModel extends AbstractTableGateway
{
    protected $id = 'id';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();

    }


    /**
     * Указание поля первичного ключа таблицы
     *
     * @param int|array $id
     *
     * @return bool
     */
    public function setId($id)
    {
        if (is_array($id) || is_int($id)) {
            $this->id = $id;
            return true;
        } else {
            return false;
        }
    }


    /**
     * Обновление записи в таблице по первичному ключу
     *
     * @param array|int $input_id
     * @param array $data
     *
     * @return bool|int
     */
    public function updateById($input_id, array $data)
    {
        if (is_array($this->id)) {
            $request_params = array();
            foreach ($this->id as $k => $v) {
                if (is_array($input_id)) {
                    $request_params[$v] = $input_id[$k];
                } else if (is_int($input_id)) {
                    $request_params[$v] = $input_id;
                }
            }

            return $this->updateByField($request_params, $data);
        } else if (is_string($this->id)) {
            return $this->updateByField($this->id, $input_id, $data);
        }

        return false;
    }

    /**
     * Обновление записи в таблице по значению полей
     *
     * @param array|string $fields
     * @param string|array $values
     * @param array|string|null $data
     * @param string $bool_flag
     *
     * @return int
     */
    public function updateByField($fields, $values, $data = NULL, $bool_flag = 'AND')
    {
        $sql = $this->getSql();
        $update = $sql->update();
        $where_args = array();

        if (is_array($values)) {
            foreach ($values as $k => $v) {
                if (is_string($v)) {
                    $values[$k] = $this->escape($v);
                }
            }
        }

        if (is_array($data)) {
            foreach ($data as $k => $v) {
                if (is_string($v)) {
                    $data[$k] = $this->escape($v);
                }
            }
        }

        if (is_array($fields)) {
            $bool_flag = ($data == 'AND' || $data == 'OR') ? $data : 'AND';
            $data = $values;
            $where_args = $fields;
        } else if (is_string($fields)) {
            $where_args = array($fields => $values);
        }

        $update->set($data)->where($where_args, $bool_flag);
        $result = $this->executeUpdate($update);

        return $result;
    }


    /**
     * Возвращает все данные таблицы
     *
     * @param string $field
     * @param bool $normalize
     * @param string $sort_flag
     *
     * @return array
     */
    public function getAll($field = '', $normalize = false, $sort_flag = '')
    {
        $sql = $this->getSql();
        $select = $sql->select();

        //Если задан вариант сортировки
        if ( $sort_flag == 'ASC' ||$sort_flag == 'DESC' ) {
            $select->order('sort '.$sort_flag);
        }

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

    /**
     * Возвращает количество записей в таблице
     *
     * @return int
     */
    public function countAll()
    {
        $result = $this->select()->count();
        return $result;
    }

    /**
     * Получение данных по первичному ключу
     *
     * @param int|array $id
     *
     * @return array
     */
    public function getById($id)
    {
        if (!is_array($this->id)) {
            $result = $this->getByField($this->id, $id);
        } else {
            $values_list = array();

            foreach ($this->id as $k => $v) {
                if (is_int($id)) {
                    $values_list[$v] = $id;
                } else if (is_array($id)){
                    $values_list[$v] = $id[$k];
                }
            }

            $result = $this->getByField($values_list);
        }


        return $result;
    }


    /**
     * Получение запсией по значению полей
     *
     * @param string|array $fields
     * @param mixed|null|array $values
     * @param string|null $limit
     * @param string $bool_flag
     * @param string|null $order_flag
     *
     * @return array
     */
    public function getByField($fields, $values = null, $limit = null, $bool_flag = 'AND', $order_flag = NULL)
    {
        $sql = $this->getSql();
        $select = $sql->select();
        $order_field = '';

        if (is_array($fields)) {
            $order_flag = ($bool_flag == 'ASC' || $bool_flag == 'DESC') ? $bool_flag : NULL;
            $bool_flag = ($limit == 'OR' || $limit == 'AND') ? $limit : 'AND';
            $limit = (int)$values;
            $select->where($fields, $bool_flag);

            if (!empty($order_flag)) {
                foreach ($fields as $k => $v) {
                    $order_field .= $k.' '.$order_flag.', ';
                    
                }
                $order_field = substr($order_field, 0, strlen($order_field) - 2).' ';
            }
        } else if(is_string($fields)) {
            $limit = (int)$limit;
            if (is_array($values)) {
                $where_list = array($fields=>array());
                foreach ($values as $k => $v) {
                    array_push($where_list[$fields], $v);
                }
                $select->where($where_list, 'OR');

            } else {
                $select->where(array($fields => $values));
            }

            $order_field = $fields.' '.$order_flag.' ';
        }

        if (!empty($order_field)) {
            $select->order($order_field);
        }

        if ($limit) {
            $select->limit($limit);
        }

        $result = $this->executeSelect($select)->toArray();

        return $result;
    }

    /**
     * Удаление записи по первичному ключу
     *
     * @param $input_id
     *
     * @return int
     */
    public function deleteById($input_id)
    {

        $result = 0;

        if (!is_array($this->id)) {
            if (is_array($input_id)) {
                $values_list = array($this->id => $input_id);
                $result = $this->deleteByField($values_list);
            } else if (is_int($input_id)) {
                $result = $this->deleteByField($this->id, $input_id);
            }
        } else {
            $values_list = array();

            foreach ($this->id as $k => $v) {
                $values_list[$v] = (is_array($input_id)) ? $input_id[$k] : $input_id;
            }

            $result = $this->deleteByField($values_list, 'AND');
        }

        return $result;
    }

    /**
     * Удаление записи по значению полей
     *
     * @param string|array $field
     * @param mixed $value
     *
     * @return int
     */
    public function deleteByField($field, $value = null)
    {
        $sql = $this->getSql();
        $delete = $sql->delete();

        if (is_string($field)) {
            $delete->where(array($field => $value));
        } else if (is_array($field)){
            $bool_flag = ($value == 'AND' || $value == 'OR') ? $value : 'OR';
            $delete->where($field, $bool_flag);
        }

        $result = $this->executeDelete($delete);

        return $result;
    }

    /**
     * @param string $input_str
     *
     * @return string
     */
    protected function escape($input_str)
    {
       return addslashes(htmlspecialchars(trim($input_str)));
    }

    /**
     * @param string $input_str
     *
     * @return string
     */
    protected function escapeField($input_str)
    {
        return "`".$input_str."`";
    }

}