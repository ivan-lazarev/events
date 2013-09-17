<?php
include_once 'management_module.php';

class event_module extends management_module {
	
	protected function init() {
		$this->table_name = 'event';
		$this->sql = 'SELECT id, name FROM event';
		$this->headers = array(
				'Название' => 'name',
				'Изменить' => 'update',
				'Удалить'  => 'delete',
			);
		$this->fields = array('name','city_id','place_id', 'desc','web_site','address','phones','email', 'cost_desc','start_time', 'duration', 'latitude', 'longitude');
		$this->row_config = array(
			'event_city_id' => 'city_id',
			'event_place_id' => 'place_id',
			'event_name' => 'name',
			'event_desc' => 'desc',
			'event_web_site' => 'web_site',	
			'event_phones' => 'phones',
			'event_address' => 'address',
			'event_email' => 'email',
			'event_cost_desc' => 'cost_desc',
			'event_start_time' => 'start_time',
			'event_duration' => 'duration',
			'event_latitude' => 'latitude',
			'event_longitude' => 'longitude'
		);
	}
	
	protected function create_add () {
 				
 		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
 			$this->add(); 					
 		} 
		$this->set('select_city', $this->get_select_element('city', 'event_city_id'));
		$this->set('select_place', $this->get_select_element('place', 'event_place_id')); 					  		
 		$this->set('event_name', ''); 	
 		$this->set('event_desc', ''); 
 		$this->set('event_web_site', '');
 		$this->set('event_phones', ''); 
 		$this->set('event_address', '');
 		$this->set('event_email', '');
 		$this->set('event_cost_desc', '');
 		$this->set('event_start_time', '');
 		$this->set('event_duration', '');
 		$this->set('event_latitude', '');
 		$this->set('event_longitude', '');
 		$this->set('latitude_on_map', 0);
 		$this->set('longitude_on_map', 0);
 		$this->set('action_url', 'admin.php?page=event/add');
 		$this->set('submit_value', 'Добавить');
 		return $this->output(DIR_TEMPLATES.'/form/form_add_event.html'); 		
 	}
 	
 	protected function add($row = array()) {
 		
 		include_once dirname(__FILE__).'/../class/data_base.php'; 		
 		$test = data_base::get_connection(); 		
 		foreach($this->row_config as $post_key => $db_key) {
 			$row[$db_key] = (isset($_POST[$post_key]) ? $test->escape($_POST[$post_key]) : '');
 		}
 		$test->insert($this->table_name,$row);
 		$this->add_category(mysql_insert_id());	
 		header('Location: admin.php?page='.$this->module_name);
 		exit;
 	}
 	
 	private function add_category($event_id) {
 		
 		include_once dirname(__FILE__).'/../class/data_base.php';
 		$test = data_base::get_connection();
 		$row = array(
 					'event_id' => $event_id,
 					'category_id' => ''
 				);
 		$category = $_POST['event_category'];
 		for($i = 0; $i < 2; $i++) {
 			$key = array_keys($category[$i]);
 			for($j = 0; $j < count($category[$i]); $j++){
 				$row['category_id'] = $category[$i][$key[$j]];
 				$test->insert('eventcategory',$row);
 			}
 		}
 	}
 	
 	protected function create_update () {
 		
 		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
 			$this->update();
 		}
 		
 		$row = $this->get_row_for_db();
 		$this->set('event_name', $row['name']);
 		$this->set('event_desc', $row['desc']);
 		$this->set('event_web_site', $row['web_site']);
 		$this->set('event_phones', $row['phones']);
 		$this->set('event_address', $row['address']);
 		$this->set('event_email', $row['email']);
 		$this->set('event_cost_desc', $row['cost_desc']);
 		$this->set('event_start_time', $row['start_time']);
 		$this->set('event_duration', $row['duration']);
 		$this->set('event_latitude', $row['latitude']);
 		$this->set('event_longitude', $row['longitude']);
 		$this->set('latitude_on_map', $row['latitude']);
 		$this->set('longitude_on_map', $row['longitude']);
 		$this->set('select_city', $this->get_select_element('city', 'event_city_id', $row['city_id']));
 		$this->set('select_place', $this->get_select_element('place', 'event_place_id', $row['place_id']));
 		$this->set('action_url', 'admin.php?page=event/update/'.$this->action_id);
 		$this->set('submit_value', 'Изменить');
 		return $this->output(DIR_TEMPLATES.'/form/form_add_event.html');
 	}
 	
}