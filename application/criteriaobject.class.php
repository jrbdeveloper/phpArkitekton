<?php
class CriteriaObject
{
	/**
	 * Object property array
	 * @access Private
	 */
	private $Properties = array();
	public $SelectList;
	public $CurrentPage;
	public $PageSize;
	public $SortColumn;
	public $SortDirection;
	public $TotalRecords;
	public $SearchKey;
	public $SearchValue;
	
	/**
	 * Object property setter method
	 * 
	 * @access Public
	 * @param String $name
	 * @param Mixed $value
	 */
	public function __set($name, $value)
	{
		$this->Properties[$name] = $value;
	}

	/**
	 * Object property getter method
	 * 
	 * @access Public
	 * @param String $name
	 */
	public function __get($name)
	{
		return array_key_exists($name, $this->Properties) ? $this->Properties[$name] : null;
	}
	
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct($database=null, $table=null, $selectList=null) 
	{
		if(!empty($database) && !empty($table) && !empty($selectList))
		{
			$this->SelectList 	= $selectList;
			$this->TotalRecords = $database->getRowCount($database->getRecordset($this->GetFrom($table)));
			$this->CurrentPage 	= isset($_POST['page']) ? $_POST['page'] : 1;
			$this->PageSize 	= isset($_POST['rp']) ? $_POST['rp'] : 10;
			$this->SortColumn 	= isset($_POST['sortname']) ? $_POST['sortname'] : $this->SelectList[1];
			$this->SortDirection= isset($_POST['sortorder']) ? $_POST['sortorder'] : 'desc';
			$this->SearchKey 	= isset($_POST['qtype']) ? $_POST['qtype'] : '';
			$this->SearchValue 	= isset($_POST['query']) ? $_POST['query'] : '';
		}
	}
	
	/**
	 * Class destructor
	 * 
	 * @access Public
	 * @return Void
	 */
	public function __destruct() 
	{
	
	}

	/**
	 * Constructs a select statement to be executed base on the criteria object properties
	 *
	 * @access Public
	 * @param String $table
	 * @param Array $criteria
	 * @return String
	 */
	public function GetFrom($table)
	{
		$strBuilder = new StringBuilder();
		
		// Get the list of columns to return from the query
		$selectList = (count($this->SelectList) > 0) ? $selectList = implode(",",$this->SelectList) : '*'; 
		
		$strBuilder->Append('SELECT '.$selectList);
		$strBuilder->Append(' FROM '.strtolower($table));
		$strBuilder->Append($this->getWhere());
		$strBuilder->Append($this->getOrder());
		$strBuilder->Append($this->getLimit());
		
		return $strBuilder->toString();
	}
	
	/**
	 * Method performs a save
	 * 
	 * @access Public
	 * @param String
	 * @return Boolean
	 */
	public function Save($database)
	{
		// Itterate over the columns array
		foreach ($database->Columns as $key=>$value)
		{
			// Create a column names string used for the insert statement
			$colNames .= $key.',';
			
			if(is_numeric($value))
			{
				$colValues .= $value.','; // Create a column values string used for the insert statement
				$value = $value; //Used for the updated statement
			}
			elseif(is_string($value))
			{
				$colValues .= "'".$value."',"; // Create a column values string used for the insert statement
				$value = "'".$value."'"; //Used for the updated statement
			}
			
			// Create a column list string used for the update statement
			$colList .= $key.'='.$value.',';
		}
		
		// If the record does not exists insert it, else update it
		if(!$database->RecordExists($database->Table,$database->KeyName,$database->KeyValue))
			$query = 'INSERT INTO '.$database->Table.'('.rtrim($colNames,',').') VALUES('.rtrim($colValues,',').')';
		else
			$query = 'UPDATE '.$database->Table.' SET '.rtrim($colList,',').' WHERE '.$database->KeyName.'='.$database->KeyValue;
		
		return $database->execute($query);
	}
	
	/**
	 * Method to delete select records from the database
	 * 
	 * @access Public
	 * @param Variaent
	 * @return Boolean
	 */
	public function Delete($database)
	{
		$query = 'DELETE FROM '.$database->Table.' WHERE '.$database->KeyName.'='.$database->KeyValue;
		return $database->execute($query);
	}
	
	/**
	 * Method to set the WHERE portion of the select statement
	 *
	 * @access Private
	 * @return String
	 */
	private function getWhere()
	{
		$return = '';
		
		// If there is anything in the criteria array
		if(count($this->Properties) > 0)
		{
			$return = ' WHERE '.$this->getQueryStringParameters();;			
		}else 
		{
			// Filter based on the search in the Flexigrid
			if($this->SearchKey != null)
			{
				$return = ' WHERE '.$this->SearchKey.' LIKE '."'".$this->SearchValue."%'";
			}else
			{
				$return = '';
			}
		}
		
		return $return;
	}
	
	/**
	 * Method to set the ORDER BY portion of the select statement
	 *
	 * @access Private
	 * @return string
	 */
	private function getOrder()
	{
		$order = '';
		
		if($this->SortColumn != "")
		{
			$order = ' ORDER BY '.$this->SortColumn.' '.$this->SortDirection;
		}
			
		return $order;
	}
	
	/**
	 * Method to set the LIMIT portion of the select statement
	 *
	 * @access Private
	 * @return String
	 */
	private function getLimit()
	{
		$limit = '';
		
		if($this->PageSize != "")
		{
			$start = ($this->CurrentPage-1) * $this->PageSize;
			$limit = ' LIMIT '.$start.','.$this->PageSize;
		}
		
		return $limit;
	}

	/**
	 * Method to assemble the filter parameters for the query from the querystring parameter
	 *
	 * @access Private
	 * @return string
	 */
	private function getQueryStringParameters()
	{
		$strBuilder = new StringBuilder();
		
		// Loop through the criteria array
		foreach ($this->Properties as $name => $value) 
		{
			if($value != '')
			{
				// Set the values to be used in the where portion of the sql statement
				if(is_numeric($value))
					$strBuilder->Append($name.'='.$value);
				else
					$strBuilder->Append($name."='$value'");
				
				// If there are multiple append them with AND logic
				if(count($this->Properties) > 0)
					$strBuilder->Append(' AND ');
			}
		}
	
		return substr($strBuilder->toString(),0,strrpos($strBuilder->toString(),'AND'));
	}
}
?>