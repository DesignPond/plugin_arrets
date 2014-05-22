<?php

class Log{
	
	public function __construct() {
						
	}
	
	public function write($message){
		
		$filename = plugin_dir_path(  dirname(dirname(__FILE__) ) ) . 'logs/log.txt';
		
		$handle  = fopen($filename, "r");
		$current = fread($handle, filesize($filename));
		
		fclose($handle);
		// Append a new person to the file
		$current .= $message."\n";
		// Write the contents back to the file
		file_put_contents($filename, $current , FILE_APPEND);

	}
}