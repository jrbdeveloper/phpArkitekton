<?php
class Person extends ObjectBase 
{	
	private $peopleList;
	
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct() 
	{
		parent::__construct();
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
	 * Method to load the person interface
	 *
	 * @param String $param
	 * @return String
	 */
	public function Load($param=null)
	{
		$strBuilder = new StringBuilder();
		
		// Call the method to set the Interface property
		$this->getInterface($param);
		
		if(file_exists($this->Interface))
		{
			// Load the template (HTML)
			$template = file($this->Interface);
			
			// Populate the person list with GetByCriteria
			$this->peopleList = $this->GetByCriteria($param);
		
			// Populate the interface with the data
			for($x = 0; $x < count($template); $x ++) 
			{
				if(empty($param))
				{
					$strBuilder->Append($template[$x]);
				}else
				{
					$strBuilder->Append($this->PopulateTemplate($template, $x));
				}
			}
		
			unset($template);
		}else
		{
			$strBuilder->Append($this->ErrorHandler->Display(ERROR_TEMPLATE, $this->Interface));
		}
		
		// Replace out the parameter property so we know what to load
		$return = str_replace("{param}",$param,$strBuilder->toString());

		unset($strBuilder);
		
		return $return;
	}
	
	/**
	 * Method used to get the object collection
	 *
	 * @param String $criteria
	 * @return ArrayObject
	 */
	private function GetByCriteria($criteria=null)
	{
		$table = 'person';
		$selectList = array("ID","FirstName","LastName","UserName","Password");
		
		// Create list objects to be used
		$ObjectList = new ArrayObject();
		$DataList = new ArrayObject();
		
		// Open a connection to the database
		$this->Database->Open();
		
		// Set all the Criteria object properties
		$CriteriaObject = new CriteriaObject($this->Database, $table, $selectList);
		
		// Set the criteria filters
		if(!empty($criteria))
		{
			if(is_numeric($criteria))
				$CriteriaObject->ID = $criteria;
			else
				$CriteriaObject->LastName = $criteria;
		}
		
		// Get the recordset results
		$result = $this->Database->getRecordset($CriteriaObject->GetFrom($table));
		while ($row = $this->Database->getRecords($result))
		{
			// Add objects to a list by calling the factory method to create them
			$ObjectList->append($this->CreateObjectFromData($row));
			
			// Add records to a list by calling the LoadDataRows method
			$DataList->append($this->LoadDataRows($row, $CriteriaObject->SelectList));
		}
		
		// Call the method to set the JSON data
		$this->SetJSONData($DataList, $CriteriaObject);
		
		// Close the connection to the database
		$this->Database->Close($result);
		
		unset($CriteriaObject);
		
		return $ObjectList;
	}
	
	/**
	 * Method that creates objects based on the data row passed to it
	 *
	 * @access Private
	 * @param Array $dataRow
	 * @return Object
	 */
	private function CreateObjectFromData($dataRow)
	{
		$person = new Person();
		$person->FirstName = $dataRow['FirstName'];
		$person->LastName = $dataRow['LastName'];
		$person->Username = $dataRow['UserName'];
		$person->Password = $dataRow['Password'];
		
		return $person;
	}

	/**
	 * Method gets the interface based on if there is a query string parameter
	 * 
	 * @access Private
	 * @return Void
	 */
	private function getInterface($param=null)
	{
		if(empty($param))
		{
			$this->Interface = MODULES.'person/template/view.template.html';
		}else
		{
			$this->Interface = MODULES.'person/template/edit.template.html';
		}
	}

	/**
	 * Populate the template with the data
	 *
	 * @param string $template
	 * @param integer $counter
	 * @return string
	 */
	private function PopulateTemplate($template, $counter)
	{
		$interface = str_replace('{firstname}',$this->peopleList[0]->FirstName,$template[$counter]);
		$interface = str_replace('{lastname}',$this->peopleList[0]->LastName,$interface);
		$interface = str_replace('{username}',$this->peopleList[0]->Username,$interface);
		$interface = str_replace('{password}',$this->peopleList[0]->Password,$interface);
		return $interface;
	}
}
?>