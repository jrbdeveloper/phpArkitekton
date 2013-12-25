<?php
/**
 * @author John Bales
 * @copyright All Rights Reserved 2009
 * @package Controls
 * @name Grid
 * @uses Control Base Class
 * @version 1.0
 */
class DataGrid extends ControlBase
{
	private $Properties 	= array();
	private $columns 		= array();
	private $colsByIndex 	= array();
	private $customCol 		= array();
	private $colLocked 		= array();
	private $customCommand 	= array();
	private $colVisable 	= array();
	private $colControl 	= array();
	private $filters 		= array();
	private $wasExecuted 	= false;
	
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
	 * @param String $ID
	 * @param String $tableName
	 * @param String $dataKeyName
	 * @param Boolean $enableEdit
	 * @param Boolean $endableUpdate
	 * @param Boolean $enableDel
	 * @param Boolean $enableInsert
	 * @param Boolean $enableSel
	 * @param Boolean $enableSort
	 * @return Void
	 */
	public function __construct($ID=null, $tableName=null, $dataKeyName=null, $enableEdit=null, $endableUpdate=null, $enableDel=null, $enableInsert=null, $enableSel=null, $enableSort=null)
	{
		parent::__construct();

		$this->ID 			= empty($ID) ? null : $ID;
		$this->Name 		= $this->ID;
		$this->Height 		= '';
		$this->Width 		= '100%';
		$this->CellPadding 	= 1;
		$this->CellSpacing 	= 1;
		$this->Border 		= 0;
		$this->Visible 		= true;
		$this->ButtonType 	= 'image';
		$this->CssClass 	= 'listing';
		$this->Style 		= '';

		$this->TableName 		= empty($tableName) 	? null 		: $tableName;
		$this->DataKeyName 		= empty($dataKeyName) 	? null 		: $dataKeyName;
		
		$this->SelectCommand 	= '';
		$this->UpdateCommand 	= '';
		$this->DeleteCommand 	= '';
		
		$this->EnableEditing 	= empty($endableUpdate) ? false 	: $endableUpdate;
		$this->EnableUpdating 	= empty($enableEdit) 	? false 	: $enableEdit;
		$this->EnableDeleting 	= empty($enableDel) 	? false 	: $enableDel;
		$this->EnableInsert 	= empty($enableInsert) 	? false 	: $enableInsert;
		$this->EnableSorting 	= empty($enableSort) 	? false 	: $enableSort;
		$this->EnableSelect 	= empty($enableSel) 	? false 	: $enableSel;
		
		$this->EditText 	= 'Edit';
		$this->DeleteText 	= 'Delete';
		$this->CancelText 	= 'Cancel';
		$this->SelectText 	= 'Select';
		$this->InsertText 	= 'Insert';
		
		$this->EditImageUrl 	= EDIT_ICON;
		$this->SelectImageUrl 	= '';
		$this->DeleteImageUrl 	= DELETE_ICON;
		$this->SaveImageUrl 	= SAVE_ICON;
		$this->CancelImageUrl 	= CANCEL_ICON;
		
		$this->object 	= isset($_GET['obj']) 		? $_GET['obj'] 		: null;
		$this->task 	= isset($_GET['task']) 		? $_GET['task'] 	: null;
		$this->subtask 	= isset($_GET['subtask']) 	? $_GET['subtask'] 	: null;
		$this->param 	= isset($_GET['id']) 		? $_GET['id'] 		: null;
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
	 * This method make it possible to add filters to the where clause of the SQL statement
	 *
	 * @access Public
	 * @param String $logic
	 * @param String $name
	 * @param String $operator
	 * @param Variant $value
	 * @return Void
	 */
	public function AddFilter($logic=null,$name=null,$operator=null,$value=null)
	{
		if(is_string($value))
			$value = '"'.$value.'"';
			
		$this->filters[] = $logic.' '.$name.' '.$operator.' '.$value.' ';
	}
	
	/**
	 * This method generates the select statment and applies the filters
	 *
	 * @access Public
	 * @param String $tableName
	 * @param String $cols
	 * @return Void
	 */
	public function SetSelect($cols=null)
	{
		if($this->SelectCommand == '')
		{
			$filter = '';
			$newcols = '';
			
			if(is_array($this->filters) && count($this->filters) > 0)
			{
				$filter = " WHERE ";
				for($x=0; $x < count($this->filters); $x++)
				{
					$filter .= $this->filters[$x];
				}
			}
			
			if(is_array($this->columns) && count($this->columns) > 0)
			{
				foreach ($this->columns as $key => $value)
				{
					$newcols .= $key . " AS '".$value."', ";
				}
				$this->SelectCommand = 'SELECT '.$this->DataKeyName.','.substr($newcols,0,strrpos($newcols,',')).' FROM '.$this->TableName.$filter;
			}else
			{
				$this->SelectCommand = 'SELECT '.$this->DataKeyName.','.$cols.' FROM '.$this->TableName.$filter;
			}
		}
	}
	
	/**
	 * This method binds the grid to the data source
	 *
	 * @access Public
	 * @param String $tableName
	 * @param String $cols
	 * @return Void
	 */
	public function Bind($cols=null)
	{
		//$this->setSelect($this->TableName,$cols);
		$this->setSelect($cols);
	}
	
	/**
	 * This method is used to display the grid in the interface
	 *
	 * @access Public
	 * @return String
	 */
	public function Display()
	{
		$this->Bind();
		
		$cntr = 0;
		$printed_headers = false;
		$isCustom = '';
		$colAlign = '';
		
		$return = 	'<div class="table" id="'.$this->ID.'">'.NEW_LINE.
					'<form name="'.$this->ID.'" method="post">'.NEW_LINE.
					'<table '.
						'cellpadding="'.$this->CellPadding.'" '.
						'cellspacing="'.$this->CellSpacing.'" '.
						'border="'.$this->Border.'" '.
						'height="'.$this->Height.'" '.
						'width="'.$this->Width.'" '.
						'style="'.$this->Style.'" '.
						'class="'.$this->CssClass.'">'.NEW_LINE;
		
		//print $this->controler($this->subtask, $this->param, $_POST);
		
		if($this->controler($this->subtask, $this->param, $_POST) != '')
		{
			//$this->Database->Open();
			//$this->Database->execute($this->controler($this->subtask,$this->param,$_POST));
			//$this->Database->Close();
			header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?obj='.$this->object.'&task='.$this->task.'&subtask=&id=');
		}else
		{
			$this->Database->Open();
			$rs = $this->Database->getRecordset($this->SelectCommand);
			
			if(!empty($rs))
			{			
				while($row = $this->Database->getRecords($rs))
				{
					$count = 0;
					if(!$printed_headers)
					{
						//print the headers once:
						$return .= '<tr>'.NEW_LINE;
						
						if($this->EnableEditing || $this->EnableDeleting || $this->EnableSelecting)
							$return .= '<th width="25">&nbsp;</th>'.NEW_LINE;
						
						foreach(array_keys($row) AS $header)
						{
							//you have integer keys as well as string keys because of the way PHP handles arrays.
							if(!is_int($header) && $header != $this->DataKeyName)
								$return .= '<th nowrap>'.$header.'</th>'.NEW_LINE;
						}
						
						$return .= '</tr>'.NEW_LINE;
						$printed_headers = true;
					}
				    
					//print the data row
					$return .= '<tr'.$this->alter($cntr).'><a name="'.$row[$this->DataKeyName].'">'.NEW_LINE;
		
					if($this->EnableEditing || $this->EnableDeleting)
						$return .= '<td nowrap align="center">'.$this->GetCommandButtons($this->subtask, $this->param, $row[$this->DataKeyName]).'</td>'.NEW_LINE;
		
					foreach($row AS $key=>$value)
					{
						if(!is_int($key) && $key != $this->DataKeyName)
						{
							if($this->customCol[$count] == $this->colsByIndex[$count])
							{
								$isCustom = true;
								$colAlign = ' align="center" ';
							}else
							{
								$isCustom = false;
								$colAlign = ' align="left" ';
							}
								
							$return .= '<td'.$colAlign.'>';
		
							if($this->param == $row[$this->DataKeyName])
							{
								// Check to see if we need to show a custom column
								if($isCustom)
									$return .= '<a href="'.$this->customCommand[$count].'&id='.$value.'" title="Edit"><img src="'.$this->EditImageUrl.'" border="0"></a>';
								else
								{
									if((count($this->colLocked) > 0) && $this->colLocked[$count])
										$return .= $value;
									else
									{
										if(!empty($this->colControl[$count]))
											$return .= $this->GetEditFields($this->colsByIndex[$count], $value, $this->colControl[$count]);
										else 
											$return .= $this->GetEditFields($this->colsByIndex[$count], $value);
									}
										
								}
									
							}elseif($this->param != $row[$this->DataKeyName])
							{
								// Check to see if we need to show a custom column
								if($isCustom)
									$return .= '<a href="'.$this->customCommand[$count].'&id='.$value.'" title="Edit"><img src="'.$this->EditImageUrl.'" border="0"></a>';
								else
									$return .= $value.'&nbsp;';
							}
								
							$return .= '</td>'.NEW_LINE;
						
							$count++;
						}
					}
					$return .= '</tr>'.NEW_LINE;
					$cntr++;
				}
			}
		}
		
		$return .= 	'</table>'.NEW_LINE.
					'</form>'.NEW_LINE.
					'</div>'.NEW_LINE;
		
		if(!empty($rs))
			$this->Database->Close($rs);
		else
			$this->Database->Close();
		
		if($this->Visible)
			return $return;
		else
			return '';
	}
	
	/**
	 * This method presents a text box for editing for the selected row of the grid
	 * 
	 * @access Private
	 * @param String $name
	 * @param String $value
	 * @return String
	 */
	private function GetEditFields($name, $value, $control=null)
	{
		if(!empty($control))
		{
			$editField = $control->display('',$value);
		}
		else
			$editField = '<input type="text" name="'.$name.'" value="'.$value.'" style="width:100%;" class="">';
			
		return $editField;
	}

	/**
	 * This method adds the columns to the grid control, generic columns and/or command columns can be added using this method
	 *
	 * @access Public
	 * @param String $name
	 * @param String $text
	 * @param Boolean $custom
	 * @param String $obj
	 * @param String $task
	 * @return Void
	 */
	public function AddColumn($name=null, $text=null, $custom=null, $obj=null, $task=null, $locked=null, $visable=null, $control=null)
	{
		$this->columns[$name] 	= $text;
		$this->colsByIndex[] 	= $name;
		
		// Used to present a control other than a textbox in edit mode
		$this->colControl[] 	= $control;
		
		// Add a custom column to this array index
		if($custom)
		{
			$this->customCol[] 		= $name;
			$this->customCommand[] 	= '?obj='.$obj.'&task='.$task.'&subtask=edit';
		}else // Not a custom column
		{
			$this->customCol[] 		= '';
			$this->customCommand[] 	= '';
		}
		
		// Used to prevent editing of a paticular column/field
		if($locked)
			$this->colLocked[] = true;
		else
			$this->colLocked[] = false;
			
		if($visable)
			$this->colVisable[] = true;
		else
			$this->colVisable[] = false;
	}
	
	/**
	 * This method prepares the command buttons to be displayed
	 *
	 * @access Private
	 * @param String $qsTask
	 * @param Integer $qsID
	 * @param Integer $dbID
	 * @return String
	 */
	private function GetCommandButtons($qsTask, $qsID, $dbID)
	{
		$buttons = '';
		
		if(!isset($qsTask))
		{
			if($this->EnableEditing)
				$buttons = 	$this->GetButtonType('Edit',$dbID,$this->ButtonType,$this->EditImageUrl).NEW_LINE;
			
			if($this->EnableDeleting)
				$buttons.= $this->GetButtonType('Delete',$dbID,$this->ButtonType,$this->DeleteImageUrl).NEW_LINE;
				
		}elseif($qsID == $dbID)
		{
			$buttons =	"<table cellpadding=\"0\" cellspacing=\"0\">".NEW_LINE.
						"	<tr>".NEW_LINE.
						"		<td style=\"border:none;\">".$this->GetButtonType('Save',$dbID,$this->ButtonType,$this->SaveImageUrl)."</td>".NEW_LINE.
						"		<td style=\"border:none;\">".$this->GetButtonType('Cancel',$dbID,$this->ButtonType,$this->CancelImageUrl)."</td>".NEW_LINE.
						"	</tr>".NEW_LINE.
						"</table>";
		}elseif($qsID != $dbID)
		{
			if($this->EnableEditing)
				$buttons = 	$this->GetButtonType('Edit',$dbID,$this->ButtonType,$this->EditImageUrl).NEW_LINE;
				
			if($this->EnableDeleting)
				$buttons.= $this->GetButtonType('Delete',$dbID,$this->ButtonType,$this->DeleteImageUrl).NEW_LINE;
		}
		
		return $buttons;
	}
	
	/**
	 * This method constructs the type of command button to be used in the grid
	 *
	 * @access Private
	 * @param String $task
	 * @param Integer $id
	 * @param String $btnType
	 * @param String $imgPath
	 * @return String
	 */
	private function GetButtonType($task, $id, $btnType, $imgPath=null)
	{
		if(strtolower($task) == 'cancel')
			$action = '?obj='.$this->object.'&task='.$this->task;
		else
			$action = '?obj='.$this->object.'&task='.$this->task.'&subtask='.strtolower($task).'&id='.$id.'#'.$id;
			
		switch (strtolower($btnType))
		{
			case 'link':
				$return = '<a href="'.$action.'" title="'.$task.'">'.$task.'</a>&nbsp;';
				break;
				
			case 'button':
				$return = '<input type="button" name="button" value="'.$task.'" onclick="location.href=\''.$action.'\'" title="'.$task.'">';
				break;
				
			case 'image':
				if($task == 'Edit' && $task != 'Delete')
					$return = '<a href="'.$action.'" title="'.$task.'"><img src="'.$imgPath.'" border="0" style="padding:3px;"></a>&nbsp;';
				else
				{
					if($task == 'Delete')
						$return = '<a href="'.$action.'" title="'.$task.'" onclick="return confirm(\'Are you sure you want to delete this record?\');"><img src="'.$imgPath.'" border="0" style="padding:3px;"></a>&nbsp;';
					elseif($task == 'Cancel')
						$return = '<a href="'.$action.'" title="'.$task.'"><img src="'.$imgPath.'" border="0" style="padding:3px;"></a>&nbsp;';
					else
						$return = '<input type="image" name="button" value="'.strtolower($task).'" id="'.strtolower($task).'" title="'.$task.'" src="'.$imgPath.'" style="padding:3px;">';
				}
				break;
		}
		
		return $return;
	}

	/**
	 * This is the grids internal controller, its purpose is to route to the appropriate save, cancel or delete functions
	 *
	 * @access Private
	 * @param String $task
	 * @param Integer $id
	 * @param Array $postVars
	 * @return String
	 */
	private function Controler($task,$id,$postVars)
	{
		$strSql = '';
		
		if(strtolower($task) == 'edit')
		{
			if(isset($postVars['button']))
			{
				switch(strtolower($postVars['button']))
				{
					case 'save':
						if($this->UpdateCommand == '')
							$strSql = 'UPDATE '.$this->TableName.' SET '.$this->getUpdateFields().' WHERE '.$this->DataKeyName.'='.$id;
						else
							$strSql = $this->UpdateCommand;
						
						$this->Database->Open();
						$this->Database->execute($strSql);
						$this->Database->Close();
						break;
						
					case 'cancel':
						header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'?obj='.$_GET['obj'].'&task='.$_GET['task'].'&subtask=&id=');
						break;
				}
			}
		}elseif(strtolower($task) == 'delete')
		{
			$refCols = '';
			$refTables = '';
			
			$query = 	"SELECT ".
						"	TABLE_NAME, ".
						" 	COLUMN_NAME,".
						"	REFERENCED_TABLE_NAME, ".
						"	REFERENCED_COLUMN_NAME ".
						"FROM information_schema.KEY_COLUMN_USAGE ".
						"WHERE CONSTRAINT_SCHEMA = 'gstour' ".
						"	AND (TABLE_NAME='".$this->TableName."' OR REFERENCED_TABLE_NAME ='".$this->TableName."') ".
						"	AND COLUMN_NAME != 'DivisionID' ";

			if(!$this->wasExecuted)
			{
				$this->Database->Open();
				$result = $this->Database->getRecordset($query);
				$rowsQuery = '';
				
				while ($row = $this->Database->getRecords($result))
				{
					if(!empty($row['REFERENCED_TABLE_NAME']) && !empty($row['REFERENCED_COLUMN_NAME']))
					{
						if($row['TABLE_NAME'] == $this->TableName)
						{
							$rowsQuery = 	"SELECT ".
											"	".$row['REFERENCED_TABLE_NAME'].".*,".
											"	".$this->TableName.".* ".
											"FROM ".
											"	".$row['REFERENCED_TABLE_NAME'].",".
											"	".$this->TableName." ".
											"WHERE ".$row['REFERENCED_TABLE_NAME'].".".$row['REFERENCED_COLUMN_NAME']."=".$row['TABLE_NAME'].".".$row['COLUMN_NAME']." ".
											"	AND ".$this->TableName.".".$this->DataKeyName."=".$id; 
							
							if($this->Database->getRowCount('',$rowsQuery) > 0)
							{
								$refTables .= $row['REFERENCED_TABLE_NAME'].".*, ";
								$refCols .= " INNER JOIN ".$row['REFERENCED_TABLE_NAME']." ON ".$row['REFERENCED_TABLE_NAME'].".".$row['REFERENCED_COLUMN_NAME']."=".$row['TABLE_NAME'].".".$row['COLUMN_NAME'];
							}
						}
						else
						{
							$rowsQuery = 	"SELECT ".
											"	".$row['TABLE_NAME'].".*,".
											"	".$this->TableName.".* ".
											"FROM ".
											"	".$row['TABLE_NAME'].",".
											"	".$this->TableName." ".
											"WHERE ".$row['TABLE_NAME'].".".$row['COLUMN_NAME']."=".$row['REFERENCED_TABLE_NAME'].".".$row['REFERENCED_COLUMN_NAME']." ".
											"	AND ".$this->TableName.".".$this->DataKeyName."=".$id;
							
							if($this->Database->getRowCount('',$rowsQuery) > 0)
							{
								$refTables .= $row['TABLE_NAME'].".*, ";
								$refCols .= " INNER JOIN ".$row['TABLE_NAME']." ON ".$row['TABLE_NAME'].".".$row['COLUMN_NAME']."=".$row['REFERENCED_TABLE_NAME'].".".$row['REFERENCED_COLUMN_NAME'];
							}
						}
					}
				}
				
				$strSql = 	"DELETE ".$this->TableName.".*, ".substr($refTables,0,-2)." ".
							"FROM ".$this->TableName." ".$refCols." ".
							"WHERE ".$this->TableName.".".$this->DataKeyName."=".$id;

				$this->wasExecuted = true;
			}
			
			$this->Database->execute($strSql);
			$this->Database->Close();
		}
		
		return $strSql;
	}
	
