<?php

$sql = [];

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'orders`
ADD `utm_source` VARCHAR(255) NOT NULL AFTER `dpd`,
ADD `utm_medium` VARCHAR(255) NOT NULL AFTER `utm_source`,
ADD `utm_campaign` VARCHAR(255) NOT NULL AFTER `utm_medium`,
ADD `utm_term` VARCHAR(255) NOT NULL AFTER `utm_campaign`,
ADD `utm_content` VARCHAR(255) NOT NULL AFTER `utm_term`;';

foreach ( $sql as $query ) {
	if ( Db::getInstance()->execute( $query ) == false ) {
		return false;
	}
}
