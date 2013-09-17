<?php

class html_pages
{
	private $count;
	private $page_size;
	private $page_number;
	private $limit;
	private $link;

	public function html_pages($count,$page_size,$page_number,$link)
	{
		$this->count = $count;
		$this->page_size = $page_size;
		$this->page_number = $page_number;
		
		$this->set_link($link);

		if (($this->count % $this->page_size) == 0){
			$this->limit=$this->count/$this->page_size;
		}
		else{
			$this->limit=floor($this->count/$this->page_size)+1;
		};
		if ($this->limit < $this->page_number || !(int)$this->page_number){
			header('Location: '.$this->link.'page_number=1');
 			exit;
		};
	}

	private function set_link($link) {
		if(strpos($link, '?')) {
			$this->link = $link.'&';
		}
		else {
			$this->link = $link.'?';
		}
	}
	
	public function show()
	{
		if ($this->limit == 1) {
			return '';
		}
		$str = '<div class="pages">';
		for($i=0;$i<$this->limit;$i++)
		{
			$ii=$i+1;
			if ($this->page_number == ($i+1)){
				$str.= "<span>$ii</span>";
			}
			else{
				$str.= "<a href=\"".$this->link."page_number=$ii\">$ii</a>";
			}
		}
		$str.='</div>';
		return $str;
	}
}