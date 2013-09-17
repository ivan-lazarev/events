<?php

header('Content-Type: text/html; charset=utf-8');

include_once dirname(__FILE__).'/class/hierarchy.php';
include_once dirname(__FILE__).'/class/data_base.php';
include_once dirname(__FILE__).'/config.php';

$test = data_base::get_connection();
$sql = 'SELECT * FROM category ORDER BY parent_id, number';
$test->select_query($sql);
$data = $test->get_data();
$h = new hierarchy($data, 'parent_id');
$h->create_hierarchy_sequence();