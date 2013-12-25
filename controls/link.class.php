<?php
class Link extends ControlBase
{
	/**
	 * Class Constructer
	 *
	 */
	public function __construct($path=null, $text=null, $target=null)
	{
		parent::__construct();
		
		$this->Path = $path;
		$this->Text = $text;
		$this->Target = $target;
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
	 * Method to display the link
	 * 
	 * @param string $customInclude
	 * @return string
	 */
	public function Display($customInclude=null)
	{
		$custAttr = '';
		if($this->CustomAttr)
		{
			$custAttr = $this->CustomAttr;
		}
		
		$return = '<a href="'.$this->Path.'" target="'.$this->Target.'" class="'.$this->CssClass.'" '.$custAttr.'>'.$this->Text . $customInclude.'</a>';
		return $return;
	}
}
?>