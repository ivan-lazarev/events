<?php
 
class HierarchyDBTableTools {
	
	protected $tableName = null;
	protected $uniqueField = null;
	protected $parentField = null;
	protected $orderBy = null;
	protected $where = null;
	
	protected $deepRSField = 'deep';
	
	/**
	 * @var IDB
	 */
	private $db = null;
	
	protected $rows = array();
	protected $links = array();
	
	private $visiters = array();
	private $preRenderVisiters = array();
	
	public function __construct(IDB $db, $tableName, $parentField, $uniqueField = 'id', $orderBy = null, $where = null) {
		$this->db = $db;
		$this->tableName = $tableName;
		$this->parentField = $parentField;
		$this->uniqueField = $uniqueField;
		$this->orderBy = $orderBy;
		$this->where = $where;
	}
	
	public function setDeepRSField($field) {
		$this->deepRSField = $field;
	}
	
	public function addVisiter($visiter) {
		$this->visiters[] = $visiter;
	}
	
	public function addPreRenderVisiter($visiter) {
		$this->preRenderVisiters[] = $visiter;
	}
		
	public function fill() {
		$sql = "SELECT * FROM $this->tableName ".$this->where." ".$this->orderBy;
		
		$rs = $this->db->select($sql);
		
		$this->rows = array();
		
		while($record = $rs->next()) {
			/* @var $record IRecord */
// 			foreach($this->visiters as $visiter) {
// 				/* @var $visiter IVisiter */
// 				$record->accept($visiter);
// 			}
			$this->rows[] = $record->toArray();
		}
		
		$this->visitList($this->rows, $this->visiters);
		
		$links = array();
		
		foreach($this->rows as &$record) {
			$links[$record[$this->uniqueField]] = $record;
		}
		
		foreach($this->rows as &$record) {
			if (isset($links[$record[$this->parentField]])) {
				if (!isset($links[$record[$this->parentField]]['childs'])) {
					$links[$record[$this->parentField]]['childs'] = array();
				}
				$links[$record[$this->parentField]]['childs'][] = &$links[$record[$this->uniqueField]];
				$links[$record[$this->uniqueField]]['__parent'] = &$links[$record[$this->parentField]];
			}
		}
		
		foreach($this->rows as &$record) {
			if ($record[$this->parentField]) {
				unset($links[$record[$this->uniqueField]]);
			}
		}

		$this->links = $links;
	}
	
	public function runPreRenderVisiters() {
		$this->_visitLinks($this->links, $this->preRenderVisiters);
	}
	
	private function _visitLinks(&$list, $visiters) {
		$this->visitList($list, $visiters);
		foreach($list as &$row) {
			if (isset($row['childs'])) {
				$this->_visitLinks($row['childs'], $visiters);
			}
		}
	}
	
	private function visitList(&$list, $visiters) {
		foreach($list as &$row) {
			$record = new StdRecord($row);
			foreach($visiters as $visiter) {
				/* @var $visiter IVisiter */
				$record->accept($visiter);
			}
			$row = $record->toArray();
		}
	}

	public function getTemplatedSequence($template, $delimiter) {
		$arr = array();
		$this->_getTemplatedSequence($arr, $template, $this->links);
		return implode($delimiter, $arr);
	}

	private function _getTemplatedSequence(&$arr, $template, $links) {
		foreach($links as $link) {
			$arr[] = $this->handleContent($template, $link);
			if (isset($link['childs'])) {
				$this->_getTemplatedSequence($arr, $template, $link['childs']);
			}
		}
	}
	
	public function renderUL($content, $excludeId = null) {
		if (!is_array($excludeId)) {
			$excludeId = array($excludeId);
		}
		$excludeId = array_filter($excludeId);
		$html = $this->_renderUL($content, $this->links, $excludeId);
		return $html;
	}

	public function renderSubUL($content, $id, $excludeId = null) {
		if (!is_array($excludeId)) {
			$excludeId = array($excludeId);
		}
		$excludeId = array_filter($excludeId);
		if ($id == 0) {
			return $this->_renderUL($content, $this->links, $excludeId);
		}
		$html = '';
		$link = $this->getLink($id);
		if ($link !== null && isset($link['childs'])) {
			$html = $this->_renderUL($content, $link['childs'], $excludeId);
		}
		return $html;
	}
	
	private function _renderUL($content, $links, $excludeId = null) {
		$html = '<ul>';
		foreach($links as $link) {
			if (in_array($link[$this->uniqueField], $excludeId)) {
				continue;
			}
			$html .= '<li>'.$this->handleContent($content, $link);
			if (isset($link['childs'])) {
				$html .= $this->_renderUL($content, $link['childs'], $excludeId);
			}
			$html .= '</li>';
		}

		$html .= '</ul>';
		
		return $html;
	}
	
	public function getLinks() {
		return $this->links;
	}
	
	public function getLink($id) {
		return $this->_getLink($id, $this->links);
	}
	
	private function _getLink($id, $links) {
		foreach($links as $link) {
			if ($link[$this->uniqueField] == $id) {
				return $link;
			}
			if (isset($link['childs'])) {
				$foundLink = $this->_getLink($id, $link['childs']);
				if (!is_null($foundLink)) {
					return $foundLink;
				}
			}
		}
		
		return null;
	}
	