	/**
	 * This method gets the update text field values for the grid from the $_POST collection
	 *
	 * @access Private
	 * @return String
	 */
	private function GetUpdateFields()
	{
		$keyValues = '';
		foreach($_POST AS $key=>$value)
		{
			if($key != 'button_x' && $key != 'button_y' && $key != 'button')
			{
				if(is_int($value))
					$keyValues .= $key."=".$value.",";
				else
					$keyValues .= $key."='".$value."',";
			}
		}
		return substr($keyValues,0,strrpos($keyValues,','));
	}
	
	/**
	 * This method gets a column from the collection by name
	 *
	 * @access Private
	 * @param String $name
	 * @return String
	 */
	private function GetColumn($name)
	{
		if (array_key_exists($name, $this->columns))
			return $this->columns[$name];
		else
			return '';
	}
	
	/**
	 * This method gets all the columns from the collection
	 *
	 * @access Private
	 * @return Array
	 */
	private function GetColumns()
	{
		return $this->columns;
	}

	/**
	 * This method sets the alter CSS class for all HTML tables
	 * 
	 * @access Protected
	 * @param Integer $counter
	 * @return String
	 */
	protected function alter($counter)
	{
		// For every other row alternate the colors to be used in table rows
		if($counter % 2)
			return ' class="bg"';
		else
			return '';
	}
}
?>