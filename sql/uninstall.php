<?php

$sql = [];

$sql[] = 'ALTER TABLE `' . _DB_PREFIX_ . 'orders`
DROP `utm_source`,
DROP `utm_medium`,
DROP `utm_campaign`,
DROP `utm_term`,
DROP `utm_content`;';

foreach ( $sql as $query ) {
	if ( Db::getInstance()->execute( $query ) == false ) {
		return false;
	}
}
