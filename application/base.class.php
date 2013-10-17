<?php
/**
 * @copyright 2010 phpArkitekton
 * @version 1.8.1
 * @author John Bales
 * @license This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

class Base
{
	/**
	 * Object property array
	 * @access Private
	 */
	private $Properties = array();

	/**
	 * Object property setter method
	 * 
	 * @access Public
	 * @param String $name
	 * @param Mixed $value
	 */
	public function __set($name, $value)
	{
		$this->Properties[$name] = $value;
	}

	/**
	 * Object property getter method
	 * 
	 * @access Public
	 * @param String $name
	 */
	public function __get($name)
	{
		return array_key_exists($name, $this->Properties) ? $this->Properties[$name] : null;
	}

	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct()
	{
		if(function_exists("date_default_timezone_set") and function_exists("date_default_timezone_get"))
			@date_default_timezone_set(@date_default_timezone_get());
			
		$this->ParseQuerystring();
		$this->ErrorHandler = new ErrorHandler();
		$this->DevMode = true;
		$this->LoadConstants();
		$this->CeateDatabaseConnection();
	}

	/**
	 * Class destructor
	 * 
	 * @access Public
	 * @return Void
	 */
	public function __destruct()
	{
	}

	/**
	 * Method to create a connection to the database
	 * 
	 * @access Protected
	 * @return Void
	 */
	protected function CeateDatabaseConnection()
	{
		// Create an instance of the database for use in the system
		if($this->InProduction($_SERVER['SERVER_NAME']))
			//$this->Database	= new Database('gstour.db.4628821.hostedresource.com','gstour','Golf1215','gstour');
			$this->Database	= new Database('97.74.149.114','gstour','Golf1215','gstour');
		else
			$this->Database	= new Database('localhost','root','','test');
	}
	
	/**
	 * This method checks to see if the application is in production or not
	 * 
	 * @access Protected
	 * @param String $ServerName
	 * @return Boolean
	 */
	protected function InProduction($ServerName=null)
	{
		if(!strpos($ServerName,'.com'))
			return false;
		else
			return true;
	}
	
	/**
	 * This function defines the applications constants
	 * 
	 * @access Private
	 * @return Void
	 */
	private function LoadConstants()
	{
		$data_config = $this->LoadData('application/data/appconfig.xml','config');
		
		if(!defined("NEW_LINE"))
			define("NEW_LINE","\n");
			
		foreach ($data_config AS $elem)
		{
			if(!defined(strtoupper('sitename')))
				define(strtoupper('sitename'),$elem->getElementsByTagName('sitename')->item(0)->nodeValue);
				
			if(!defined(strtoupper('tagline')))
				define(strtoupper('tagline'),$elem->getElementsByTagName('tagline')->item(0)->nodeValue);
				
			if(!defined(strtoupper('ownername')))	
				define(strtoupper('ownername'),$elem->getElementsByTagName('ownername')->item(0)->nodeValue);
				
			if(!defined(strtoupper('owneremail')))	
				define(strtoupper('owneremail'),$elem->getElementsByTagName('owneremail')->item(0)->nodeValue);
				
			if(!defined(strtoupper('appversion')))
				define(strtoupper('appversion'),$elem->getElementsByTagName('appversion')->item(0)->nodeValue);
			
			$path = $elem->getElementsByTagName('paths');
			if($path->length > 0)
			{
				foreach ($path AS $nested)
				{
					if(!defined(strtoupper('application')))
						define(strtoupper('application'),$nested->getElementsByTagName('application')->item(0)->nodeValue);
						
					if(!defined(strtoupper('controls')))
						define(strtoupper('controls'),$nested->getElementsByTagName('controls')->item(0)->nodeValue);
						
					if(!defined(strtoupper('data')))
						define(strtoupper('data'),$nested->getElementsByTagName('data')->item(0)->nodeValue);

					if(!defined(strtoupper('templates')))
						define(strtoupper('templates'),$nested->getElementsByTagName('templates')->item(0)->nodeValue);

					if(!defined(strtoupper('modules')))
						define(strtoupper('modules'),$nested->getElementsByTagName('modules')->item(0)->nodeValue);
												
					if(!defined(strtoupper('pages')))
						define(strtoupper('pages'),$nested->getElementsByTagName('pages')->item(0)->nodeValue);
						
					if(!defined(strtoupper('public')))
						define(strtoupper('public'),$nested->getElementsByTagName('public')->item(0)->nodeValue);
						
					if(!defined(strtoupper('logs')))
						define(strtoupper('logs'),$nested->getElementsByTagName('logs')->item(0)->nodeValue);
				}
			}
		}
		
		unset($data_config);
	}

	/**
	 * This method parses and loads the a template to be used by the load methods of the application and modules
	 *
	 * @access Protected
	 * @param String $interface
	 * @return String
	 */
	protected function LoadInterface($interface)
	{
		$strBuilder = new StringBuilder();
		$template = file($interface);

		foreach ($template as $line) 
		{
			$strBuilder->Append($line);
		}
		
		$return = $strBuilder->toString();
		
		unset($strBuilder);
		unset($template);
		
		return $return;
	}

	/**
	 * This function centralizes the code required for loading an xml document
	 *
	 * @access Protected
	 * @param String $resource
	 * @param String $tag
	 * @return Object
	 */
	protected function LoadData($resource, $tag)
	{
		$dom = new DOMDocument();
		$dom->load($resource);
		$object = $dom->getElementsByTagName($tag);
		
		return $object;
	}

	/**
	 * This function will process the image node in the xml file
	 *
	 * @param String $image
	 * @return String
	 */
	protected function getImage($image)
	{
		$hyperLink = new Hyperlink();
		$Image = new Image();
		
		$p_link 		= $image->getAttribute('link');
		$Image->Src		= $image->nodeValue;
		$Image->Height 	= $image->getAttribute('height');
		$Image->Width 	= $image->getAttribute('width');
		$Image->Alt		= $image->getAttribute('alt');
		$Image->Border 	= $image->getAttribute('border');
		
		if($p_link != '')
			$return = $hyperLink->Display($p_link, $Image->Display());
		else
			$return = $Image->Display();
			
		unset($hyperLink);
		unset($Image);
		
		return $return;
	}

	/**
	 * This function will process the resource node in the xml file
	 *
	 * @param String $resource
	 * @return String
	 */
	protected function getResource($resource)
	{
		$hyperLink = new Hyperlink();
		
		$p_resource = $resource->nodeValue;
		$p_title 	= $resource->getAttribute('title');
		
		if($p_title != '')
			$return = $hyperLink->Display($p_resource, $p_title);
		else
			$return = $hyperLink->Display($p_resource, $p_resource);
			
		unset($hyperLink);
		
		return $return;
	}

	/**
	 * This method is used to display the wysiwyg editor, it automatically converts a textarea object
	 *
	 * @access Private
	 * @return String
	 */
	protected function EditorInit()
	{
		$editor = 	'<script type="text/javascript" src="application/controls/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>'.NEW_LINE.
					'<script type="text/javascript"> '.NEW_LINE.
					'	tinyMCE.init({'.NEW_LINE.
					'		// General options'.NEW_LINE.
					'		mode : "textareas",'.NEW_LINE.
					'		theme : "advanced",'.NEW_LINE.
					'		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,inlinepopups",'.NEW_LINE.
					
					'		// Theme options'.NEW_LINE.
					'		theme_advanced_buttons1 : "save,newdocument,|,cut,copy,paste,pastetext,pasteword,|,search,replace,|,undo,redo,|,cleanup,code,iespell,|,preview,template,fullscreen",'.NEW_LINE.
					'		theme_advanced_buttons2 : "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,forecolor,backcolor,|,removeformat,visualaid",'.NEW_LINE.
					'		theme_advanced_buttons3 : "tablecontrols,|,insertdate,inserttime,|,charmap,media,image,advhr,|,link,unlink,anchor",'.NEW_LINE.
					'		theme_advanced_buttons4 : "formatselect,|,fontselect,|,fontsizeselect,|,del,ins,|,ltr,rtl,visualchars,nonbreaking,pagebreak",'.NEW_LINE.
					'		theme_advanced_toolbar_location : "top",'.NEW_LINE.
					'		theme_advanced_toolbar_align : "left",'.NEW_LINE.
					'		theme_advanced_statusbar_location : "bottom",'.NEW_LINE.
					'		theme_advanced_resizing : true,'.NEW_LINE.
					 
					'		// Example word content CSS (should be your site CSS) this one removes paragraph margins'.NEW_LINE.
					'		content_css : "/includes/word.css",'.NEW_LINE.
					 
					'		// Drop lists for link/image/media/template dialogs'.NEW_LINE.
					'		template_external_list_url : "lists/template_list.js",'.NEW_LINE.
					'		external_link_list_url : "lists/link_list.js",'.NEW_LINE.
					'		external_image_list_url : "lists/image_list.js",'.NEW_LINE.
					'		media_external_list_url : "lists/media_list.js",'.NEW_LINE.
					 
					'		// Replace values for the template plugin'.NEW_LINE.
					'		template_replace_values : {'.NEW_LINE.
					'			username : "Some User",'.NEW_LINE.
					'			staffid : "991234"'.NEW_LINE.
					'		}'.NEW_LINE.
					'	});'.NEW_LINE.
					'</script>'.NEW_LINE;
		
		return $editor;
	}

 	/**
 	 * Method to parse the querystring for the router class
 	 *
 	 * @access Private
 	 * @return Void
 	 */
	private function ParseQuerystring()
	{
		$this->Object 	= isset($_GET['obj']) 		? $_GET['obj'] 					: $this->Object;
		$this->Task 	= isset($_GET['task']) 		? '&task='.$_GET['task'] 		: '&task='.$this->Task;
		$this->Subtask 	= isset($_GET['subtask']) 	? '&subtask='.$_GET['subtask'] 	: '&subtask='.$this->Subtask;
		$this->Item		= isset($_GET['id']) 		? '&id='.$_GET['id'] 			: '&id='.$this->Item;
		
		$this->Action 		= '?obj='.$this->Object.$this->Task.$this->Subtask.$this->Item;
		$this->URL 			= '?obj='.$this->Object.$this->Task.$this->Item;
		$this->GenericPage 	= '?obj='.$this->Object.$this->Task;	
	}
}
?>