<?php
class ObjectBase extends Base 
{
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct() 
	{
		parent::__construct ();
	}
	
	/**
	 * Class destructor
	 * 
	 * @access Public
	 * @return Void
	 */
	public function __destruct() 
	{
		parent::__destruct();
	}

	/**
	 * Method saves the current object based on the parameter which is a person ID
	 * 
	 * @access Public
	 * @param Varient
	 * @return String
	 */
	public function Save($param=null, $action=null)
	{
		$criteriaObject = new CriteriaObject();

		$this->Database->Open();
		$this->Database->Table = get_class($this);
		$this->Database->Columns = $this->getFieldData();
		$this->Database->KeyName = 'ID';
		$this->Database->KeyValue = $param;
		
		// If the save is a success display a succes message, else display an error
		if($action == 'delete')
			$result = $criteriaObject->Delete($this->Database);
		else
			$result = $criteriaObject->Save($this->Database);
			
		if($result)
			$return = "<script>alert('".get_class($this)." Saved Successfully');location.href='?obj=".get_class($this)."&task=load';</script>";
		else
			$return = $this->ErrorHandler->Display(ERROR_OBJECT,get_class($this));
		
		$this->Database->Close();		
				
		unset($criteriaObject);
		
		return $return;
	}
	
	public function Delete($param=null)
	{
		return $this->Save($param,'delete');
	}
	
	/**
	 * Method used by objects using the Flexigrid; called through the contentloader.php file
	 *
	 * @access Public
	 * @param String $param
	 */
	public function GetJSONData($param)
	{
		$this->Load($param);
		print $this->JSON;
	}

	/**
	 * Method to set the parameter for creating the JSON data
	 *
	 * @access Private
	 * @param Array $list
	 * @param Object $criteriaObject
	 * @return Void
	 */
	protected function SetJSONData($list,$criteriaObject)
	{
		$data['page'] = $criteriaObject->CurrentPage;
		$data['total'] = $criteriaObject->TotalRecords;
		$data['rows'] = $list;
		$this->JSON = json_encode($data);
	}
	
	/**
	 * Method that returns an array of data rows
	 *
	 * @access Private
	 * @param Array $dataRow
	 * @return Array
	 */
	protected function LoadDataRows($dataRow, $selectList)
	{
		foreach ($selectList as $item)
		{
			$rows[] = $dataRow[$item];
		}
		
		return array("ID" => $dataRow['ID'], "cell" => $rows);
	}

	/**
	 * Method gets the values from the form fields
	 * 
	 * @access Private
	 * @return Array
	 */
	private function getFieldData()
	{
		$columns = array();
		
		// Collect the form variables
		foreach ($_POST as $key=>$value)
		{
			if(!empty($value))
			{
				$columns[$key] = $value; 
			}
		}

		return $columns;
	}
}
?>