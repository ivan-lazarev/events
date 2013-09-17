<?php
error_reporting(E_ALL);

include_once dirname(__FILE__).'/config.php';

session_start();

date_default_timezone_set('Asia/Novosibirsk');

header('Content-Type: text/html; charset=utf-8');

$body = '';
$page = 'event';
$action = '';
$action_id = '';

if (isset($_GET['page'])) {
	$pars = explode('/', $_GET['page']);
	if (isset($pars[0])) {
		$page = $pars[0];
	}
	if (isset($pars[1])) {
		$action = $pars[1];
	}
	if (isset($pars[2])) {
		$action_id = $pars[2];
	}
}
if ((!isset($_SESSION['event_admin_id'])) || ($_SESSION['event_admin_id'] == 0)) {
	$page='login';
}

switch($page) {
	case 'login' :
		include_once 'modules/login_module.php';
		$object = new login_module();
		$object->setType('admin');
		$body = $object->render();
		break;
	case 'logout' :
		unset($_SESSION['event_admin_id']);
		unset($_SESSION['event_admin_login']);
		unset($_SESSION['event_admin_type']);
		header('Location: admin.php?page=login');
		exit;
		break;
	default :
		include_once 'modules/main_module.php';
		$object = new main_module();
		$object->set_page($page);
		$object->set_action($action, $action_id);
		$body = $object->render();
		break;
}
echo $body;
?>