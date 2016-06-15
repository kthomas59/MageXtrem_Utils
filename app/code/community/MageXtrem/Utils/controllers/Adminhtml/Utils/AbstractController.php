<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 08/06/16
 * Time: 11:20
 */
class MageXtrem_Utils_Adminhtml_Utils_AbstractController extends Mage_Adminhtml_Controller_Action
{

    /**
     * @var Mage_Core_Helper_Abstract
     */
    protected $_helper;

    /**
     * @var string
     */
    private $param = 'test message';

    /**
     * @return string
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param string $param
     * @return $this
     */
    public function setParam($param)
    {
        $this->param = $param;
        return $this;
    }

    public function testAction() {
        if (!($function = $this->getRequest()->getParam('function'))) {
            Zend_Debug::dump(Mage::helper('magextrem_utils')->__('Missing mandatory param function'));
        } else {
            try {
                $result = call_user_func(array($this->_helper, $function), $this->getRequest()->getParam('param', $this->param));
                if ($result !== null) {
                    Zend_Debug::dump($result);
                }
            } catch (Exception $e) {
                Mage::helper('magextrem_utils/debugger')->traceError($e);
            }
        }
    }

}