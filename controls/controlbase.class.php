<?php
/**
 * @copyright 2010 phpArkitekton
 * @version 1.8.1
 * @author John Bales
 * @package Controls
 * @name Control Base Class
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

class ControlBase extends Base
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
	 * Class constructor for setting the baseline properties for all controls
	 * 
	 * @access Public 
	 * @return Void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->TabIndex 	= '';
		$this->CssClass		= '';
		$this->ToolTip		= '';
		$this->Height 		= '';
		$this->Width		= '';
		$this->Name 		= '';
		$this->ID 			= '';
		$this->Text 		= '';
		$this->Style 		= '';
		$this->onClick 		= '';
		$this->TextMode		= 'Text';
		$this->Enabled		= true;
		$this->Visible 		= true;
		$this->ReadOnly		= false;
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
}
?>