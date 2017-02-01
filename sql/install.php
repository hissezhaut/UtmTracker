<?php

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'quotes` (
		`id_quote` int(10) unsigned NOT NULL AUTO_INCREMENT,
		PRIMARY KEY  (`id_quote`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

foreach ( $sql as $query ) {
	if ( Db::getInstance()->execute( $query ) == false ) {
		return false;
	}
}
