<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

// @see Zend_Queue
$installer->run("
DROP TABLE IF EXISTS `message`;
CREATE TABLE IF NOT EXISTS `message` (
  `message_id` bigint(20) unsigned NOT NULL auto_increment,
  `queue_id` int(10) unsigned NOT NULL,
  `handle` char(32) default NULL,
  `body` varchar(8192) NOT NULL,
  `md5` char(32) NOT NULL,
  `timeout` decimal(14,4) unsigned default NULL,
  `created` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`message_id`),
  UNIQUE KEY `message_handle` (`handle`),
  KEY `message_queueid` (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `queue`
--

DROP TABLE IF EXISTS `queue`;
CREATE TABLE IF NOT EXISTS `queue` (
  `queue_id` int(10) unsigned NOT NULL auto_increment,
  `queue_name` varchar(100) NOT NULL,
  `timeout` smallint(5) unsigned NOT NULL default '30',
  PRIMARY KEY  (`queue_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`queue_id`) REFERENCES `queue` (`queue_id`) ON DELETE CASCADE ON UPDATE CASCADE;
");

$installer->endSetup();


