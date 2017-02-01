<?php

$sql = [];

$sql[] = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'quotes`;';

foreach ( $sql as $query ) {
	if ( Db::getInstance()->execute( $query ) == false ) {
		return false;
	}
}
