<?
/**
 * @copyright 2010 phpArkitekton
 * @version 1.8.1
 * @author John Bales
 * @package Controls
 * @name Text Box
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

class TextBox extends ControlBase
{
	/**
	 * This is the constructor for the text box class and it allows the developer to set properties on instantiation
	 *
	 * @access Public
	 * @param String $name
	 * @param String $id
	 * @param String $text
	 * @param String $width
	 * @param Integer $maxlen
	 * @param Bolean $readonly
	 * @param Bolean $enabled
	 * @return Void
	 */
	public function __construct($name=null, $id=null, $text=null, $width=null, $maxlen=null, $readonly=false, $enabled=true)
	{
		parent::__construct();
		
		$this->Name 		= $name;
		$this->ID 			= $id;
		$this->Text 		= $text;
		$this->Width 		= $width;
		$this->Maxlength 	= $maxlen;
		$this->ReadOnly 	= $readonly;
		$this->Enabled 		= $enabled;
		
		if(!$this->Enabled)
			$this->Enabled = 'disabled';
			
		if($this->ReadOnly)
			$this->ReadOnly = 'readonly';
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
	 * This method renders the control based on the properties set
	 *
	 * @access Public
	 * @return String
	 */
	public function Render()
	{
		if(is_string($this->Width))
			$p_width = $this->Width;
		else
			$p_width = $this->Width.'px';
		
		$strBuilder = new StringBuilder();
		
		if($this->TextMode == 'multiline')
		{
			if($this->Visible)
			{
				$strBuilder->Append("<script>\n"); 
				$strBuilder->Append("function textCounter(field,cntfield,maxlimit)\n");
				$strBuilder->Append("{\n");
				$strBuilder->Append("if (field.value.length > maxlimit) // if too long...trim it!\n");
				$strBuilder->Append("field.value = field.value.substring(0, maxlimit);\n");
				$strBuilder->Append("else\n");
				$strBuilder->Append("cntfield.value = maxlimit - field.value.length;\n");
				$strBuilder->Append("}\n");
				$strBuilder->Append("</script>\n");
				
				$strBuilder->Append("<table cellpadding=\"0\" cellspacing=\"0\" style=\"width:".$this->Width.";\">\n");
				$strBuilder->Append("<tr>\n");
				$strBuilder->Append("<td><textarea wrap=\"hard\" onKeyDown=\"textCounter(document.getElementById('".$this->ID."'),document.getElementById('".$this->ID."count'),".$this->Maxlength.")\" onKeyUp=\"textCounter(document.getElementById('".$this->ID."'),document.getElementById('".$this->ID."count'),".$this->Maxlength.")\" name=\"".$this->Name."\" id=\"".$this->ID."\" style=\"height:".$this->Height.";width:".$this->Width.";overflow:auto;\" cols=1 rows=1 wrap=hard ".$this->ReadOnly." ".$this->Enabled.">".$this->Text."</textarea></td>\n");
				$strBuilder->Append("</tr>\n");
				$strBuilder->Append("<tr>\n");
				$strBuilder->Append("<td align=\"right\"><input readonly type=\"text\" name=\"".$this->ID."count\" id=\"".$this->ID."count\" size=\"3\" maxlength=\"3\" value=\"".$this->Maxlength."\" style=\"text-align:right;border:none;\" /> characters left&nbsp;</td>\n");
				$strBuilder->Append("</tr>\n");
				$strBuilder->Append("</table>\n");
				
				$return = $strBuilder->toString();
				unset($strBuilder);
				return $return;
			}else
			{
				return '';
			}
		}else
		{
			$strBuilder->Append('<input');
			$strBuilder->Append('name="'.$this->Name.'"');
			$strBuilder->Append('id="'.$this->ID.'"');
			$strBuilder->Append('value="'.$this->Text.'"');
			$strBuilder->Append('style="width:'.$p_width.'"');
			$strBuilder->Append('maxlength="'.$this->Maxlength.'"');
			$strBuilder->Append($this->ReadOnly);
			$strBuilder->Append($this->Enabled);
			
			if(!$this->Visible)
			{
				$strBuilder->Append('type="hidden"');			
			}elseif($this->TextMode == "password")
			{
				$strBuilder->Append('type="password"');		
			}elseif($this->TextMode == 'Text')
			{
				$strBuilder->Append('type="text"');	
			}
			
			$strBuilder->Append('/>');
			
			$return = $strBuilder->toString();
			unset($strBuilder);
			return $return;
		}
	}

	/**
	 * This method overrides the render method
	 *
	 * @access Public
	 * @return String
	 */
	public function Display()
	{
		return $this->Render();
	}
}
?>