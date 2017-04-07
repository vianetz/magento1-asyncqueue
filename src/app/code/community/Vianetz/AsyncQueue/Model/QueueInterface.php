<?php
/**
 * AsyncQueue Queue Interface
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
 */

interface Vianetz_AsyncQueue_Model_QueueInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @param Zend_Queue_Message $message
     *
     * @return Vianetz_AsyncQueue_Model_QueueInterface
     */
    public function run(Zend_Queue_Message $message);
}