<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 07/06/16
 * Time: 11:39
 */
class MageXtrem_Utils_Helper_Env extends Mage_Core_Helper_Abstract
{

    const LOCAL_ADDRESS_XML_PATH = 'magextrem_utils/environment/local_address';
    const DEV_ADDRESS_XML_PATH = 'magextrem_utils/environment/dev_address';
    const PREPROD_ADDRESS_XML_PATH = 'magextrem_utils/environment/preprod_address';
    const PROD_ADDRESS_XML_PATH = 'magextrem_utils/environment/prod_address';
    const LOCAL_HOST_XML_PATH = 'magextrem_utils/environment/local_host';
    const DEV_HOST_XML_PATH = 'magextrem_utils/environment/dev_host';
    const PREPROD_HOST_XML_PATH = 'magextrem_utils/environment/preprod_host';
    const PROD_HOST_XML_PATH = 'magextrem_utils/environment/prod_host';

    /**
     * @return array
     */
    public function getServer()
    {
        return Mage::getSingleton('core/app')->getRequest()->getServer();
    }

    /**
     * @return string
     */
    public function getHost()
    {
        $server = $this->getServer();
        if (isset($server['HTTP_HOST']))
            return $server['HTTP_HOST'];
        return '';
    }

    /**
     * @return string
     */
    public function getIP()
    {
        $server = $this->getServer();
        if (isset($server['REMOTE_ADDR']))
            return $server['REMOTE_ADDR'];
        return '';
    }

    /**
     * @return string
     */
    public function getPhpSelf()
    {
        $server = $this->getServer();
        if (isset($server['PHP_SELF']))
            return $server['PHP_SELF'];
    }

    /**
     * @return bool
     */
    public function isCron()
    {
        return strpos($this->getPhpSelf(), 'prod') !== false && strpos($this->getPhpSelf(), 'cron') !== false;
    }

    /**
     * @param string $environment
     * @return string
     */
    public function getHostConfig($environment) {
        return Mage::getStoreConfig(constant('self::'.strtoupper($environment).'_HOST_XML_PATH'));
    }

    /**
     * @param string $environment
     * @return string
     */
    public function getIpConfig($environment) {
        return Mage::getStoreConfig(constant('self::'.strtoupper($environment).'_ADDRESS_XML_PATH'));
    }

    /**
     * @param string $environment
     * @return bool
     */
    public function is($environment) {
        return strpos($this->getHost(), $this->getHostConfig($environment)) !== false && strpos($this->getIP(), $this->getIpConfig($environment)) !== false;
    }

    /**
     * @return bool
     */
    public function isLocal()
    {
        return $this->is('local');
    }

    /**
     * @return bool
     */
    public function isDev()
    {
        return $this->is('dev');
    }

    /**
     * @return bool
     */
    public function isPreprod()
    {
        return $this->is('preprod');
    }

    /**
     * @return bool
     */
    public function isProd()
    {
        return $this->is('prod');
    }

    /**
     * @return bool
     */
    public function isOe()
    {
        return $this->isPreprod() || $this->isProd();
    }

}