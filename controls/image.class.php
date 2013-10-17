<?php
class Image extends ControlBase 
{	
	/**
	 * Class Constructer
	 *
	 */
	public function __construct() 
	{
		parent::__construct ();
	}
	
	/**
	 * Class Destructer
	 *
	 */
	public function __destruct() 
	{
		parent::__destruct();
	}
	
	/**
	 * Method to display the Image
	 * 
	 * @access Public
	 * @return string
	 */
	public function Display()
	{
		$strBuilder = new StringBuilder();
		
		$strBuilder->Append('<img');
		
		if($this->Src != "")
			$strBuilder->Append('src="'.$this->Src.'"');
			
		if($this->ID != "")
			$strBuilder->Append('id="'.$this->ID.'"');
			
		if($this->Height != "")
			$strBuilder->Append('height="'.$this->Height.'"');
			
		if($this->Width != "")
			$strBuilder->Append('width="'.$this->Width.'"');
			
		if($this->Border != "")
			$strBuilder->Append('border="'.$this->Border.'"');
			
		if($this->Alt != "")
			$strBuilder->Append('alt="'.$this->Alt.'"');
		
		$strBuilder->Append('/>');
		
		$return = $strBuilder->toString();
		
		unset($strBuilder);

		return $return;
	}
}
?>