<?php

class data_base {
	
	private $link = null;
	public  $data = array();
	private $charset = 'utf8';

	public function connect ($hostname, $username, $password, $dbname) {                     //подключение к БД, 
	
		$this->link = mysql_connect($hostname, $username, $password)
		or die('connect to database failed');
		mysql_query("SET charset ".$this->charset);
		mysql_query("SET character_set_client = ".$this->charset);
		mysql_query("SET character_set_connection = ".$this->charset);
		mysql_query("SET character_set_results = ".$this->charset);
		//mysql_set_charset('cp1251');
		
		mysql_select_db($dbname,$this->link)
		or die('no database');
	}
		
	public function insert($table, $row) {                   //Добавление в БД строки, параметры: таблица, 
		                                                     //данные (ассоциативный массив, ключи-> название столбцов, 
		$flag=true;                                          //значение->данные)
		$header='';
		$data='"';
		foreach ($row as $key=>$value){
			$key = '`'.$key.'`';
			if(!$flag){
				$header.=' , ';
				$data.='" , "';
			}
			$header.=$key;
			$data.=$value;
			$flag=false;
		}
		
		$sql = '
			INSERT INTO `'.$table.'`
			('.$header.') 
			VALUES
			('.$data.'")';
		$result = mysql_query($sql)
		or die('Ошибка запроса: '.$sql.' '.mysql_error());
		return $msg='Good';
	}
	
	public function update($table, $row, $wh) {            //Изменение строки в БД, параметры: таблица,
		                                                     //данные (ассоциативный массив, ключи-> название столбцов,
		$flag=true;                                          //значение->данные), условие изменение данных.
		$set = '`';
		foreach ($row as $key=>$value){
			if(!$flag){

				$set.='\' , `';
			}
			$set.= $key.'` = \''.$value;
			$flag=false;
		}
		$set .= '\'';
		
		$flag=true;
		$where = '`';
		foreach ($wh as $key=>$value){
			if(!$flag){
		
				$where.=' AND `';
			}
			$where.= $key.'` = '.$value;
			$flag=false;
		}
		$where .= '';
		
		$sql = '
			UPDATE `'.$table.'`
			SET '.$set.'
			WHERE 
			'.$where.'';

		$result = mysql_query($sql)
		or die('Ошибка запроса: '.$sql.' '.mysql_error());
	}
	
	public function select_query($query){                                        //Выборка из БД, параметр - запрос                                                        //получается массив, 0-я строчка - столбцы
		$result = mysql_query($query)                                            //остальные - данные
		or die('query failed');	
		$flag = false;
		$this->data = array();
		while ($row = mysql_fetch_assoc($result))
		{
			if (!$flag) {
				$this->data[] = array_keys($row);
				$flag = true;
			}
			$this->data[]=array_values($row);
		}
		
	}
	
	public function delete($table, $where) {
		$flag=true;
		$wh = '`';
		foreach ($where as $key=>$value){
			if(!$flag){
		
				$wh.=' AND `';
			}
			$wh.= $key.'` = '.$value;
			$flag=false;
		}
		$wh .= '';
		$sql = 'DELETE FROM `'.$table.'` 
				WHERE '.$wh;
		mysql_query($sql)
		or die('Ошибка запроса: '.$sql.' '.mysql_error());
	}
	
	public function get_data(){
		return $this->data;
	}
	
	public function get_row(){                                              //Возврашает ассоциативный массив одной строки, 
		if (count($this->data) > 1) {                                      //ключи название -> столбцов, значение -> данные
			return array_combine($this->data[0], $this->data[1]); 
		}
		return null;
	}
	
	
    public function disconnect(){
    	mysql_close($this->link);
    }
    
    static public function escape($str) {
    	return mysql_real_escape_string($str, self::get_connection()->link);
    }    
    
    static private $connection = null;
    
    static public function get_connection() {
    	if (self::$connection === null) {
	    	self::$connection = new self();
 	    	self::$connection->connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
//	    	self::$connection->connect(DB_HOST, 'root', 'officepark', 'tusur_event');
    	}
    	return self::$connection;
    }
}



?>