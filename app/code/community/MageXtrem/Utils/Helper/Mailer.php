<?php

/**
 * Created by PhpStorm.
 * User: kevin
 * Date: 07/06/16
 * Time: 11:39
 */
class MageXtrem_Utils_Helper_Mailer extends Mage_Core_Helper_Abstract
{

    const SENDER_ADDRESS_XML_PATH = 'magextrem_utils/mail/sender';
    const RECIPIENT_ADDRESS_XML_PATH = 'magextrem_utils/mail/recipient';
    const DEBUG_XML_PATH = 'magextrem_utils/mail/debug';

    /**
     * @var string
     */
    private $sender;

    /**
     * @var string
     */
    private $recipient;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @var MageXtrem_Utils_Helper_Env
     */
    protected $_env;

    /**
     * @var MageXtrem_Utils_Helper_Logger
     */
    protected $_logger;

    public function __construct()
    {
        $this->_env = Mage::helper('magextrem_utils/env');
        $this->_logger = Mage::helper('magextrem_utils/logger')->setFilename('magextrem/mailer');
        $this->debug = (bool)Mage::getStoreConfig(self::DEBUG_XML_PATH);
        $this->sender = Mage::getStoreConfig(self::SENDER_ADDRESS_XML_PATH);
        $this->recipient = Mage::getStoreConfig(self::RECIPIENT_ADDRESS_XML_PATH);
    }

    /**
     * @param string $msg
     * @param string $subject
     * @param string $email
     * @param string $name
     * @param array $cc
     * @param array $bcc
     * @param string $attachment
     * @return bool|Zend_Mail
     */
    public function send($msg, $subject = '', $email = '', $name = '', $cc = array(), $bcc = array(), $attachment = '')
    {
        if ($this->_env->isProd() || $this->debug) {
            if (empty($email))
                $email = $this->recipient;
            if (empty($name))
                $name = $email;
            if (empty($bcc))
                $bcc = $this->sender;

            $mail = new Zend_Mail('UTF-8');

            if ($attachment)
                $mail->addAttachment($attachment);

            foreach ($cc as $cc_name => $cc_email) {
                $mail->addCc($cc_email, $cc_name);
            }
            $mail->addBcc($bcc);

            try {
                return $mail->addTo($email, $name)
                    ->setFrom($this->sender)
                    ->setSubject($subject)
                    ->setBodyHtml($msg)
                    ->send();
            } catch (Exception $e) {
                $this->_logger->logException($e);
                return false;
            }
        }
        return false;
    }

    /**
     * Send transactional email to recipient
     *
     * @param   int $templateId
     * @param   string|array $sender sneder informatio, can be declared as part of config path
     * @param   string $email recipient email
     * @param   string $name recipient name
     * @param   array $vars varianles which can be used in template
     * @param   int|null $storeId
     * @return  Mage_Core_Model_Email_Template
     */
    public function sendTransactional($templateId, $sender, $email, $name, $vars = array(), $bcc = array(), $storeId = null)
    {
        if ($this->_env->isProd() || $this->debug) {
            try {
                /** @var @var Mage_Core_Model_Email_Template $email_template */
                $email_template = Mage::getModel('core/email_template');
                if (empty($bcc))
                    $bcc = $this->sender;
                $email_template->addBcc($bcc);

                return $email_template->setDesignConfig(array('area' => 'frontend'))
                    ->sendTransactional(
                        $templateId,
                        $sender,
                        $email,
                        $name,
                        $vars,
                        $storeId
                    );
            } catch (Exception $e) {
                $this->_logger->logException($e);
                return false;
            }
        }
        return false;
    }

}