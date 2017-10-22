<?php
/**
 * AsyncQueue Message Interface
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

interface Vianetz_AsyncQueue_Model_MessageInterface
{
    /**
     * @param \Zend_Queue_Message $message
     *
     * @return \Vianetz_AsyncQueue_Model_MessageInterface
     */
    public function import(Zend_Queue_Message $message);

    /**
     * @return array
     */
    public function getParameters();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function toString();

    /**
     * @return boolean
     */
    public function validate();

    /**
     * @return \Vianetz_AsyncQueue_Model_MessageInterface
     */
    public function execute();
}