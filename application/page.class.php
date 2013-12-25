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

class Page extends Base
{
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @param String $template
	 * @param String $page
	 * @return Void
	 */
	public function __construct($template=null, $file=null)
	{		
		parent::__construct();
		
		// Set the template and file properties
		$this->Template 	= $template;
		$this->File 		= $file;
		$this->Interface 	= TEMPLATES.$this->Template.'.html';
		
		// Load the error pages array with options to use for the page name
		$this->ErrorPages = array('logviewer','logs','log','errors','errorpage','exceptions','errorlogs','errorlog');
		
		// Load Objects
		$this->LoadObjects();		
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
		
		// Destroy Objects
		unset($this->Content);
		unset($this->Footer);
		unset($this->Menu);
		unset($this->News);
		unset($this->Panel);
		unset($this->MetaTags);
		unset($this->Email);
	}

	/**
	 * Method to instantiate all required objects
	 * 
	 * @access Private
	 * @return Void
	 */
	private function LoadObjects()
	{
		$this->MetaTags 	= new MetaTag($this->File);
		$this->Content 		= new Content($this->File);
		$this->Menu			= new Menu();
		$this->News 		= new News();
		$this->Panel 		= new Panel();
		$this->Footer 		= new Footer();
		$this->Email 		= new Email();
	}
	
	/**
	 * This function is called from the front end php files to display the page
	 *
	 * @access Public
	 * @return String
	 */
	public function Load()
	{
		$return = '';
		if(file_exists($this->Interface))
		{
			$return = $this->LoadInterface($this->Interface);
			
			$return = str_replace('{metatags}',$this->MetaTags->Load(),$return);
			$return = str_replace('{tagline}',TAGLINE,$return);
			$return = str_replace('{menu}',$this->Menu->Load(),$return);
			$return = str_replace('{content}',$this->Content->Load(),$return);
			$return = str_replace('{news}',$this->News->Load(3),$return);
			$return = str_replace('{panel}',$this->Panel->Load('Social Links'),$return);
			$return = str_replace('{footer}',$this->Footer->Load(),$return);
		}else
		{
			$return = $this->ErrorHandler->Display(ERROR_TEMPLATE, $this->Template);
		}
		$return .= '<div style="color:#666; font-size:xx-small; text-align:center;" align="center"><a href="http://www.phparkitekton.com" target="_NEW">phpArkitekton</a> Version '.APPVERSION.'</div>';
		
		return $return;
	}
}
?>