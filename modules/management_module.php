<?php

abstract class management_module {
 	
 	protected  $action = null;
 	protected  $action_id = null;
 	protected  $module_name = null;
 	protected  $page_number = null;
 	protected  $page_size = null;
 	
 	protected  $table_name = null;
 	protected  $sql = null;
 	protected  $headers = null;
 	protected  $fields = array();
 	protected  $row_config = null;
 	
 	protected  $values = array();
 	
 	abstract protected function init();
 	
 	public function render () { 	
 		
 		$this->init(); 		
 		$page = '';
		if ($this->action) {
 			$method_name = 'create_'.$this->action;
 		}
 		else {
 			$method_name = 'create_show';
 		} 		
		if (method_exists($this, $method_name)) {
			$page = $this->$method_name();
		} 					
 		return $page;
 	}
 	
   	protected function check_page_number() { 		
 		if(!isset($_GET['page_number'])) {
 			return 1;
 		}
 		else {
 			return $_GET['page_number'];
 		} 		
 	}
 	
 	public function set_action ($action, $action_id, $module_name) {
 		$this->action = $action;
 		$this->action_id = $action_id;
 		$this->module_name = $module_name;
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
 	
 	protected function add($row = array()) {
 		
 		include_once dirname(__FILE__).'/../class/data_base.php';
 		$test = data_base::get_connection(); 		
 		foreach($this->row_config as $post_key => $db_key) {
 			$row[$db_key] = (isset($_POST[$post_key]) ? $test->escape($_POST[$post_key]) : '');
 		}
 		$test->insert($this->table_name,$row); 		
 		header('Location: admin.php?page='.$this->module_name);
 		exit;
 	}
 	
 	protected function update($row = array()) {
 		
 		include_once dirname(__FILE__).'/../class/data_base.php';
 		$test = data_base::get_connection(); 		
 		foreach($this->row_config as $post_key => $db_key) {
 			$row[$db_key] = (isset($_POST[$post_key]) ? $test->escape($_POST[$post_key]) : '');
 		}
 		$where = array(
 					'id' => $test->escape($this->action_id)
 				);
 		$test->update($this->table_name,$row,$where);
 		header('Location: admin.php?page='.$this->module_name);
 		exit;
 	}
 	
 	protected function create_show ($headers = null) { 	
 		include_once dirname(__FILE__).'/../class/html_table.php';
 		include_once dirname(__FILE__).'/../class/html_pages.php';
 		include_once dirname(__FILE__).'/../class/data_base.php';
 			
 		$this->page_number = $this->check_page_number();
 		$this->page_size = 10;
 	
 		$test = data_base::get_connection();
 		$test->select_query($this->sql);
 		$data = $test->get_data(); 	    
		$pages = $this->get_pages($data);		
		$this->add_action_fields($data);			 
 									
		$param = array(
				'module' => $this->module_name,
				'delete_name' => $this->fields[0],
			);
 		$html = new html_table($this->headers, $data, $this->page_size, $this->page_number);
 		$html->add_filter('filter_add_action_button',$param);
 		$button = '<div class="add-button"><a href="admin.php?page='.$this->module_name.'/add">Добавить</a></div>';
  		return $html->show().$pages.$button;
 	}
 	
	protected function create_delete () { 		
 		
 		$this->get_row_for_db();
 		include_once dirname(__FILE__).'/../class/data_base.php';
 		$test = data_base::get_connection();
 		$where = array(
 				'id' => $test->escape($this->action_id)
 		);
 		$test->delete($this->table_name,$where);
 		header('Location: admin.php?page='.$this->module_name);
 		exit;
 	}
 	
 	protected function get_row_for_db () { 		
 		if ($this->action_id != 0){
 			 			
 			include_once dirname(__FILE__).'/../class/data_base.php';
 			$test = data_base::get_connection(); 			
 			for ($i=0;$i<count($this->fields);$i++) {
 				$this->fields[$i] = '`'.$this->fields[$i].'`';
 			}
 			$test->select_query('SELECT '.implode(' , ', $this->fields).' FROM '.$this->table_name.' WHERE id = '.$test->escape($this->action_id));
 			$row = $test->get_row();
 			if ($row) {
 				return $row;
 			}
 		}
 		header('Location: admin.php?page='.$this->module_name);
 		exit;
 	}
 	
 	protected function get_pages(&$data) {
 		if (!$data){
 			$i=0;
 			foreach ($this->headers as $key => $val){
 				$data[0][$i]=$val;
 				$i++;
 			}
 			return '';
 		}
 		else {
 			$pages = new html_pages(count($data) - 1, $this->page_size, $this->page_number, 'admin.php?page='.$this->module_name);
 			return $pages->show();
 		}
 	}
 	
 	protected function add_action_fields(&$data) {
 		array_push($data[0],'delete');
 		array_push($data[0],'update');             
 		for ($i = (($this->page_number - 1) * $this->page_size + 1); $i <= ($this->page_size * $this->page_number); $i++) {
 			if ($i >= count($data)) {
				break;
			}
 			array_push($data[$i],'');
 			array_push($data[$i],'');
 		} 
 	} 		
 	
 	protected function get_select_element ($table_name, $element_name, $selected = null) {
 		
 		include_once dirname(__FILE__).'/../class/select.php';
		include_once dirname(__FILE__).'/../class/data_base.php';
		$row = array();
		$test = data_base::get_connection();
 		$test->select_query('SELECT id, name FROM '.$table_name); 
 		$temp = $test->get_data();
 		for ($i=1;$i<count($temp);$i++) {
 			$row[$temp[$i][1]]=$temp[$i][0];
 		}			 		
 		$select = new select();
 		return $select->create($element_name, 'form', $row, $selected);
 		
 	}
 }