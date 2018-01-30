<?php
/**
 * AsyncQueue Main Queue Model
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

abstract class Vianetz_AsyncQueue_Model_Queue implements Vianetz_AsyncQueue_Model_QueueInterface
{
    /**
     * @var array
     */
    protected $registry = array();

    /**
     * @var integer
     */
    protected $numberOfMessagesPerBatch = 5;

    /**
     * Return the queue instance.
     *
     * @return Zend_Queue
     */
    private function getInstance($queueName)
    {
        if (isset($this->registry[$queueName]) === false) {
            $db = @simplexml_load_file(Mage::getBaseDir('etc') . DS . 'local.xml');
            $db = $db->global->resources->default_setup->connection;
            $queueOptions = array(
                Zend_Queue::NAME => $queueName,
                'driverOptions' => array(
                    'host' => $db->host,
                    'port' => $db->port,
                    'username' => $db->username,
                    'password' => $db->password,
                    'dbname' => $db->dbname,
                    'type' => 'pdo_mysql',
                    Zend_Queue::TIMEOUT => 1,
                    Zend_Queue::VISIBILITY_TIMEOUT => 1
                )
            );

            $this->registry[$queueName] = new Zend_Queue('Db', $queueOptions);
        }

        return $this->registry[$queueName];
    }

    /**
     * @param Vianetz_AsyncQueue_Model_QueueInterface $queue
     *
     * @return Vianetz_AsyncQueue_Model_Queue
     * @throws \Zend_Queue_Exception
     */
    public function processQueue(Vianetz_AsyncQueue_Model_QueueInterface $queue)
    {
        $queueInstance = $this->getInstance($queue->getName());
        foreach ($queueInstance->receive($this->numberOfMessagesPerBatch) as $message) {
            try {
                /** @var \Vianetz_AsyncQueue_Model_MessageInterface $messageInstance */
                $messageInstance = $this->convertMessage($message);
                Mage::helper('vianetz_asyncqueue')->log('Processing message: ' . $messageInstance->toString());

                if ($messageInstance->validate() === true) {
                    $messageInstance->execute();
                    $queueInstance->deleteMessage($message);
                }
            } catch (Exception $exception) {
                Mage::helper('vianetz_asyncqueue')->log('Error running queue message for queue ' . $queue->getName() . ': ' . $exception->getMessage());
            }
        }

        return $this;
    }

    /**
     * @param Vianetz_AsyncQueue_Model_MessageInterface $message
     *
     * @return Vianetz_AsyncQueue_Model_Queue
     * @throws \Zend_Queue_Exception
     */
    public function sendToQueue(Vianetz_AsyncQueue_Model_MessageInterface $message)
    {
        $this->getInstance($this->getName())->send($message->toString());

        return $this;
    }

    /**
     * @param \Zend_Queue_Message $queueMessage
     *
     * @return \Vianetz_AsyncQueue_Model_MessageInterface
     * @throws \Vianetz_AsyncQueue_Model_MissingMessageException
     */
    private function convertMessage(Zend_Queue_Message $queueMessage)
    {
        /** @var \Vianetz_AsyncQueue_Model_Message $defaultMessageInstance */
        $defaultMessageInstance = Mage::getModel('vianetz_asyncqueue/message')->import($queueMessage);
        $messageType = $defaultMessageInstance->getType();

        if (empty($messageType) === false && class_exists($messageType) === true) {
            /** @var \Vianetz_AsyncQueue_Model_MessageInterface $concreteMessage */
            $concreteMessage = new $messageType();
            if ($concreteMessage instanceof Vianetz_AsyncQueue_Model_MessageInterface) {
                return $concreteMessage->import($queueMessage);
            }
        }

        throw new Vianetz_AsyncQueue_Model_MissingMessageException('Did not find a valid message class implementation for ' . $messageType);
    }
}