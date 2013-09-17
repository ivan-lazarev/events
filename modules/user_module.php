<?php
include_once 'management_module.php';

class user_module extends management_module {
	
	protected function init() {
		$this->table_name = 'user';
		$this->sql = 'SELECT id, login FROM user';
		$this->headers = array(
				'Логин' => 'login',
				'Удалить' => 'delete',
			);
		$this->fields = array ('login');
	}
	
	
	protected function create_add () {
 		
		$error = '';
 		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
 			$error = $this->check_unique_login();
 			if ($error == 'good'){
 				$this->add();
 			} 					
 		}		
 		$select_type = array(
 				'Оператор' => '1',
 				'Администратор' => '2', 				
 			);	
 		include_once dirname(__FILE__).'/../class/select.php';
 		$select = new select();
 		$this->set('error', $error);
 		$this->set('select_type', $select->create('type', 'form', $select_type, '1'));		  		
 		$this->set('city_name', ''); 	
 		$this->set('action_url', 'admin.php?page=user/add');
 		$this->set('submit_value', 'Добавить');
 		return $this->output(DIR_TEMPLATES.'/form/form_add_user.html'); 		
 	}
 	
 	protected function add () {
 		 		
  		include_once dirname(__FILE__).'/../class/data_base.php';
  		$test = data_base::get_connection();  		
  		$row = array (
  					'login' => $test->escape($_POST['login']),
  					'password' => md5($_POST['password'].')(&$^&%$%#@E#@";'),
  					'type' => $test->escape($_POST['type']),
  				);
  		$test->insert('user',$row);
  		header('Location: admin.php?page=user');
  		exit;
 	}

 	private function check_unique_login() {
 		
 		include_once dirname(__FILE__).'/../class/data_base.php';
  		$test = data_base::get_connection();
  		$test->select_query('SELECT login FROM user WHERE login like "'.$test->escape($_POST['login']).'"');
 		$data = $test->get_row();
 		if (isset($data['login'])){
 			return $this->get_html_error_block('Пользователь с таким логином уже существует');
 		}
 		else {
 			return 'good';
 		}
 	}
 	
 	private function get_html_error_block($error_str){ 		
 		return '<div class="error-group"><div id="error-label">'.$error_str.'</div></div>';
 	}
}
