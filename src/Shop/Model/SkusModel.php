<?php
namespace Shop\Model;

use Shop\shopRegistry;
use Zend\Db\Adapter\Adapter;

class SkusModel extends ShopModel
{
    public function __construct(Adapter $adapter)
    {
        $this->table = 'Products_sku';
        parent::__construct($adapter);
    }


    /**
     * Удаляем вариации по идентификаторам товаров
     *
     * @param array $product_ids
     * @return bool
     * @throws \Exception
     */
    public function deleteByProducts(array $product_ids)
    {
        //////   Формируем строку условия IN /////////////////
        $str = '(';
        foreach ($product_ids as $kry => $val) {
            $str .= $val . ',';
        }
        $str = substr($str, 0, strlen($str) - 1);
        $str .= ')';
        ///////////////////////////////////////////////////////////////

        $query = $this->adapter->query("SELECT id FROM {$this->table} WHERE Products_id IN {$str}", Adapter::QUERY_MODE_EXECUTE);
        $query = $query->toArray();

        foreach ($query as $item) {
            if(!$this->delete($item['id'], true)) {
                throw new \Exception('Ошибка удаления вариации '.$item['id']);
            }
        }

        return true;
    }


    /**
     * Удаляем вариацию по идентификатору
     *
     * @param int $input_id
     * @return bool
     */
    public function delete($input_id, $drop_flag = false)
    {
        $input_id = (int)$input_id;

        $sku = $this->getById($input_id);

        if (empty($sku)) {
            return false;
        }

        $sku = array_shift($sku);

        $product = $this->adapter->query('SELECT * FROM Products WHERE id = '.
            (int)$sku['products_id'], Adapter::QUERY_MODE_EXECUTE);
        $product = $product->toArray();
        $product = array_shift($product);


        //Если нет товара для данной вариации, то просто убиваем
        //запись в таблице
        if (empty($product)) {
            $this->deleteById($sku['id']);
        }

        $other_skus = $this->getOtherSkusList($sku['products_id'], $input_id);

        //Если у товара только одна вариация, то мы не
        //должны её удалять
        if (empty($other_skus)) {
            if (!$drop_flag) {
                return true;
            } else {
                $this->deleteFromStockInfo($input_id);
                $this->deleteFromFeaturesInfo($input_id);
                return (bool)$this->deleteById($input_id);
            }

        }

        $update = array(
            'sku_type' => (count($other_skus['skus']) > 1) ? 1 : 0,
            'max_price' => $other_skus['max_price'],
            'min_price' => $other_skus['min_price']
        );

        if ($product['sku_id'] == $input_id) {
            $update['sku_id'] = $other_skus['skus'][0]['id'];
            $update['price'] = $other_skus['skus'][0]['price'];
        }

        $update['count'] = $other_skus['count'];

        $product_model = new ProductsModel($this->adapter);

        $product_model->updateById($product['id'], $update);



        /**
         * Удаление данных из всех смежных таблиц
         */

        $result = $this->deleteById($input_id);

        return (bool)$result;
    }

    /**
     * Возвращает данные из таблицы плюс данные
     * по складам
     *
     * @param int $sku_id
     * @return array
     */
    public function getSku($sku_id)
    {
        $sku_id = (int)$sku_id;
        $sku = $this->getById($sku_id);

        /*
         * Может ли один артикул быть у нескольких товаров?
         */

        if (empty($sku)) {
            return array();
        }

        $sku = array_shift($sku);
        $sku['storage'] = array();

        $query = $this->adapter->query("SELECT * FROM Storage_info WHERE sku_id = {$sku_id}", Adapter::QUERY_MODE_EXECUTE);
        $query = $query->toArray();

        if (!empty($query)) {
            foreach ($query as $key => $item) {
                $sku['storage'][$item['storage_id']] = $item['count'];
            }
        }

        return $sku;
    }

