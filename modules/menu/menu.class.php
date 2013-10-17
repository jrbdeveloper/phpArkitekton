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
		$this->Document		= MODULES.'menu/data/menu.xml';
		$this->Element 		= 'menu';
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
	 * @return String
	 */
	public function Load()
	{
		$x = 0;
		$itemList = new ItemList("bullet", "udm", "udm");
		// id="udm" class="udm"
		// Load the data (XML)
		$data = $this->LoadData($this->Document,$this->Element);
		
		foreach ($data AS $elem)
		{
			// Get the values from the xml data file
			$p_template = trim($elem->getElementsByTagName('template')->item(0)->nodeValue);
			$p_file 	= trim($elem->getElementsByTagName('page')->item(0)->nodeValue);
			$p_target 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('target'));
			$p_type 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('type'));
			$p_isLast 	= trim($elem->getElementsByTagName('page')->item(0)->getAttribute('islast'));
			$p_link 	= trim($elem->getElementsByTagName('link')->item(0)->nodeValue);
			$p_isLastClass = ($p_isLast == 'true') ? "last" : "";
			
			$hyperLink = new Hyperlink();
			$hyperLink->Target = $p_target;
			$hyperLink->CssClass = $p_isLastClass;
				
			if($p_type == 'file' || $p_type == 'document' || $p_type == 'pdf' || $p_type == 'doc' || $p_type == 'download')
			{
				if($this->DevMode)
					$itemList->add($hyperLink->Display($p_file,$p_link).$this->getSublinks($elem, $x, $p_template));
				else
					$itemList->add($hyperLink->Display("/".$p_file."/",$p_link).$this->getSublinks($elem, $x, $p_template));
			}else
			{
				if($this->DevMode)
					$itemList->add($hyperLink->Display("index.php?template=".$p_template."&file=".$p_file,$p_link).$this->getSublinks($elem, $x, $p_template));
				else
					$itemList->add($hyperLink->Display("/".$p_template."/".$p_file."/",$p_link).$this->getSublinks($elem, $x, $p_template));
			}
			
			$x++;
		}
		
		$return = $itemList->display();
		
		unset($itemList);
		unset($hyperLink);
		unset($data);
		
		return trim($return);
	}
	
	/**
	 * This function checks for sublinks in the menu data file and adds them to drop-down menu panels
	 * 
	 * @param Integer $counter
	 * @param String $template
	 * @return String
	 */
	private function getSublinks($parent, $counter, $template)
	{
		$itemList = new ItemList("bullet");
		$sublink = $parent->getElementsByTagName('sublink');
				
		if($counter <= $sublink->length)
		{
			foreach ($sublink AS $nested)
			{
				$sub_file 	= trim($nested->getElementsByTagName('page')->item(0)->nodeValue);
				$sub_target = trim($nested->getElementsByTagName('page')->item(0)->getAttribute('target'));
				$sub_link 	= trim($nested->getElementsByTagName('link')->item(0)->nodeValue);
				$sub_type 	= trim($nested->getElementsByTagName('page')->item(0)->getAttribute('type'));
				
				$hyperLink = new Hyperlink();
				$hyperLink->Target = $sub_target;
				
				if($sub_type == 'file' || $sub_type == 'document' || $sub_type == 'pdf' || $sub_type == 'doc' || $sub_type == 'download')
				{
					if($this->DevMode)
						$itemList->add($hyperLink->Display($sub_file,$sub_link));
					else
						$itemList->add($hyperLink->Display("/".$sub_file."/",$sub_link));
				}else
				{
					if($this->DevMode)
						$itemList->add($hyperLink->Display("index.php?template=".$template."&file=".$sub_file,$sub_link));
					else
						$itemList->add($hyperLink->Display("/'.$template.'/'.$sub_file.'/",$sub_link));
				}
			}
			
			$return = $itemList->display();
			
			unset($itemList);
			unset($hyperLink);
			
			return trim($return);
		}
	}
}
?>