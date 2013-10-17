<?php
final class Footer extends Base
{
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->Interface 	= MODULES.'footer/template/footer.html';
		$this->Document 	= MODULES.'footer/data/footer.xml';
		$this->Element 		= 'content';
	}
	
	/**
	 * Class destructor
	 * 
	 * @access Public
	 * @return Void
	 */
	function __destruct() 
	{
		parent::__destruct();
	}
	
	/**
	 * This function get the footer content from the footer xml file and loads it into the interface
	 *
	 * @access Private
	 * @return String
	 */
	public function Load()
	{
		$return = '';
		
		// Load the data (XML)
		$data = $this->LoadData($this->Document,$this->Element);
		
		if(file_exists($this->Interface))
		{
			// Load the interface (HTML)
			$return = $this->LoadInterface($this->Interface);
			
			foreach ($data AS $elem)
			{
				// Get the object properties
				$this->SetPropertiesFromData($elem);
			
				// Populate the interface with the data
				$return = str_replace('{address}',$this->Address,$return);
				$return = str_replace('{phone}',$this->Phone,$return);
				$return = str_replace('{fax}',$this->Fax,$return);
				$return = str_replace('{email}',$this->Email,$return);
				$return = str_replace('{copyright}',$this->Copyright,$return);				
			}
		}else
		{
			$return = $this->ErrorHandler->Display(ERROR_TEMPLATE, $this->Interface);
		}
		
		unset($data);
		
		return $return;
	}
	
	/**
	 * Set the object properties from the data
	 *
	 * @param string $elem
	 */
	private function SetPropertiesFromData($elem)
	{
		// Get the values from the xml data file
		$this->Address 		= $elem->getElementsByTagName('address')->item(0)->nodeValue; 
		$this->Phone 		= $elem->getElementsByTagName('phone')->item(0)->nodeValue;
		$this->Fax 			= $elem->getElementsByTagName('fax')->item(0)->nodeValue;
		$this->Email 		= $elem->getElementsByTagName('email')->item(0)->nodeValue;
		$this->Copyright 	= str_replace('(c)','&copy;',$elem->getElementsByTagName('copyright')->item(0)->nodeValue);
	}
}
?>