    /**
     * Получаем список вариаций, с данными по складам,
     * по идентификатору товара
     *
     * @param int $product_id
     * @return array
     */
    public function getDataByProductId($product_id)
    {
        $product_id = (int)$product_id;
        $result = array();

        $skus = $this->getByField('products_id', $product_id);

        if (!empty($skus)) {
            foreach ($skus as $item) {
                $result[] = $this->getSku($item['id']);
            }
        }

        return $result;
    }

    /**
     * Удаление иформации о вариации из данных складов
     *
     * @param int $sku_id
     */
    public function deleteFromStockInfo($sku_id)
    {
        $sku_id = (int)$sku_id;

        //Заменить на работу метода модели "StorageInfoModel"
        $this->adapter->query("DELETE FROM Storage_info WHERE sku_id = $sku_id", Adapter::QUERY_MODE_EXECUTE);
    }

    /**
     * Удаление информации о вариации из списка характеристик товара
     *
     * @param int $sku_id
     */
    public function deleteFromFeaturesInfo($sku_id)
    {
        $sku_id = (int)$sku_id;

        //Заменить на работу метода модели "ProductFeaturesModel"
        $this->adapter->query("DELETE FROM Product_features WHERE sku_id = $sku_id", Adapter::QUERY_MODE_EXECUTE);
    }

    public function addSku(array $sku_data)
    {
        if (!isset($sku_data['products_id'])) {
            throw new \Exception('Не задан товар для вариации');
        } else if (empty($sku_data['products_id'])) {
            throw new \Exception('Задан некорректный идентификатор товара для вариации');
        }

        //////// Проверяем указание ценовых характеристик////////////////
        if (isset($sku_data['price'])) {
            $sku_data['price'] = (double)$sku_data['price'];
        } else {
            $sku_data['price'] = 0;
        }

        if (isset($sku_data['purchase_price'])) {
            $sku_data['purchase_price'] = (float)$sku_data['purchase_price'];
        } else {
            $sku_data['purchase_price'] = 0;
        }

        if (isset($sku_data['compare_price'])) {
            $sku_data['compare_price'] = (float)$sku_data['compare_price'];
        } else {
            $sku_data['compare_price'] = 0;
        }

        if (!isset($sku_data['currency'])) {
            $registry = shopRegistry::getInstance($this->adapter);
            $sku_data['currency'] = $registry->getSetting('currency');
        }
        ////////////////////////////////////////////////////////////

        if (!isset($sku_data['sku'])) {
            $sku_data['sku'] = '';
        }

        $sku_data['available'] = (isset($sku_data['available'])) ? $sku_data['available'] : 0;



        $this->insert($sku_data);

        return $this->adapter->getDriver()->getLastGeneratedValue();
    }

    /**
     * @param int $product_id
     * @param int $sku_id
     * @return array
     */
    protected function getOtherSkusList($product_id, $sku_id) {
        $result = array();

        $query = $this->adapter->query("SELECT id, price, currency, count FROM {$this->table}
                                  WHERE products_id = {$product_id} AND id != {$sku_id}
                                  AND available = 1", Adapter::QUERY_MODE_EXECUTE);

        $query = $query->toArray();

        if (!empty($query)) {

            $currency_model = new CurrencyModel($this->adapter);
            $registry = shopRegistry::getInstance($this->adapter);
            $prices = array();
            $count = 0;

            $primary_currency = $registry->getSetting('currency');

            foreach ($query as $key => &$item) {
                if ($item['currency'] != $primary_currency) {
                    $item['price'] = $currency_model->convertByCode($item['price'], $item['currency'], $primary_currency);
                }
                array_push($prices, $item['price']);
                if ($count !== null && $item['count'] !== null) {
                    $count += $item['count'];
                } else {
                    $count = null;
                }
            }
            unset($item);

            $result['skus'] = $query;

            $result['min_price'] = min($prices);
            $result['max_price'] = max($prices);
            $result['count'] = $count;
        }

        return $result;
    }


}