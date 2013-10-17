<?
/**
 * @author John Bales
 * @copyright All Rights Reserved 2009
 * @package Application
 * @name Application Rounter
 * @uses None
 * @version 1.0
 */
final class Router 
{
	/**
	 * Object Properties array
	 * @access Private
	 */
	private $object = array();

	/**
	 * Object constructor
	 *
	 * @access Public
	 * @param String $url
	 * @return Void
	 */
	public function __construct($url) 
	{
		// Get the name of the object to instantiate
		$this->object['object'] = isset($url['obj']) 	? ucfirst($url['obj']) 	: null;
		
		// Get the name of the method to call
		$this->object['method']	= isset($url['task']) 	? $url['task'] 			: null;
		
		// Get the value of the ID parameter
		$this->object['param']	= isset($url['id']) 	? $url['id'] 			: null;
	}
	
	/**
	 * Object destructor
	 *
	 * @access Public
	 * @return Void
	 */
	public function __destruct() 
	{
		// Destroy the object property array
		unset($this->object);
	}
	
	/**
	 * Preventing the object from being cloned
	 * 
	 * @access Public
	 * @return False
	 */
	private function __clone()
	{ return false; }
	
	/**
	 * Object router that dynamically instantiates the object and calls the specified method passing the specified parameter
	 * 
	 * @access Public
	 * @return Mixed
	 */
	public function Route()
	{
		// If the method contains a value use it otherwise set it to use the getbycriteria method
		$method = empty($this->object['method']) ? 'GetByCriteria' : $this->object['method'];
		
		// If the class exists as well as the method being called instantiate the object and call the method
		if(class_exists($this->object['object']) && method_exists($this->object['object'],$this->object['method']))
		{
			// The class and the method passed to the constructor was found, use them
			$this->object['class'][$this->object['object']] = new $this->object['object']($this->object['param']);
		}elseif(empty($this->object['object']) && empty($this->object['object']))
		{
			$this->object['object'] = 'Page';
			$method = 'Display';
			$this->object['param'] = 9;
			
			// Use the values established above to instantiate the object and call the method for displaying File not found info
			$this->object['class'][$this->object['object']] = new $this->object['object']($this->object['param']);
		}else
		{
			$this->object['object'] = 'ErrorHandler'; // Set the object to be instantiated to the error object
			$method = 'Display'; // Set the method to be call to the notfound method
			$this->object['param'] 	= 404; // Set the parameter to the value of 404
			
			// Use the values established above to instantiate the object and call the method for displaying File not found info
			$this->object['class'][$this->object['object']] = new $this->object['object']($this->object['param']);
		}
		
		// Return the value from the method called on the paticular object
		return $this->object['class'][$this->object['object']]->$method($this->object['param']);
	}
	
	/**
	 * This method is used to get the contents of the object array
	 * 
	 * @access Public
	 * @return Array
	 */
	public function ObjectArray()
	{
		return $this->object['class'][$this->object['object']];
	}
}
?>