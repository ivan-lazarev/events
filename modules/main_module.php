<?php

class main_module {
	
	private $page = null;
	private $action = null;
	private $action_id = null;
	
	protected $values = array();
	
	public function render() {
		
		$user_name = $_SESSION['event_admin_login'];
		$user_div = $this->get_user_div($_SESSION['event_admin_type']); 
		$this->set('user_div', $user_div);		
		$html = $this->get_content();
		$this->set('user_name', '<b>'.$user_name.'</b>!');		
		$this->set('dir_js', URL_JS);
		$this->set('dir_css', URL_CSS);
		
		return $this->output(dirname(__FILE__).'/../templates/main.html');
	}
		
	private function get_user_div ($admin_type) {
		if ($admin_type == 1) {
			return '';
		}
		else {
			return '<div class="item{select_user}"><a href="admin.php?page=user">Пользователи</a></div>';
		}
	}
	
	private function get_content () {
				
		$class_name = $this->page.'_module';
		$this->select_left_menu_item();
		
		if (file_exists(dirname(__FILE__).'/'.$class_name.'.php')) {
			require_once $class_name.'.php';
			$obj = new $class_name();
			$obj->set_action($this->action, $this->action_id, $this->page);			
			$rightcolumn = $obj->render();
			$this->set('rigthcolumn_css_link', '<link rel="stylesheet" href="{dir_css}/'.$this->page.'.css" type="text/css">');
		}
		else {
// 			header('Location: admin.php?page=event');
// 			exit;
			$this->set('rigthcolumn_css_link','');
			$rightcolumn = '';			
		}
		$this->set('rightcolumn', $rightcolumn);
	}
	
	public function set_action ($action, $action_id) {
		$this->action = $action;
		$this->action_id = $action_id;
	}
	
	public function set_page($page) {
		$this->page = $page;
	}
	
	 	protected function set($name, $value) {
 		$this->values[$name] = $value;
 	}
 	 	
 	protected function output($template) {
 		$page = file_get_contents($template);
 		foreach($this->values as $key => $value) {
 			$page = str_replace('{'.$key.'}', $value, $page); 	
 		}
 		return $page;
 	} 
	
	private function select_left_menu_item () {
		
		$left_menu = array(
				'select_user' => '',
				'select_city' => '',
				'select_event' => '',
				'select_place' => '',
				'select_category' => ''
			);
		foreach ($left_menu as $key => $value) {
			if (substr($key,7) == $this->page) {
				$value = ' select';
			}
			$this->set($key, $value);
		}
	}
}