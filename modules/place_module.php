<?php
include_once 'management_module.php';

class place_module extends management_module {
	
	protected function init() {
		$this->table_name = 'place';
		$this->sql = 'SELECT id, name FROM place';
		$this->headers = array(
				'Название' => 'name',
				'Изменить' => 'update',
				'Удалить'  => 'delete',
			);
		$this->fields = array('name','city_id','desc','web_site','address','phones','email','work_time', 'latitude', 'longitude');
		$this->row_config = array(
			'place_city_id' => 'city_id',
			'place_name' => 'name',
			'place_desc' => 'desc',
			'place_web_site' => 'web_site',	
			'place_phones' => 'phones',
			'place_address' => 'address',
			'place_email' => 'email',
			'place_latitude' => 'latitude',
			'place_longitude' => 'longitude'
		);
	}
	
	protected function create_add () {
 				
 		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
 			$row = array();
 			$row['work_time'] = $this->get_field_work_time();
 			$this->add($row);		
 		} 
 		$this->set('select_city', $this->get_select_element('city', 'place_city_id'));
		$this->create_table_work_table();				  		
 		$this->set('place_name', ''); 	
 		$this->set('place_desc', ''); 
 		$this->set('place_web_site', '');
 		$this->set('place_phones', ''); 
 		$this->set('place_address', '');
 		$this->set('place_email', '');
 		$this->set('place_latitude', '');
 		$this->set('place_longitude', '');
 		$this->set('latitude_on_map', 0);
 		$this->set('longitude_on_map', 0);
 		$this->set('action_url', 'admin.php?page=place/add');
 		$this->set('submit_value', 'Добавить');
 		return $this->output(DIR_TEMPLATES.'/form/form_add_place.html'); 		
 	}
 	
 	protected function create_update () {
 		
 		if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
 			$row_up = array();
 			$row_up['work_time'] = $this->get_field_work_time();
 			$this->update($row_up);
 		}
 		
 		$row = $this->get_row_for_db();
 		$this->create_table_work_table($row['work_time']);
 		$this->set('place_name', $row['name']);
 		$this->set('place_desc', $row['desc']);
 		$this->set('place_web_site', $row['web_site']);
 		$this->set('place_phones', $row['phones']);
 		$this->set('place_address', $row['address']);
 		$this->set('place_email', $row['email']);
 		$this->set('place_latitude', $row['latitude']);
 		$this->set('place_longitude', $row['longitude']);
 		$this->set('latitude_on_map', $row['latitude']);
 		$this->set('longitude_on_map', $row['longitude']);
 		$this->set('select_city', $this->get_select_element('city', 'place_city_id', $row['city_id']));
 		$this->set('action_url', 'admin.php?page=place/update/'.$this->action_id);
 		$this->set('submit_value', 'Изменить');
 		return $this->output(DIR_TEMPLATES.'/form/form_add_place.html');
 	}
 	
 	private function create_table_work_table ($work_time = '') {
 		include_once dirname(__FILE__).'/../class/select.php'; 		
 		if ($work_time == '') {
 			$work_time = '00,00,0;00,00,0;00,00,0;00,00,0;00,00,0;00,00,0;00,00,0;';
 		}
 		$work = $this->parse_work_time($work_time);
 		$hours = array(
 				'00' => '00',
 				'01' => '01',
 				'02' => '02',
 				'03' => '03',
 				'04' => '04',
 				'05' => '05',
 				'06' => '06',
 				'07' => '07',
 				'08' => '08',
 				'09' => '09',
 				'10' => '10',
 				'11' => '11',
 				'12' => '12',
 				'13' => '13',
 				'14' => '14',
 				'15' => '15',
 				'16' => '16',
 				'17' => '17',
 				'18' => '18',
 				'19' => '19',
 				'20' => '20',
 				'21' => '21',
 				'22' => '22',
 				'23' => '23'
 			);
 		$day = array (
 				'Понедельник' => 'mo',
 				'Вторник' => 'tu',
 				'Среда' => 'we',
 				'Четверг' => 'th',
 				'Пятница' => 'fr',
 				'Суббота' => 'sa',
 				'Воскресение' => 'su'
 			);
 		$select = new select();
 		$i = 0;
 		$table = '<table><tr><th>День недели</th><th>С</th><th>По</th><th>Выходной</th></tr>';
 		foreach ($day as $title => $cod) {
 			$checked = $work[$i][2] == 1 ? 'checked="checked"' : '';
 			$table .= '<tr><td>'.$title.'</td>
 							<td>'.$select->create('work['.$i.'][0]', 'form', $hours, $work[$i][0]).'</td>
 							<td>'.$select->create('work['.$i.'][1]', 'form', $hours, $work[$i][1]).'</td>
 							<td><input type="checkbox" name="work['.$i.'][2]" '.$checked.'/></td>
 						</tr>';
 			$i++;
 		}
 		$table .= '</table>';
 		$this->set('work_time', $table);
 	}
 	
 	private function get_field_work_time(){
 		
 		$work = $_POST['work']; 		
 		$work_time = '';
 		for ($i=0;$i<count($work);$i++) {
 			$work_time .= isset($work[$i][2]) ? '00,00,1;' : $work[$i][0].','.$work[$i][1].','.'0;';
 		}
 		return $work_time;
 	}
 	
 	private function parse_work_time($work_time) {
 		
 		$work = explode(';', $work_time);
 		for ($i=0;$i<count($work);$i++) {
 			if ($work[$i] != '') {
 				$work[$i] = explode(',', $work[$i]);
 			}
 			else {
 				unset ($work[$i]);
 			}
 		}
 		return $work;
 	}

}