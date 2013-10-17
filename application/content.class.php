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

class Content extends Base
{
	/**
	 * Class constructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __construct($file=null)
	{		
		parent::__construct();
		
		$this->File = $file;		
		$this->Page = PAGES.$this->File.'.html';
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
	 * This function loads files from the pages directory to display page content
	 *
	 * @access Public
	 * @return String
	 */
	public function Load($file=null)
	{
		$strBuilder = new StringBuilder();
		
		if(!is_null($file))
			$this->File = $file;
			
		// We want to load the page directly from the database
		if($this->Object != '' && $this->Task != '')
		{
			$objRouter = new Router($_GET);
			$strBuilder->Append($objRouter->Route());
			unset($objRouter);
		}else 
		{
			if(file_exists($this->Page))
			{
				$Page = file($this->Page);
			
				foreach ($Page as $line) 
				{
					$strBuilder->Append($line);
				}
			
				unset($Page);
				
			}else 
			{
				$strBuilder->Append($this->ErrorHandler->Display(ERROR_CONTENT, $this->Page));
			}	
		}
		
		$return = $strBuilder->toString();
		unset($strBuilder);
		return $return;
	}
}
?>