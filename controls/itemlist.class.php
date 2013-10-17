<?
/**
 * @author John Bales
 * @copyright All Rights Reserved 2009
 * @package Controls
 * @name Item List
 * @uses Control Base Class
 * @version 1.0
 */
class ItemList extends ControlBase
{
	private $p_items;
	private $p_listType;
	private $p_startTag;
	private $p_endTag;
	private $p_cssClass;
	
	/**
	 * This is the class constructor
	 *
	 * @access Public
	 * @param String $type
	 * @param String $cssClass
	 * @return Void
	 */
	public function __construct($type=null, $id=null, $cssClass=null)
	{
		parent::__construct();
		
		$this->p_listType = $type;
		$this->p_listId = $id;
		$this->p_cssClass = $cssClass;
		$this->p_items = array();
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
	 * This method allows the developer to add items to the list manually
	 *
	 * @access Public
	 * @param String $text
	 * @return Void
	 */
	public function add($text)
	{
		switch ($this->p_listType)
		{
			case 'bulleted':
			case 'bullet':
				$this->p_items[] = '<li>'.$text.'</li>'.NEW_LINE;
				break;
				
			case 'numbered':
			case 'number':
				$this->p_items[] = '<li>'.$text.'</li>'.NEW_LINE;
				break;
				
			case 'none':
				$this->p_items[] = $text.'&nbsp;|'.NEW_LINE;
				break;
		}
	}
	
	/**
	 * This method allows the developer to fill the list from a database table
	 *
	 * @access Public
	 * @param String $sqlStmt
	 * @param Object $db
	 * @return Void
	 */
	public function fill($sqlStmt=null,$db=null)
	{
		if(is_array($sqlStmt))
		{
			if(count(array_keys($sqlStmt)) > 0)
			{
				foreach ($sqlStmt as $key => $value)
				{
					$this->p_items[] = NEW_LINE.'<li>'.$value.'</li>'.NEW_LINE;
				}
			}else
			{
				for($x=0; $x < count($sqlStmt); $x++)
				{
					$this->p_items[] = NEW_LINE.'<li>'.$sqlStmt[$x].'</li>'.NEW_LINE;
				}
			}
		}else
		{
			$results = &$db->getRecordset($sqlStmt);
			while($rs = &$db->getRecords($results))
			{
				$this->p_items[] = NEW_LINE.'<li>'.$rs[0].'</li>'.NEW_LINE;
			}
		}
	}
	
	/**
	 * This method renders the control to the screem
	 *
	 * @access Public
	 * @return String
	 */
	public function display()
	{
		$strBuilder = new StringBuilder();
		
		$this->setTags($this->p_listType);
		$strBuilder->Append($this->p_startTag);
		
		foreach ($this->p_items as $item)
		{
			$strBuilder->Append($item);
		}
		
		$strBuilder->Append($this->p_endTag);
		$return = $strBuilder->toString();
		unset($strBuilder);
		
		if($this->Visible)
			return $return;
		else
			return '';
	}
	
	/**
	 * This method sets the appropriate tags for the list
	 *
	 * @access Private
	 * @param String $listType
	 * @return Void
	 */
	private function setTags($listType)
	{
		switch($listType)
		{
			case 'bulleted':
			case 'bullet':
				$this->p_startTag 	= isset($this->p_cssClass) ? '<ul id="'.$this->p_listId.'" class="'.$this->p_cssClass.'">' : '<ul>'.NEW_LINE;
				$this->p_endTag 	= '</ul>'.NEW_LINE;
				break;
				
			case 'numbered':
			case 'number':
				$this->p_startTag 	= isset($this->p_cssClass) ? '<ol id="'.$this->p_listId.'" class="'.$this->p_cssClass.'">' : '<ol>'.NEW_LINE;
				$this->p_endTag 	= '</ol>'.NEW_LINE;
				break;
				
			case 'none':
				$this->p_startTag 	= '|&nbsp;';
				$this->p_endTag 	= '';
				break;
		}
	}
}
?>