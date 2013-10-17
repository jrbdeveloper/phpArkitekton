<?php

if(!defined('ERROR_TEMPLATE'))
	define(strtoupper('ERROR_TEMPLATE'),'<b>ERROR:</b> can not load template for: ');
	
if(!defined('ERROR_CONTENT'))
	define(strtoupper('ERROR_CONTENT'),'<b>ERROR:</b> can not load content for: ');
	
if(!defined('ERROR_OBJECT'))
	define(strtoupper('ERROR_OBJECT'),'<b>ERROR:</b> can not create an object for: ');
	
if(!defined('ERROR_FILE'))
	define(strtoupper('ERROR_FILE'),'<b>ERROR:</b> can not create an object for: ');
	
class ErrorHandler 
{	
	/**
	 * Class Constructer
	 *
	 * @return Void
	 */
	public function __construct() 
	{
	
	}
	
	/**
	 * Class Destructer
	 *
	 * @return Void
	 */
	public function __destruct() 
	{
	
	}
	
	/**
	 * Method to determine and display the proper error message
	 *
	 * @access Abstract
	 * @param string $type
	 * @param string $resource
	 * @return string
	 */
	public function Display($type=null, $resource=null)
	{
		switch ($type)
		{
			case ERROR_TEMPLATE:
				return ERROR_TEMPLATE.$resource;
				break;
				
			case ERROR_CONTENT:
				return ERROR_CONTENT.$resource;
				break;
				
			case ERROR_OBJECT:
				return ERROR_OBJECT.$resource;
				break;
				
			case ERROR_FILE:
				return ERROR_FILE.$resource;
				break;
				
			default:
				return $type;
				break;
		}
	}
}
?>