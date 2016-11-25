<?php
/**
 * AsyncQueue Main Queue Model
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
 * @copyright   Copyright (c) 2006-16 vianetz - C. Massmann (http://www.vianetz.com)
 * @license     http://www.gnu.org/licenses/gpl-2.0.txt GNU GENERAL PUBLIC LICENSE
 * @version     %%MODULE_VERSION%%
 */

class Vianetz_AsyncQueue_Model_Queue extends Mage_Core_Model_Abstract
{
    /**
     * @var array
     */
    protected $registry = array();

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
     */
    public function processQueue(Vianetz_AsyncQueue_Model_QueueInterface $queue)
    {
        $queueInstance = $this->getInstance($queue->getName());
        foreach ($queueInstance->receive() as $message) {
            try {
                $queue->run($message);
                $queueInstance->deleteMessage($message);

            } catch (Exception $exception) {
                Mage::helper('vianetz_asyncqueue')->log('Error running queue message for queue ' . $queue->getName() . ': ' . $exception->getMessage());
            }
        }

        return $this;
    }

    /**
     * @param Vianetz_AsyncQueue_Model_QueueInterface $queue
     * @param string|array|object $message
     *
     * @return Vianetz_AsyncQueue_Model_Queue
     */
    public function sendToQueue(Vianetz_AsyncQueue_Model_QueueInterface $queue, $message)
    {
        if (is_array($message) === true || is_object($message) === true) {
            $message = serialize($message);
        }

        $this->getInstance($queue->getName())->send($message);

        return $this;
    }
}