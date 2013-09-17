<?php

include_once dirname(__FILE__).'/../utils/filters.php';

class html_table
{
	private $data;
	private $headers;
	private $page_size;
	private $page_number;
	
	private $filters = array();

	public function html_table ($headers, $data, $page_size, $page_number)
	{
		$this->headers = $headers;
		$this->data = $data;
		$this->page_size = $page_size;
		$this->page_number = $page_number;
	}
	
	public function add_filter($callback, $params = array()) {
		$this->filters[] = array(
					'callback'	=> $callback,
					'params'	=> $params
				);
	}

	public function show()
	{
		$flag = false;

		if ($this->page_size == 0){
			$this->page_number = 1;
			$this->page_size = count($this->data);
		}
		
		if (count($this->data) == 0) {
			return '';
		}

		foreach ($this->headers as $key => $value)
		{
			for ($i=0;$i<count($this->data[0]);$i++)
			{
				if ($this->data[0][$i] == $value){
					$flag = true;
					break;
				}
			}
			if ($flag == false){
				return "Отсутствуют данные для столбца ".$key;
			}
			$flag = false;
		}
		$str= "<table>";
		$str.= "<tr>";
		foreach ($this->headers as $key => $value)
		{
			$str.= "<th>{$key}</th>";
		}
		$str.= "</tr>";

		for ($i=(($this->page_number - 1) * $this->page_size + 1); $i<=($this->page_size*$this->page_number); $i++)
		{
			
			if ($i >= count($this->data)) {
				break;
			}
			
			$filter_data = array_combine($this->data[0], $this->data[$i]);
			
			foreach ($this->filters as $filter) {
				$callback = $filter['callback'];
				$params = $filter['params'];
				$callback($filter_data, $params);
			}
			$str.= "<tr>";
			foreach ($this->headers as $key)
			{
				$str.= "<td>{$filter_data[$key]}</td>";
			}
			$str.= "</tr>";
		}
		$str .= '</table>';
		return $str;
	}
}