<?php
	/**
	 * This method sets the directories array to find classes in
	 *
	 * @param String $dir
	 * @return Array
	 */
	function get_dirs($dir)
	{
		$directories = array();
		$directories[] = 'application/';
		$directories[] = 'controls/';
		
		global $dirs;
		
		if (!isset($dirs))
			$dirs = '';
		
		if(substr($dir,-1) !== '/')
			$dir .= '/';
		
		if ($handle = opendir($dir))
		{
			while (false !== ($file = readdir($handle)))
			{
				if (filetype($dir.$file) === 'dir' && $file != "." && $file != "..")
				{
					clearstatcache();
					$dirs .= $file;
					$directories[] = $dir . $file.'/';
					//get_dirs($dir . $file);
				}
			}
			closedir($handle);
		}
		
		//return $dirs;
		return $directories;
	}
 
	/**
	 *  Class or Interface name automatically passed to this function by the PHP Interpreter
	 * 
	 * @param string $className
	 * @return Void             
	 */
	function autoLoader($className)
	{
	    foreach(get_dirs('modules/') as $directory)
	    {    	
        	if(file_exists(strtolower($directory.$className.'.class.php')))
            {
            	//print strtolower($directory.$className.'.class.php').'<br>';
                include_once strtolower(strtolower($directory.$className.'.class.php'));
            }
	    }
	}

	spl_autoload_register('autoLoader');

	$content = new Content();

	print $content->Load();
?>