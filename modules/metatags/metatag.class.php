<?php
final class MetaTag extends Base 
{
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct($file=null) 
	{
		parent::__construct ();
			
		$this->Interface	= MODULES.'metatags/template/metatags.html';
		$this->Document		= MODULES.'menu/data/menu.xml';
		$this->Element 		= 'menu';
		
		$this->File = $file;
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
	 * This method loads the meta tags from the menu.xml file
	 *
	 * @access Public
	 * @return String
	 */
	public function Load()
	{
		$strBuilder = new StringBuilder();
		
		// Load the data (XML)
		$data = $this->LoadData($this->Document, $this->Element);
		
		foreach ($data AS $elem)
		{
			if(file_exists($this->Interface))
			{
				// Load the template (HTML)
				$template = file($this->Interface);
							
				if($this->File == trim($elem->getElementsByTagName('page')->item(0)->nodeValue))
				{
					$this->SetPropertiesFromData($elem);
					
					for($x = 0; $x < count($template); $x ++) 
					{
						$strBuilder->Append($this->PopulateTemplate($template, $x));
					}
					
					break;
				}
				unset($template);
			}else
			{
				$strBuilder->Append($this->ErrorHandler->Display(ERROR_TEMPLATE, $this->Interface));
			}
		}
		
		$return = $strBuilder->toString();
		unset($strBuilder);
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
		$this->Title 		= trim($elem->getElementsByTagName('title')->item(0)->nodeValue);
		$this->Keywords 	= trim($elem->getElementsByTagName('keywords')->item(0)->nodeValue);
		$this->Description	= trim($elem->getElementsByTagName('description')->item(0)->nodeValue);
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
		$interface = str_replace('{title}',$this->Title,$template[$counter]);
		$interface = str_replace('{keywords}',$this->Keywords,$interface);
		$interface = str_replace('{description}',$this->Description,$interface);

		return $interface;
	}
}
?>