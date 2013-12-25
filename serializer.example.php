<?php
require_once('application/serializer.class.php');
require_once 'application/stringbuilder.class.php';

class Base
{
	/**
	 * Object property array
	 * @access Private
	 */
	public $Properties = array();
	
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
	 * Method gets either a collection or single instance of an object either by All or by property filter
	 *
	 * @param String $filePath
	 * @param String $propery
	 * @param String $value
	 * @return Object Array
	 */
	public function GetBy($filePath, $propery=null, $value=null)
	{
		$x = 0;
		$returnValue = null;
		$Serializer = new Serializer();
		$tmpArray[] = "";
		$objectArray[] = "";
		
		if(!$file = fopen($filePath, "r+")) 
		{ 
			$returnValue = "Error! Could not open the file."; 
			exit;
		} 
		else 
		{ 
			while (!feof ($file)) 
			{ 
				$buffer = fgets($file, 4096); 
				$tmpArray[$x] = $Serializer->Deserialize($buffer);
				$x++;
			} 
		
			// We are trying to retreive by a property value
			if(!is_null($propery))
			{
				if(!is_null($value))
				{
					foreach($tmpArray as $key=>$p)
					{
						if(!is_null($p) && $p->$propery == $value)
						{
							$objectArray[$key] = $p;
						}
					}
				}
			}
			else // We want to retreive all objects
			{
				$objectArray = $tmpArray;
			}
			
			$returnValue = $objectArray;	
		} 
		
		if(!fclose($file)) 
		{ 
			$returnValue = "Error! Could not close the file."; 
		}
		
		unset($Serializer);
		
		return $returnValue;
	}
}

class Person extends Base
{
	public function __construct()
	{
		$this->Address = new Address();
	}

	public function GetBy($propery=null, $value=null)
	{
		return parent::GetBy("../modules/sidebar/data/testData",$propery,$value);
	}
	
	public function Save($key=null)
	{
		$Serializer = new Serializer();
		$strToFind = $Serializer->Serialize($this);
		
		$file = file("../modules/sidebar/data/testData");
		if ($file) 
		{
			foreach ($file as $number=>$line) 
			{
				if($key == $number)
				{
					$lines[$number] = trim($strToFind);
				}else 
				{
					$lines[$number] = trim($line);
				}
			}
		} 
		
	    // Write $somecontent to our opened file.
	    $file = fopen("../modules/sidebar/data/testData","w");
		if (fwrite($file, implode("\n",$lines)) === FALSE) 
	    {
	        echo "Cannot write to file.";
	        exit;
	    }
	    
	    fclose($file); // Close the file.
		
	}
}

class Address extends Base
{
	public function __construct()
	{
		$this->Country = New Country();
	}	
}

class Country extends Base
{
	public function __construct()
	{
	}
}

function XmlTests()
{
	//Serialize in XML format
	$serializer = new Serializer();
	
	// Create a person object and assign in some properties
	$person = new Person();
	$person->FirstName = "John";
	$person->LastName = "Bales";
	$person->Address->Street = "9644 Seth Lane";
	$person->Address->City = "Santee";
	$person->Address->State = "CA";
	$person->Address->Zip = "92071";
	$person->Address->Country->Code = 1;
	$person->Address->Country->Name = "United States of America";
	$person->Address->Country->Abbreviation = "US";
	
	// Serialize the person object to an XML string
	$strXml = $serializer->Serialize($person,"xml");
	//print $strXmlValue;
	
	// Create a new person object graph from the serailized person object
	$newPerson = simplexml_load_string($strXml);
	//$xml = simplexml_load_file("../modules/sidebar/data/sidebars.xml");
	
	$strNewXml = $serializer->Serialize($newPerson,"xml");
	print $strNewXml;
	
	// Print out values based on walking each graph
	print $newPerson->Address->Street."<br>";
	print $person->Address->Street;
	
	// Dump the content of the object graph
	print "<pre>";
	print_r($newPerson);
	print "</pre>";
}

// Call the XML test method
//XmlTests();

function GenerateTestData()
{
	$Serializer = new Serializer();
	$Person = new Person();
	
	$Person->FirstName = "John";
	$Person->LastName = "Bales";
	$Person->Address->Street = "123 Main St";
	$Person->Address->City = "San Diego";
	$Person->Address->State = "CA";
	$Person->Address->Zip = "92120";
	$Person->Address->Country->Code = "1";
	$Person->Address->Country->Name = "United States";
	$Person->Address->Country->Abbreviation = "US";
	$serializedPerson = $Serializer->Serialize($Person);
	print $serializedPerson."\n";
	
	$Person->FirstName = "Mike";
	$Person->LastName = "Sneen";
	$Person->Address->Street = "432 Front St";
	$Person->Address->City = "San Diego";
	$Person->Address->State = "CA";
	$Person->Address->Zip = "92120";
	$Person->Address->Country->Code = "1";
	$Person->Address->Country->Name = "United States";
	$Person->Address->Country->Abbreviation = "US";
	$serializedPerson = $Serializer->Serialize($Person);
	print $serializedPerson."\n";
	
	$Person->FirstName = "Joe";
	$Person->LastName = "Henns";
	$Person->Address->Street = "456 Lake View Dr.";
	$Person->Address->City = "La Mesa";
	$Person->Address->State = "CA";
	$Person->Address->Zip = "92120";
	$Person->Address->Country->Code = "1";
	$Person->Address->Country->Name = "United States";
	$Person->Address->Country->Abbreviation = "US";
	$serializedPerson = $Serializer->Serialize($Person);
	print $serializedPerson."\n";
}
//GenerateTestData();

/*
$Person = new Person();
//$Person = $Person->GetBy("FirstName", "Joe");

$Person = $Person->GetBy();
foreach (array_keys($Person) as $key)
{
	if($Person[$key])
	{
		if($Person[$key]->FirstName == "Joe")
		{
			$Person[$key]->FirstName = "JOE";
			$Person[$key]->Save($key);
		}
	}
}


print "<pre>";
print_r($Person);
print "</pre>";
*/

$stringBuilder = new StringBuilder();
$stringBuilder->Append("Test");
$stringBuilder->Append("123");
print $stringBuilder->toString();

?>