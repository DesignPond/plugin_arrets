<?php

class Arret_AutoLoader {
     
    static private $classNames = array();
     
    /**
    * Store the filename (sans extension) & full path of all ".php" files found
    */
    public static function registerDirectory($dirName) {
     
	    $di = new DirectoryIterator($dirName);
	    foreach ($di as $file) 
	    {
		    if ($file->isDir() && !$file->isLink() && !$file->isDot()) {
		    	// recurse into directories other than a few special ones
		   		self::registerDirectory($file->getPathname());
		    } elseif (substr($file->getFilename(), -4) === '.php') {
		    	// save the class name / path of a .php file found
				$className = substr($file->getFilename(), 0, -4);
				Arret_AutoLoader::registerClass($className, $file->getPathname());
		    }
	    }
    }
     
    public static function registerClass($className, $fileName) {
   	 	Arret_AutoLoader::$classNames[$className] = $fileName;
    }
     
    public static function loadClass($className) {
   		if (isset(Arret_AutoLoader::$classNames[$className])) {
	   		 require_once(Arret_AutoLoader::$classNames[$className]);
	   	}
    }
     
}
     
spl_autoload_register(array('Arret_AutoLoader', 'loadClass'));