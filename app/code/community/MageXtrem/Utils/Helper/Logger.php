<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 07/06/16
 * Time: 11:39
 */
class MageXtrem_Utils_Helper_Logger extends Mage_Core_Helper_Abstract
{

    /**
     * @var bool
     */
    protected $_active = true;

    /**
     * @var string
     */
    protected $_filename = 'magextrem/default';

    /**
     * @var MageXtrem_Utils_Helper_Debugger
     */
    protected $_debugger;

    /**
     * @return MageXtrem_Utils_Helper_Debugger
     */
    public function getDebugger()
    {
        if (!$this->_debugger) {
            $this->setDebugger(Mage::helper('magextrem_utils/debugger'));
        }
        return $this->_debugger;
    }

    /**
     * @param MageXtrem_Utils_Helper_Debugger $debugger
     */
    public function setDebugger($debugger)
    {
        $this->_debugger = $debugger;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->_filename;
    }

    /**
     * @param string $filename
     * @return $this
     */
    public function setFilename($filename)
    {
        $this->_filename = $filename;
        return $this;
    }

    /**
     * @param string $msg
     * @param int $level
     * @param string $format
     * @param bool $forceLog
     */
    public function log($msg, $level = Zend_Log::DEBUG, $format = null, $forceLog = false)
    {
        if ($this->_active) {
            $filename = strtolower($this->getFilename());
            if (strpos($filename, '.log') === false)
                $filename .= '.log';

            if (!Mage::getConfig()) {
                return;
            }

            try {
                $logActive = Mage::getStoreConfig('dev/log/active');
                if (empty($filename)) {
                    $filename = Mage::getStoreConfig('dev/log/file');
                }
            } catch (Exception $e) {
                $logActive = true;
            }

            if (!Mage::getIsDeveloperMode() && !$logActive && !$forceLog) {
                return;
            }

            static $loggers = array();

            $level = is_null($level) ? Zend_Log::DEBUG : $level;
            $filename = empty($filename) ? 'system.log' : $filename;

            if (!isset($loggers[$filename])) {
                $logDir = Mage::getBaseDir('var') . DS . 'log';
                if (strpos($filename, '/') !== false) {
                    $exploded_filename = explode('/', $filename);
                    $logDir .= DS . $exploded_filename[0];
                    $filename = $exploded_filename[1];
                }
                $logFile = $logDir . DS . $filename;

                if (!is_dir($logDir)) {
                    mkdir($logDir);
                    chmod($logDir, 0777);
                }

                if (!file_exists($logFile)) {
                    file_put_contents($logFile, '');
                    chmod($logFile, 0777);
                }

                if (is_null($format)) {
                    $format = '%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL;
                }

                $formatter = new Zend_Log_Formatter_Simple($format);
                /** @var Zend_Log_Writer_Stream $writerModel */
                $writerModel = (string)Mage::getConfig()->getNode('global/log/core/writer_model');
                if (!Mage::app() || !$writerModel) {
                    $writer = new Zend_Log_Writer_Stream($logFile);
                } else {
                    $writer = new $writerModel($logFile);
                }
                $writer->setFormatter($formatter);
                $loggers[$filename] = new Zend_Log($writer);
            }

            if (is_array($msg) || is_object($msg)) {
                $msg = print_r($msg, true);
            }

            /** Zend_Log */
            $loggers[$filename]->log($msg, $level);
        }
    }

    public function logTrace() {
        if ($this->_active) {
            $this->log($this->getDebugger()->getTrace());
        }
    }

    /**
     * @param string $msg
     * @param int $level
     */
    public function logWithTrace($msg, $level=Zend_Log::DEBUG) {
        if ($this->_active) {
            $this->log($msg, $level);
            $this->logTrace();
        }
    }

    /**
     * @param Exception $e
     * @param int $level
     */
    public function logException(Exception $e, $level=Zend_Log::ERR) {
        if ($this->_active) {
            $this->log($e->getMessage(), $level);
            $this->log($e->getTraceAsString(), $level);
        }
    }

}