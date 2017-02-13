<?php

$sql = [];

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'orders`
ADD `utm_source` VARCHAR(255) NOT NULL AFTER `dpd`,
ADD `utm_medium` VARCHAR(255) NOT NULL AFTER `utm_source`,
ADD `utm_campaign` VARCHAR(255) NOT NULL AFTER `utm_medium`,
ADD `utm_term` VARCHAR(255) NOT NULL AFTER `utm_campaign`,
ADD `utm_content` VARCHAR(255) NOT NULL AFTER `utm_term`;';

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'orders` CHANGE `utm_source` `utm_source` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `utm_medium` `utm_medium` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `utm_campaign` `utm_campaign` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `utm_term` `utm_term` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `utm_content` `utm_content` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;';

foreach ( $sql as $query ) {
	if ( Db::getInstance()->execute( $query ) == false ) {
		return false;
	}
}
