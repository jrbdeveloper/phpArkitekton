<?php
class Hyperlink extends ControlBase 
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
	 * Class destructer
	 *
	 */
	public function __destruct() 
	{
		parent::__destruct();
	}
	
	/**
	 * Method to display the Hyperlink
	 *
	 * @access Public
	 * @param string $link
	 * @return string
	 */
	public function Display($link=null,$text=null)
	{
		if($this->Visible)
		{
			if(!is_null($text))
				$this->Text = $text;
			
			$stringBuilder = new StringBuilder();

			$stringBuilder->Append('<a');
			
			if($link != "")
				$stringBuilder->Append('href="'.$link.'"');
			
			if($this->ID != "")
				$stringBuilder->Append('id="'.$this->ID.'"');
				
			if($this->CssClass != "")
				$stringBuilder->Append('class="'.$this->CssClass.'"');
				
			if($this->Style != "")
				$stringBuilder->Append('style="'.$this->Style.'"');
				
			if($this->onClick != "")
				$stringBuilder->Append('onclick="'.$this->onClick.'"');
				
			$stringBuilder->Append('>'.$this->Text.'</a>');
			
			return $stringBuilder->toString();
		}
		else
		{
			return "";
		}
	}
}
?>