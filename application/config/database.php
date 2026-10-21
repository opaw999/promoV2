<?php
defined('BASEPATH') or exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

// $db['default'] = array(
// 	'dsn'	=> '',
// 	'hostname' => 'localhost',
// 	'username' => 'pis',
// 	'password' => 'itprog2013',
// 	'database' => 'pis',
// 	'dbdriver' => 'mysqli',
// 	'dbprefix' => '',
// 	'pconnect' => FALSE,
// 	'db_debug' => (ENVIRONMENT !== 'production'),
// 	// 'db_debug' => FALSE,
// 	'cache_on' => FALSE,
// 	'cachedir' => '',
// 	'char_set' => 'utf8',
// 	'dbcollat' => 'utf8_general_ci',
// 	'swap_pre' => '',
// 	'encrypt' => FALSE,
// 	'compress' => FALSE,
// 	'stricton' => FALSE,
// 	'failover' => array(),
// 	'save_queries' => TRUE
// );

// $db['timekeeping'] = array(
// 	'dsn'	   => '',
// 	'hostname' => 'localhost',
// 	'username' => 'timekeeping',
// 	'password' => 'timekeeping2022',
// 	'database' => 'timekeeping',
// 	'dbdriver' => 'mysqli',
// 	'dbprefix' => '',
// 	'pconnect' => FALSE,
// 	'db_debug' => (ENVIRONMENT !== 'production'),
// 	// 'db_debug' => FALSE,
// 	'cache_on' => FALSE,
// 	'cachedir' => '',
// 	'char_set' => 'utf8',
// 	'dbcollat' => 'utf8_general_ci',
// 	'swap_pre' => '',
// 	'encrypt' => FALSE,
// 	'compress' => FALSE,
// 	'stricton' => FALSE,
// 	'failover' => array(),
// 	'save_queries' => TRUE
// );


$db['default'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost:3307',
	'username' => 'pis',
	'password' => 'itprog2013',
	'database' => 'pis',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['timekeeping'] = array(
	'dsn'	=> '',
	'hostname' => 'localhost:3307',
	'username' => 'timekeeping',
	'password' => 'itprog2013',
	'database' => 'timekeeping',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);





$db['talibon'] = array(
	'dsn'	=> '',
	'hostname' => '172.16.90.220',
	'username' => 'root',
	'password' => 'itprog2013',
	'database' => 'pis',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);

$db['tubigon'] = array(
	'dsn'	=> '',
	'hostname' => '172.16.96.1',
	'username' => 'hrms',
	'password' => 'itprog2013',
	'database' => 'pis',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE
);
