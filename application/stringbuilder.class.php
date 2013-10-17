<?php
final class StringBuilder
{	
	private $arrValues;
	
	/**
	 * Class Constructer
	 *
	 */
	function __construct() 
	{
		$this->arrValues = new ArrayObject();
	}
	
	/**
	 * Class Destructer
	 *
	 */
	function __destruct() 
	{
	
	}
	
	/**
	 * Method to add items to the Array Object
	 *
	 * @param string $strValue
	 */
	public function Append($strValue)
	{
		$this->arrValues->append($strValue);
	}
	
	/**
	 * Method to combine the elements of the Array Object to a string
	 *
	 * @return string
	 */
	public function toString()
	{
		$arr = new ArrayObject();
		
		foreach ($this->arrValues as $item)
		{
			if($item != "")
				$arr->append($item);
		}
		
		return implode(" ",(array)$arr);
	}
}
?>