<?php
/**
 * AsyncQueue Helper
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

class Vianetz_AsyncQueue_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Log message to file if enabled in system configuration.
     *
     * @param string $message
     * @param int $type
     *
     * @return Vianetz_AsyncQueue_Helper_Data
     */
    public function log($message, $type = LOG_DEBUG)
    {
        $moduleName = Mage::app()->getRequest()->getModuleName();
        $extensionVersion = Mage::getConfig()->getModuleConfig($moduleName)->version;
        $message = $moduleName . ' v' . $extensionVersion . ': ' . $message;
        $logFilename = $moduleName . '.log';

        Mage::log($message, $type, $logFilename, true);

        return $this;
    }
}