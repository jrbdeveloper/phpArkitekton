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

class LogViewer extends Base
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
		$this->Interface 	= TEMPLATES.'main.html';
		$this->LogFile	= LOGS.'error.log';
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
	 * This function loads the course interface into the page
	 *
	 * @access Public
	 * @return String
	 */
	public function Load()
	{
		$strBuilder = new StringBuilder();
		
		if(file_exists($this->Interface))
		{
			$template = file($this->Interface);
		
			for($x = 0; $x < count($template); $x ++) 
			{
				$strBuilder->Append($this->PopulateTemplate($template, $x));
			}
		
			unset($template);
		}
		else
		{
			$strBuilder->Append($this->ErrorHandler->Display(ERROR_TEMPLATE, $this->Interface));
		}
		
		$return = $strBuilder->toString();
		unset($strBuilder);
		
		return $return;
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
		$interface = str_replace('{title}','Log File',$template[$counter]);
		$interface = str_replace('{keywords}','',$interface);
		$interface = str_replace('{description}','',$interface);
		$interface = str_replace('{menu}','',$interface);
		$interface = str_replace('{content}',$this->ParseLogFile(),$interface);
		$interface = str_replace('{footer}',SITENAME.' error log viewer',$interface);
		
		return $interface;
	}
	
	/**
	 * This method attempts to open the log file for parsing
	 *
	 * @access Private
	 * @return String
	 */
	private function ParseLogFile()
	{
		$strBuilder = new StringBuilder();
		
		if(file_exists($this->LogFile))
		{
			$logFile = file($this->LogFile);
				
			$strBuilder->Append('<table cellpadding="5" cellspacing="4">');
			
			for($y = 0; $y < count($logFile); $y++)
			{
				if($y % 2)
					$bgcolor = ' bgcolor="#eee" ';
				else
					$bgcolor = ' bgcolor=#fff; ';
					
				$strBuilder->Append('<tr><td'.$bgcolor.'>'.trim($logFile[$y]).'</td></tr>');
			}
			
			$strBuilder->Append('</table>');
		}else
		{
			$strBuilder->Append(ErrorHandler::Display(ERROR_FILE, $this->LogFile));
		}
		
		$return = $strBuilder->toString();
		unset($strBuilder);
		
		return $return;
	}
}
?>