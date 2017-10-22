<?php
/**
 * AsyncQueue Message Model
 *
 * @section LICENSE
 * This file is created by vianetz <info@vianetz.com>.
 * The Magento module is distributed under a commercial license.
 * Any redistribution, copy or direct modification is explicitly not allowed.
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@vianetz.com so we can send you a copy immediately.
 *
 * @category    Vianetz
 * @package     Vianetz_AsyncQueue
 * @author      Christoph Massmann, <C.Massmann@vianetz.com>
 * @link        http://www.vianetz.com
 * @copyright   Copyright (c) since 2006 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE
 */

class Vianetz_AsyncQueue_Model_Message implements Vianetz_AsyncQueue_Model_MessageInterface
{
    /**
     * @var array
     */
    protected $messageData;

    /**
     * @param \Zend_Queue_Message $message
     *
     * @return $this
     */
    public function import(Zend_Queue_Message $message)
    {
        try {
            $this->messageData = unserialize($message->body);
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
}