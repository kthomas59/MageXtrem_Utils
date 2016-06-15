<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 08/06/16
 * Time: 11:10
 */

require_once Mage::getModuleDir('controllers', 'MageXtrem_Utils') . DS . 'Adminhtml' . DS . 'Utils' . DS . 'AbstractController.php';

class MageXtrem_Utils_Adminhtml_Utils_MailerController extends MageXtrem_Utils_Adminhtml_Utils_AbstractController
{

    public function _construct()
    {
        parent::_construct();
        $this->_helper = Mage::helper('magextrem_utils/mailer');
    }

}