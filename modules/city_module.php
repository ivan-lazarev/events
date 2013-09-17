<?php
include_once 'management_module.php';

class city_module extends management_module {
	
	protected function init() {
		$this->table_name = 'city';
		$this->sql = 'SELECT id, name FROM city';
		$this->headers = array(                       //заголовки и названия столбцов таблицы
				'Название' => 'name',
				'Изменить' => 'update',
				'Удалить'  => 'delete',
			);
		$this->fields = array ('name', 'latitude', 'longitude');  //массив полей, чтобы брать текущее значение
		$this->row_config = array(                                //массив для добавления, ключ поле на форме,  
			'city_name' => 'name',                               // значение поле в БД
			'city_latitude' => 'latitude',
			'city_longitude' => 'longitude'		
		);
	}
 	
 	protected function create_add () {
 		
 		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && $_POST['city_name']) {
 			$this->add();
 		} 						  		
 		$this->set('city_name', '');
 		$this->set('city_latitude', '');
 		$this->set('city_longitude', '');
 		$this->set('latitude_on_map', 0);
 		$this->set('longitude_on_map', 0);
 		$this->set('action_url', 'admin.php?page=city/add');
 		$this->set('submit_value', 'Добавить');
 		return $this->output(DIR_TEMPLATES.'/form/form_add_city.html'); 		
 	}
 	 	
 	protected function create_update () {
 		
 		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST' && $_POST['city_name']) {
 			$this->update();
 		}	
 		$row = $this->get_row_for_db();
 		$this->set('city_name', $row['name']);
 		$this->set('city_latitude', $row['latitude']);
 		$this->set('city_longitude', $row['longitude']);
 		$this->set('latitude_on_map', $row['latitude']);
 		$this->set('longitude_on_map', $row['longitude']);
 		$this->set('action_url', 'admin.php?page=city/update/'.$this->action_id);
 		$this->set('submit_value', 'Изменить');
 		return $this->output(DIR_TEMPLATES.'/form/form_add_city.html');
 	}
 	 	 	
 }