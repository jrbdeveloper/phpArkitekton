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

class News extends Base 
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
		$this->Interface	= MODULES.'news/template/news.html';
		$this->Document		= MODULES.'news/data/news.xml';
		$this->Element 		= 'article';
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
	 * This function reads the contents of the news xml file and presents it to the interface
	 *
	 * @access Public
	 * @param Integer $count
	 * @return String
	 */
	public function Load($count=null)
	{
		$counter = 0;

		$strBuilder = new StringBuilder();
		
		if(is_null($count))
			$count=1;
			
		// Load the data (XML)
		$data = $this->LoadData($this->Document, $this->Element);
		
		foreach ($data AS $elem)
		{
			// We only want to load a certain number of records
			if($counter < $count)
			{
				if(file_exists($this->Interface))
				{
					// Load the template (HTML)
					$template = file($this->Interface);
				
					// Get the values from the xml data file
					$this->SetPropertiesFromData($elem);
				
					// Populate the interface with the data
					for($x = 0; $x < count($template); $x ++) 
					{
						$strBuilder->Append($this->PopulateTemplate($template, $x));
					}
				
					unset($template);
				}else
				{
					$strBuilder->Append($this->ErrorHandler->Display(ERROR_TEMPLATE, $this->Interface));
				}
			}
										
			$counter++;
		}
		
		$return = $strBuilder->toString();
		
		unset($strBuilder);
		unset($data);
		
		return trim($return);
	}
	
	/**
	 * Populate the template with the data
	 *
	 * @param string $template
	 * @param integer $counter
	 * @return string
	 */
	private function PopulateTemplate($template, $counter)
	{
		$interface = str_replace('{date}',$this->date,$template[$counter]);
		$interface = str_replace('{author}',$this->author,$interface);
		$interface = str_replace('{title}',$this->title,$interface);
		$interface = str_replace('{content}',$this->content,$interface);
		return $interface;
	}
	
	/**
	 * Set the object properties from the data
	 *
	 * @param string $elem
	 */
	private function SetPropertiesFromData($elem)
	{
		// Get the values from the xml data file
		$this->date 	= $elem->getElementsByTagName('date')->item(0)->nodeValue;
		$this->author 	= $elem->getElementsByTagName('author')->item(0)->nodeValue;
		$this->title 	= $elem->getElementsByTagName('title')->item(0)->nodeValue;
		$this->content 	= $elem->getElementsByTagName('content')->item(0)->nodeValue;
	}
}
?>