	public function getSubIds($id) {
		$res = array();
		$this->_getSubIds($this->links, $id, $res, false);
		return $res;
	}

	private function _getSubIds($links, $id, &$res, $put) {
		foreach($links as $link) {
			if ($link[$this->uniqueField] == $id) {
				$put = true;
				$res[] = $link[$this->uniqueField];
				if (isset($link['childs'])) {
					$this->_getSubIds($link['childs'], $id, $res, $put);
				}
				return $res;
			}
			if ($put) {
				$res[] = $link[$this->uniqueField];
			} 
			if (isset($link['childs'])) {
				$this->_getSubIds($link['childs'], $id, $res, $put);
			}
		}
	}
	
	public function getChildren($id) {
		if ($id == 0) {
			return $this->links;
		} else {
			return $this->_getChildren($this->links, $id);
		}
	}
	
	public function _getChildren($links, $id) {
		
		foreach($links as $link) {
			if ($link[$this->uniqueField] == $id) {
				if (isset($link['childs'])) {
					return $link['childs'];
				}
			} else if (isset($link['childs'])) {
				$ret = $this->_getChildren($link['childs'], $id);
				if (!is_null($ret)) {
					return $ret;
				}
			}
		}
		return null;
	}

	public function getFormattedPath($content, $delimiter, $searchForId) {
		$path = $this->getPath($searchForId);
		
		$toRet = array();
		foreach($path as $el) {
			$toRet []= $this->handleContent($content, $el);
		}
		
		$toRet = implode($delimiter, $toRet);
		
		return $toRet;
	} 
	
	public function getReverseFormattedPath($content, $delimiter, $searchForId) {
		$path = array_reverse($this->getPath($searchForId));
		
		$toRet = array();
		foreach($path as $el) {
			$toRet []= $this->handleContent($content, $el);
		}
		
		$toRet = implode($delimiter, $toRet);
		
		return $toRet;
	}
	
	public function getPath($searchForId) {
		$this->pathStop = false;
		$path = array();
		$this->_getPath($path, $this->links, $searchForId);
		$path = array_reverse($path);
		return $path;
	}
	
	private $pathStop = false;
	
	private function _getPath(&$path, $links, $searchForId) {
		if (!$this->pathStop) {
			foreach($links as $link) {
				if ($this->pathStop) {
					break;
				}
				$el = $link;
				unset($el['childs']);
				if ($searchForId == $link['id']) {
					$this->pathStop = true;
				} else if (isset($link['childs'])) {
					$this->_getPath($path, $link['childs'], $searchForId);
				}
				if ($this->pathStop) {
					$path[] = $el;
				}
			}
		}
		
	}
	
	protected function handleContent($content, $row) {
		
		foreach($row as $key => $value) {
			if (is_array($value)) continue;
			$content = str_replace('{'.$key.'}', $value, $content);
		}
		
		return $content;
	}
	
	//--------------------------------------------------
	
	/**
	 * @return IRecordset
	 */
	public function getRecordset() {
		return new StdRecordset($this->generateRecords($this->deepRSField));
	}
	
	private function generateRecords($deepField) {
		$records = array();
		$deep = 0;
		$this->_generateRecords($records, $this->links, $deepField, $deep);
		return $records;
	}
	
	private function _generateRecords(&$records, $links, $deepField, $deep) {
		foreach($links as $link) {
			$el = $link;
			unset($el['__parent']);
			unset($el['childs']);
			$el[$deepField] = $deep;
			$records[] = new StdRecord($el);
			if (isset($link['childs'])) {
				$this->_generateRecords($records, $link['childs'], $deepField, $deep + 1);
			}
		}
	}

	//--------------------------------------------------
	
	/**
	 * @return IRecordset
	 */
	public function getComboboxRecordset($content, $excludeId = null, $startIds = null) {
		return new StdRecordset($this->generateComboboxRecords($content, $excludeId, $startIds));
	}
	
	private function generateComboboxRecords($content, $excludeId = null, $startIds = null) {
		$records = array();
		$this->_generateComboboxRecords(0, $records, $this->links, $content, $excludeId, $startIds);
		return $records;
	}
	
	private function _generateComboboxRecords($deep, &$result, $links, $content, $excludeId, $startIds, $started = false) {
		foreach($links as $link) {
			if ($link[$this->uniqueField] == $excludeId) {
				continue;
			}
			if (is_null($startIds) || $started || (is_array($startIds) && in_array($link[$this->uniqueField], $startIds))) {
				$str = str_repeat('&nbsp;&nbsp;&nbsp;', $deep).$this->handleContent($content, $link);
				$result[] = new StdRecord(array($link[$this->uniqueField], $str));
			}
			if (isset($link['childs'])) {
				$nextDeep = $deep;
				$nextStarted = $started;
				if (is_null($startIds) || $started || (is_array($startIds) && in_array($link[$this->uniqueField], $startIds))) {
					$nextDeep++;
					$nextStarted = true;
				}
				$this->_generateComboboxRecords($nextDeep, $result, $link['childs'], $content, $excludeId, $startIds, $nextStarted);
			}
		}
	}
	
	public function getRoot($id) {
		$link = $this->getLink($id);
		while(isset($link['__parent'])) {
			$link = $this->getLink($link[$this->parentField]);
		}
		return $link;
	}
	
	public function _getRoot($links, $id) {
		foreach($links as $link) {
			
		}
	}
}


?>