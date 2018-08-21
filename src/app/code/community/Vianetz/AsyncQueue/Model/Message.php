<?php
/**
 * AsyncQueue Message Model
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The code is distributed under the GPL license.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz\AsyncQueue
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) since 2006 vianetz - Dipl.-Ing. C. Massmann (http://www.vianetz.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.txt GNU GENERAL PUBLIC LICENSE
 */

class Vianetz_AsyncQueue_Model_Message implements Vianetz_AsyncQueue_Model_MessageInterface
{
    /**
     * The lifetime of a message in minutes. After this amount of time the message will be deleted even if not processed.
     *
     * @var integer
     */
    protected $messageLifetimeInMinutes = 1440;

    /**
     * @var array
     */
    protected $messageData;

    /**
     * @var \Zend_Date
     */
    protected $createdAt;

    /**
     * @param \Zend_Queue_Message $message
     *
     * @return $this
     */
    public function import(Zend_Queue_Message $message)
    {
        try {
            $this->messageData = unserialize($message->body);
            $this->createdAt = new Zend_Date($message->created, Zend_Date::TIMESTAMP);
        } catch (Exception $exception) {
            Mage::helper('vianetz_asyncqueue')->log('Unable to unserialize queue message: ' . $exception->getMessage(), LOG_ERR);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->messageData;
    }

    /**
     * @return mixed|null
     */
    public function getType()
    {
        if (isset($this->messageData['type']) === false) {
            return null;
        }

        return $this->messageData['type'];
    }

    /**
     * @param array $customParameters
     *
     * @return $this
     */
    public function setCustomParameters(array $customParameters)
    {
        $this->messageData['customParameters'] = $customParameters;
        return $this;
    }

    /**
     * @return array
     */
    public function getCustomParameters()
    {
        if (isset($this->messageData['customParameters']) === false) {
            return array();
        }

        return $this->messageData['customParameters'];
    }

    /**
     * @return string
     */
    public function toString()
    {
        $this->messageData['type'] = get_class($this);
        try {
            $messageData = serialize($this->messageData);
        } catch (Exception $exception) {
            Mage::helper('vianetz_asyncqueue')->log('Unable to serialize queue message: ' . $exception->getMessage(), LOG_ERR);
            $messageData = '';
        }

        return $messageData;
    }

    /**
     * @return \Zend_Date
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * @return $this
     */
    public function execute()
    {
        return $this;
    }

    /**
     * @return boolean
     */
    public function isExpired()
    {
        $now = Zend_Date::now();
        $diffInSeconds = $now->sub($this->getCreatedAt())->toValue();

        return ($diffInSeconds >= $this->messageLifetimeInMinutes*60);
    }
}