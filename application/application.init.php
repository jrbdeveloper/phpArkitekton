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
		$directories[] = 'modules/calendar';
		$directories[] = 'modules/footer';
		$directories[] = 'modules/logviewer';
		$directories[] = 'modules/menu';
		$directories[] = 'modules/metatags';
		$directories[] = 'modules/news';
		$directories[] = 'modules/panel';
		$directories[] = 'modules/person';
		$directories[] = 'modules/email';
		
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

	// Turn on error reporting and log all error to a log file
	error_reporting ( E_ERROR | E_WARNING | E_PARSE | E_NOTICE );
	ini_set ( 'display_errors', 'On' );
	ini_set ( 'log_errors', 'On' );
	ini_set ( 'error_log', 'modules/logviewer/data/error.log' );

	// Get the template and file from the URL; variables are first defined in the index page
	$template 	= isset($_GET['template']) 	? $_GET['template'] : $StartingTemplate;
	$file 		= isset($_GET['file']) 		? $_GET['file'] 	: $StartingFile;

	// Create the page and error logs objects
	$page 		= new Page($template, $file);
	$errorLogs 	= new LogViewer();
	
	// If we're trying to view the error logs load them otherwise load the page that was requested
	if(in_array(strtolower($file),$page->ErrorPages)) {
		print $errorLogs->Load();
	} elseif($file == "email") {
		try {
			if($page->Email->Send())
			{
				header('Location: index.php?template=main&file=home');
				exit;
			}
		}catch(ErrorException $e)
		{
			throw new ErrorException($e->getMessage(), $e->getCode(), $e->getSeverity(), $e->getFile(), $e->getLine(), $e->getPrevious());
		}
	}else {
		print $page->Load();
	}
	
	unset($errorLogs);
	unset($page);
?>