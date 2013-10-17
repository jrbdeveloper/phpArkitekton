<?
class Serializer
{
	/** 
	* Main static function.
	* Returns serialized string according to format
	* @param mixed $obj
	* @param string $format
	* @throws exception if formt not supported
	* @return string
	*/
	public function Serialize($Object,$format='php')
	{
		if($format == '') 
			$format = 'php';
	
		$serializer = new Serializer();
		$method = $format.'_serialize';
	
		if(method_exists($serializer,$method))
			return $serializer->$method($Object);
	
		throw new Exception('Format "'.$format.'" not supported');
	}
	

	public function Deserialize($ObjectAsString,$format='php')
	{
		if($format == '') 
			$format = 'php';
			
		$serializer = new Serializer();
		$method = $format.'_deserialize';
	
		if(method_exists($serializer,$method))
			return $serializer->$method($ObjectAsString);
	
		throw new Exception('Format "'.$format.'" not supported');
	}
  
	/**
	* Php serialization
	* @param mixed $o
	* @return string
	*/
	function php_serialize($o)
	{
		return serialize($o);
	}
	
	function php_deserialize($objString)
	{
		return unserialize($objString);
	}
  
	/**
	* Javascript serialization
	* @param mixed $o
	* @return string
	*/
	function js_serialize($o){
		if(is_array($o))
		{
			$arr = array();
			$symbolic = false;
			
			foreach($o as $k=>$s)
			{
				if(!is_numeric($k))
				{
					$symbolic = true;
					break;
				}
			}
	
			if($symbolic)
			{
				foreach($o as $k=>$s)
				{
					$arr[] = $this->js_serialize($k).':'.$this->js_serialize($s);
				}
	
				return '{'.implode(',',$arr).'}';
			}
			else
			{
				foreach($o as $s)
				{
					$arr[] = $this->js_serialize($s);
				}
				
				return '['.implode(',',$arr).']';
			}
		}
		else if(is_object($o))
		{
			$arr = array();
			foreach(get_object_vars($o) as $k=>$s)
			{
				$arr[] = $this->js_serialize($k).':'.$this->js_serialize($s);
			}
	
			return '{'.implode(',',$arr).'}';
		}
		else
		{
			$o = str_replace('\\','\\\\',$o);
			$o = str_replace('"','\\"',$o);
			$o = str_replace("\n",'\\n',$o);
			$o = str_replace("\r",'\\r',$o);
			$o = str_replace("\t",'\\t',$o);
	
			return '"'.$o.'"';
		}
	}
  
	/**
	 * XML serialization
	 * @param mixed $o
	 * @return string
	 */
	private function xml_serialize($Object)
	{
		if(is_array($Object))
		{
			$ret = "";
			
			foreach($Object as $key=>$value)
			{
				if(is_object($value))
				{
					$ret .= $this->xml_serialize($value);
				}
				else
				{
					$ret .= "<".htmlspecialchars($key).">".$this->xml_serialize($value)."</".htmlspecialchars($key).">\n";
				}
			}
	
			return $ret;
		}
		else if(is_object($Object))
		{
			$ret = "<".get_class($Object).">\n";
			
			foreach(get_object_vars($Object) as $key=>$value)
			{
				if(is_array($value))
				{
					$ret .= $this->xml_serialize($value);
				}
				else
				{
					$ret .= "<".$key.">".$this->xml_serialize($value)."</".$key.">\n";
				}
			}
			
			$ret .= "</".get_class($Object).">\n";
	
			return $ret;
		}
		else
		{
			return htmlspecialchars($Object);
		}
	}
}
?>
