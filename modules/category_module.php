<?php
include_once 'management_module.php';

class category_module extends management_module {
	
	protected function init() {
		$this->table_name = 'category';
		$this->sql = 'SELECT id, name FROM category';
		$this->headers = array(
				'Название' => 'name',
				'Изменить' => 'update',
				'Удалить'  => 'delete',
			);
		$this->fields = array ('name', 'number');
		$this->row_config = array(
			'category_name' => 'name',
			'category_number' => 'number'		
		);
	}

	protected function create_add () {
	
		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
			$this->add();
		}
		$this->set('category_name', '');	
		$this->set('category_number', '');		
		$this->set('action_url', 'admin.php?page=category/add');
		$this->set('submit_value', 'Добавить');
		return $this->output(DIR_TEMPLATES.'/form/form_add_category.html');
	}


	protected function create_update () {
			
		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
			$this->update();
		}
		$row = $this->get_row_for_db();
		$this->set('category_name', $row['name']);
		$this->set('category_number', $row['number']);
		$this->set('action_url', 'admin.php?page=category/update/'.$this->action_id);
		$this->set('submit_value', 'Изменить');
		return $this->output(DIR_TEMPLATES.'/form/form_add_category.html');
	}
}