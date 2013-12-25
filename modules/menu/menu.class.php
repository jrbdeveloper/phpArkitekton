<?php
/**
 * @copyright 2010 phpArkitekton
 * @version 1.8.5
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
class Menu extends Base
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
		$this->TopNav 	= NAVIGATIONDATA.'menu.xml';
		$this->LeftNav 	= NAVIGATIONDATA.'leftnav.xml';
		$this->Element 	= 'menu';
		$this->FileTypeList = array('file','document','pdf','doc','download');
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
	 * This function loads the navigation for the site; it supports a top nav and a left nav
	 *
	 * @access Public
	 * @param String $nav
	 * @return String
	 */
	public function Load($nav=null)
	{		
		if(is_null($nav)) {
			return $this->loadTop();
		}else {
			if($nav == 'top')
			{
				return $this->loadTop();
			} elseif($nav == 'left') {
				return $this->loadLeft();
			}
		}
	}

	/**
	 * Method is responsible for loading the top menu items
	 * 
	 * @access Private
	 * @return String
	 */
	private function loadTop()
	{
		// Load the data (XML)
		$data = $this->LoadData($this->TopNav,$this->Element);
		
		// creat a link object to be used to build the menu
		$objLink = new Link();
		
		$return = '<ul class="nav navbar-nav">';
		foreach ($data AS $elem)
		{
			// Get the values from the xml data file
			$p_template = trim($elem->getElementsByTagName('template')->item(0)->nodeValue);
			$p_file 	= trim($elem->getElementsByTagName('page')->item(0)->nodeValue);
			$p_target 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('target'));
			$p_type 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('type'));
			$p_isLast 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('islast'));
			$p_display 	= trim($elem->getElementsByTagName('link')->item(0)->getAttribute('display'));
			$p_link 	= trim($elem->getElementsByTagName('link')->item(0)->nodeValue);
			
			if($p_display == 'true')
			{
				$objLink->Target = $p_target;
				$objLink->Text = $p_link;
				
				$sublink = $elem->getElementsByTagName('sublink');
				
				if($sublink->length > 0) {
					$liClass = 'class="dropdown"';
					$custInclude = '<b class="caret"></b>';
					$objLink->CssClass = 'dropdown-toggle';
					$objLink->CustomAttr = "data-toggle=\"dropdown\"";
				}else {
					$custInclude = null;
					$liClass = '';
					$objLink->CustomAttr = '';
				}
				
				if(in_array($p_type, $this->FileTypeList)) { // Looking for document extensions	
					$objLink->Path = ($this->DevMode) ? $p_file : "/'.$p_file.'/";										
				} else { // Not a document
					$objLink->Path = ($this->DevMode) ? "index.php?template=".$p_template."&file=".$p_file : "/'.$p_template.'/'.$p_file.'/";	
				}
				
				$return .= '<li '.$liClass.'>' . $objLink->Display($custInclude) . $this->getSublinks($elem, $p_template) . '</li>';
			}
		}
		$return .= '</ul>';
		
		unset($objLink);
		unset($data);
		
		return $return;
	}
	
	/**
	 * Method is responsible for loading the left menu items
	 * 
	 * @access Private
	 * @return String
	 */
	private function loadLeft()
	{
		$return = '';
		
		// Load the data (XML)
		$data = $this->LoadData($this->LeftNav,$this->Element);
		
		// creat a link object to be used to build the menu
		$objLink = new Link();
		
		foreach ($data AS $elem)
		{
			// Get the values from the xml data file
			$p_template = trim($elem->getElementsByTagName('template')->item(0)->nodeValue);
			$p_file 	= trim($elem->getElementsByTagName('page')->item(0)->nodeValue);
			$p_target 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('target'));
			$p_type 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('type'));
			$p_isLast 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('islast'));
			$p_display 	= trim($elem->getElementsByTagName('link')->item(0)->getAttribute('display'));
			$p_link 	= trim($elem->getElementsByTagName('link')->item(0)->nodeValue);
			
			if($p_display)
			{
				$objLink->Target = $p_target;
				$objLink->Text = $p_link;
				
				if(in_array($p_type, $this->FileTypeList))
				{
					$objLink->Path = ($this->DevMode) ? $p_file : "/".$p_file."/";
				}else
				{
					$objLink->Path = ($this->DevMode) ? "index.php?template=".$p_template."&file=".$p_file : "/".$p_template."/".$p_file."/";
				}
				
				$return .= $objLink->Display();
			}
		}
		
		unset($objLink);
		unset($data);
		
		return $return;
	}
	
	/**
	 * This function checks for sublinks in the menu data file and adds them to drop-down menu panels
	 * 
	 * @param Integer $counter
	 * @param String $template
	 * @return String
	 */
	private function getSublinks($parent, $template)
	{
		$sublink = $parent->getElementsByTagName('sublink');
				
		if($sublink->length > 0)
		{
			// creat a link object to be used to build the menu
			$objLink = new Link();
			
			$return = '<ul class="dropdown-menu">';
			foreach ($sublink AS $nested)
			{
				$sub_file 	= trim($nested->getElementsByTagName('page')->item(0)->nodeValue);
				$sub_target = trim($nested->getElementsByTagName('page')->item(0)->getAttribute('target'));
				$sub_link 	= trim($nested->getElementsByTagName('link')->item(0)->nodeValue);
				
				$objLink->Target = $sub_target;
				$objLink->Text = $sub_link;
				
				$objLink->Path = ($this->DevMode) ? "index.php?template=".$template."&file=".$sub_file : "/".$template."/".$sub_file."/";
				
				$return .= "<li>" . $objLink->Display() . "</li>";
			}
			$return .= '</ul>';
			
			unset($objLink);
			
			return $return;
		}
	}
}
?>