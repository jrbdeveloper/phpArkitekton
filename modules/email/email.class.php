<?php
/**
 * @author John Bales
 * @copyright All Rights Reserved 2009
 * @package Objects
 * @name Email
 * @uses User Interface
 * @version 1.0
 */
class Email
{
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct() 
	{
		$this->ShowEditor 	= false;
		$this->To 			= isset($this->To) 		? $this->To 		: '';
		$this->From 		= isset($this->From) 	? $this->From 		: '';
		$this->Subject 		= isset($this->Subject) ? $this->Subject 	: '';
		$this->Body 		= isset($this->Body) 	? $this->Body 		: '';
	}
	
	/**
	 * Class destructor
	 * 
	 * @access Public
	 * @return Void
	 *
	 */
	public function __destruct() 
	{
	}
	
	/**
	 * This method performs the sending of the email
	 *
	 * @access Public
	 * @return String
	 */
	public function Send()
	{
		$this->getPostVars($_POST);	

		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: '.$this->From;		
		
		return mail($this->To, $this->Subject, $this->Body, $headers);
	}

	/**
	 * This method grabs the values from the form post and sets the class properties for saving an updating records
	 * 
	 * @access Protected
	 * @param Array $Post
	 * @return Void
	 */
	protected function getPostVars($Post=null)
	{
		$filterList = array("cmd", "redirect_cmd", "submit", "button");
		
		$this->Body = "<table>";
		foreach($Post as $key => $value) {
			$this->getFrom($key, $value);
			$this->getSubject($key, $value);
			$this->getRecipients($key, $value);
			
			if(!in_array($key, $filterList))
			{
				$this->Body .= "<tr><td>" . $this->InsertSpace($key) . "</td><td>" . $value . "</td></tr>";
			}		
		}
		$this->Body .= "</table>";
	}
	
	/**
	 * Method to get the recipients for the email
	 *
	 * @access Private
	 * @param unknown $key
	 * @param unknown $value
	 * @return Void
	 */
	private function getRecipients($key, $value)
	{
		if($key == "recipients")
		{
			$this->To = $value;
		}
	}
	
	/**
	 * Method to get the subject for the email
	 * 
	 * @access Private
	 * @param unknown $key
	 * @param unknown $value
	 * @return Void
	 */
	private function getSubject($key, $value)
	{
		if($key == "subject")
		{
			$this->Subject = $value;
		}
	}
	
	/**
	 * Method to get the email address for the from field
	 * 
	 * @access Private
	 * @param unknown $key
	 * @param unknown $value
	 * @return Void
	 */
	private function getFrom($key, $value)
	{
		if($key == "email")
		{
			$this->From = $value;
		}
	}
	
	/**
	 * Method to replace underscores with a space
	 * @param unknown $string
	 * @return string
	 */
	private function InsertSpace($string)
	{
		return ucwords(str_replace('_',' ', $string));
	}
}
?>