<?php

class hierarchy {
        
	private $hierarchy_sequence;
	private $parent_field;
	private $uniq_field;
	private $data;

       
	function __construct ($data, $parent_field, $uniq_field = 'id') {
                
		$this->data = $data;
		for($i = 0; $i < count($this->data[0]); $i++){
			if($this->data[0][$i] == $parent_field){
				$this->parent_field = $i;
			}
			if($this->data[0][$i] == $uniq_field){
				$this->uniq_field = $i;
			}			
		}
	}
        
	public function create_hierarchy_sequence () {

		for($i = 0; count($this->data); $i++) {
			$links[$this->data[$this->uniq_field]] = &$this->data[$i];
		}  
		var_dump($links); exit;
	}
	
}