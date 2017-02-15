<?php
namespace Shop;

use Shop\Model\SettingsModel;
use Zend\Db\Adapter\Adapter;

class shopRegistry
{
    private static $instance = null;

    private $settings_model;

    private $settings = array();

    private $init_settings_flag = false;

    private function __construct(Adapter $adapter){
        //Зададим в $this->model объект класса SettingsModel
        $this->settings_model = new SettingsModel($adapter);
    }

    private function __clone(){}

    static public function getInstance(Adapter $adapter)
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($adapter);
        }

        return self::$instance;
    }


    /**
     * Получение требуемой настройки магазина
     *
     * @param string $setting
     * @return string|null
     */
    public function getSetting($setting)
    {
        if (!$this->init_settings_flag) {
            $settings = $this->settings_model->get();

            foreach ($settings as $item) {
                $this->settings[$item['name']] = $item['value'];
            }

            $this->init_settings_flag = true;
        }

        $setting = (string)$setting;

        if (!empty($this->settings) && isset($this->settings[$setting])) {
            return $this->settings[$setting];
        } else {
            return null;
        }
    }

    /**
     * Получение настроек магазина
     *
     * @return array
     */
    public function getSettings()
    {
        if (!$this->init_settings_flag) {
            $settings = $this->settings_model->get();

            foreach ($settings as $item) {
                $this->settings[$item['name']] = $item['value'];
            }

            $this->init_settings_flag = true;
        }

        return $this->settings;
    }

    public function info()
    {
        echo '<pre>';
            print_r($this->settings_model);
        echo '</pre>';
    }
}