<?php

include_once dirname(__FILE__).'/../class/data_base.php';
date_default_timezone_set('Asia/Novosibirsk');

class login_module {
	
	private $type = null;
	protected $error_str = '';
	protected $loginform = '';
	private $rez = null;

	public function setType ($t){
		$this->type = $t;
	}
	
	public function render() {
	
		$html = file_get_contents(dirname(__FILE__).'/../templates/login.html');
	
		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
			$this->input_test_data();
		}
		$html = str_replace('{dir_css}', URL_CSS, $html);
		$html = str_replace('{dir_js}', URL_JS, $html);
		$html = str_replace('{error}', $this->error_str, $html);
		$html = str_replace('{login}', $this->loginform, $html);
		return $html;
	}
	
	protected function input_test_data() {
		
		$login = $_POST['login'];
		//$password = $_POST['pass'];
		$password = md5($_POST['pass'].')(&$^&%$%#@E#@";');
		$obj = data_base::get_connection();
		if ($this->type == 'admin'){
			$obj->select_query('SELECT `id`, `login`, `password`, `type` FROM user WHERE login="'.$login.'"');
		}
			
		$this->rez = $obj->get_row();
		if ($this->rez != null) {
			$passDB = $this->rez['password'];
			if ($passDB == $password) {
				$this->update_time_visit();
				$this->input();
			}
			else {
				$this->error_str = '<div class="error-group"><div id="error-label">Неверный пароль</div></div>';
				$this->loginform = $login;
			}
		}
		else {
			$this->error_str = '<div class="error-group"><div id="error-label">Несуществующий пользователь</div></div>';
			$this->loginform = $login;
		}
	}
	
	private function update_time_visit () {
	
		$obj = data_base::get_connection();
		$value = array(
				'time_visit'=> date('Y-m-d H:i:s')
		);
		$where = array(
				'id'=> $obj->escape($this->rez['id']),
		);
		$obj->update('user', $value, $where);
	}
	
	private function input() {
	
		$_SESSION['event_admin_id'] = $this->rez['id'];
		$_SESSION['event_admin_login'] = $this->rez['login'];
		$_SESSION['event_admin_type'] = $this->rez['type'];
		header('Location: admin.php?page=event');
		exit;
	}
}

?>