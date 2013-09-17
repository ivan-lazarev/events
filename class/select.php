<?php

class select {

	public function create ($name,$form_id,$data,$selected) { 
				
		$select = '<select name="'.$name.'" form="'.$form_id.'">';
		foreach ($data as $key=>$value){
			$select.='<option value="'.$value.'"';
			if($value == $selected){
			  $select.=' selected="selected"';
			}
			$select.='>'.$key.'</option>';
		}
		$select.='</select>';
		return $select;
	}
}
?>