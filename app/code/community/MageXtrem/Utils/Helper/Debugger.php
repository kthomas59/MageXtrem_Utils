<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 07/06/16
 * Time: 11:39
 */
class MageXtrem_Utils_Helper_Debugger extends Mage_Core_Helper_Abstract
{

    /**
     * @var int
     */
    protected $_current_string_limit = 512;

    /**
     * @var int
     */
    protected $_new_string_limit = 10000;

    /**
     * @var MageXtrem_Utils_Helper_Env
     */
    protected $_env;

    /**
     * @var MageXtrem_Utils_Helper_Logger
     */
    protected $_logger;

    /**
     * @return MageXtrem_Utils_Helper_Env
     */
    public function getEnv()
    {
        if (!$this->_env) {
            $this->setEnv(Mage::helper('magextrem_utils/env'));
        }
        return $this->_env;
    }

    /**
     * @param MageXtrem_Utils_Helper_Env $env
     */
    public function setEnv($env)
    {
        $this->_env = $env;
    }

    /**
     * @return MageXtrem_Utils_Helper_Logger
     */
    public function getLogger()
    {
        if (!$this->_logger) {
            $this->setLogger(Mage::helper('magextrem_utils/logger')->setFilename('magextrem/debugger'));
        }
        return $this->_logger;
    }

    /**
     * @param MageXtrem_Utils_Helper_Logger $logger
     */
    public function setLogger($logger)
    {
        $this->_logger = $logger;
    }

    /**
     * @return MageXtrem_Utils_Helper_Mailer
     */
    public function getMailer()
    {
        if (!$this->_mailer) {
            $this->setMailer(Mage::helper('magextrem_utils/mailer'));
        }
        return $this->_mailer;
    }

    /**
     * @param MageXtrem_Utils_Helper_Mailer $mailer
     */
    public function setMailer($mailer)
    {
        $this->_mailer = $mailer;
    }

    /**
     * @var MageXtrem_Utils_Helper_Mailer
     */
    protected $_mailer;

    /**
     * @param string $msg
     * @param bool $trace
     */
    public function dump($msg, $trace = false)
    {
        if ($this->getEnv()->isProd()) {
            $this->getLogger()->log($msg);
            if ($trace) {
                $this->getLogger()->logTrace();
            }
        } else {
            if (ini_get('xdebug.var_display_max_data') <= $this->_current_string_limit) {
                ini_set('xdebug.var_display_max_data', $this->_new_string_limit);
            }
            Zend_Debug::dump($msg);
            if ($trace) {
                $this->dumpTrace();
            }
        }
    }

    /**
     * @return string
     */
    public function getTrace() {
        try {
            throw new Exception();
        } catch (Exception $e) {
            return $e->getTraceAsString();
        }
    }

    public function dumpTrace() {
        echo '<pre><strong>'.$this->getTrace().'</strong></pre>';
    }

    /**
     * @param string $msg
     * @param bool $trace
     */
    public function shutdown($msg = '', $trace = true)
    {
        $this->dump($msg, $trace);
        if ($this->getEnv()->isLocal() || $this->getEnv()->isDev())
            die;
        $this->getMailer()->send('See log file : ' . $this->getLogger()->getFilename(), '[WARNING] DIE on prod');
    }

    /**
     * @param object $elt
     * @param bool $trace
     */
    public function getClass($elt, $trace = false)
    {
        $this->dump(get_class($elt), $trace);
    }

    /**
     * @param object $elt
     * @param bool $trace
     */
    public function getClassDie($elt, $trace = true)
    {
        $this->shutdown(get_class($elt), $trace);
    }

    /**
     * @param Varien_Object $elt
     * @param string $key
     * @param bool $trace
     */
    public function getData($elt, $key = '', $trace = false)
    {
        if (!is_object($elt)) {
            $this->dump($elt, $trace);
        }
        $this->dump($elt->getData($key), $trace);
    }

    /**
     * @param Varien_Object $elt
     * @param string $key
     * @param bool $trace
     */
    public function getDataDie($elt, $key = '', $trace = true)
    {
        if (!is_object($elt)) {
            $this->shutdown($elt, $trace);
        }
        $this->shutdown($elt->getData($key), $trace);
    }

    /**
     * @param Exception $e
     * @param bool $trace
     */
    public function traceError($e, $trace = true)
    {
        $this->dump($e->getMessage(), $trace);
        if ($trace) {
            $this->dump($e->getTraceAsString(), $trace);
        }
    }

    /**
     * @param Exception $e
     * @param bool $trace
     */
    public function traceErrorDie($e, $trace = true)
    {
        $this->dump($e->getMessage(), $trace);
        if ($trace) {
            $this->dump($e->getTraceAsString(), $trace);
        }
        die;
    }

    /**
     * @param Varien_Data_Collection $collection
     * @param bool $trace
     * @param bool $stringMode
     */
    public function printQuery($collection, $trace = false, $stringMode = true)
    {
        $this->dump(($collection instanceof Varien_Db_Select ?
            $collection->__toString() :
            $collection->getSelectSql($stringMode)
        ), $trace);
    }

    /**
     * @param Varien_Data_Collection $collection
     * @param bool $trace
     * @param bool $stringMode
     */
    public function printQueryDie($collection, $trace = false, $stringMode = true)
    {
        $this->shutdown(($collection instanceof Varien_Db_Select ?
            $collection->__toString() :
            $collection->getSelectSql($stringMode)
        ), $trace);
    }

    /**
     * @param Varien_Data_Collection $collection
     * @param bool $trace
     */
    public function countCollection($collection, $trace = false)
    {
        $this->dump($collection->count(), $trace);
    }

    /**
     * @param Varien_Data_Collection $collection
     * @param bool $trace
     */
    public function countCollectionDie($collection, $trace = false)
    {
        $this->shutdown($collection->count(), $trace);
    }

}