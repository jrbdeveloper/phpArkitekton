<?php
class Panel extends Base
{
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Create the settings properties to store the configuration settings for this class
		$this->Interface	= MODULES.'panel/template/panel.html';
		$this->Document		= MODULES.'panel/data/panels.xml';
		$this->Element 		= 'panel';
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
	 * This function loads the sidebars into the page
	 *
	 * @return String
	 */
	public function Load($panelName=null)
	{
		$this->centerBody = false;
		
		$strBuilder = new StringBuilder();
		
		// Load the data (XML)
		$data = $this->LoadData($this->Document,$this->Element);
		
		if(file_exists($this->Interface))
		{
			// Load the template (HTML)
			$template = file($this->Interface);
			
			foreach ($data AS $elem)
			{
				// Get the values from the xml data file
				$this->SetPropertiesFromData($elem);
				
				if($panelName == $this->title)
				{
					// Populate the interface with the data
					for($x = 0; $x < count($template); $x ++)
					{
						$strBuilder->Append($this->PopulateTemplate($template, $x));
					}
				}
			}
			
			unset($template);
		}else
		{
			$strBuilder->Append($this->ErrorHandler->Display(ERROR_TEMPLATE, $this->Interface));
		}
		
		$return = $strBuilder->toString();
		
		unset($strBuilder);
		unset($data);
		
		return trim($return);
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
		$interface = str_replace('{title}',$this->title,$template[$counter]);
		$interface = str_replace('{image}',$this->image,$interface);
		$interface = str_replace('{resource}',$this->resource,$interface);
		
		if($this->centerBody)
			$interface = str_replace('{content}','<center>'.$this->image.$this->content.$this->resource.'</center>',$interface);
		else
			$interface = str_replace('{content}',$this->image.$this->content.$this->resource,$interface);
			
		return $interface;
	}
	
	/**
	 * Set the object properties from the data
	 *
	 * @param string $elem
	 */
	private function SetPropertiesFromData($elem)
	{
		$this->title 		= $elem->getElementsByTagName('title')->item(0)->nodeValue;
		$this->image 		= $this->getImage($elem->getElementsByTagName('image')->item(0));
		$this->content 		= $elem->getElementsByTagName('body')->item(0)->nodeValue;
		$this->resource 	= $this->getResource($elem->getElementsByTagName('resource')->item(0));
		$this->centerBody 	= ($elem->getElementsByTagName('body')->item(0)->getAttribute('align') == 'center');
	}
}
?>