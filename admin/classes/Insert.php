<?php

class Insert{

	protected $grab;

	protected $dates;
	
	protected $arrange;
	
	protected $database;
	
	protected $log;
		
	// Urls	
	protected $urlList;
	
	function __construct( $test = NULL ) {

		// Get classes		
		$this->grab     = new Grab;
		
		$this->dates    = new Dates($test);
				
		$this->arrange  = new Arrange;
		
		$this->log      = new Log;
		
		$this->database = new Database($test);
				
		// Urls		
		$root = 'http://relevancy.bger.ch/AZA/liste/fr/';
		
		$this->urlList  = ( get_option('dd_arrets_url_list') ? get_option('dd_arrets_url_list') : $root ); 
						
	}	
	
	/**
	 * MAIN FUNCTION
	 * Insert arrets for corresponding date => format ymmdd (ex:821001 my birtday!)
	*/
	public function insertForDate($date){
		
		// Grab list of arrets for the date in list															
		$arrets = $this->grab->getListDecisions($this->urlList, $date);	
		
		// Clean all arrets for each date
		$result = $this->arrange->cleanFormat($arrets , $date);
		
		// Update category list in DB				
		if(!empty($result['allCategories']))
		{
			$this->database->existCategorie($result['allCategories']);
		}

		// Prepare arrets
		if(!empty($result['allArrets']))
		{
			$arranged = $this->database->arrangeArret($result['allArrets']);			
		}	

		if(!empty($arranged))
		{
			// Insert new arrets
			if( ! $this->database->insertNewArrets($arranged) )
			{
				return false;
			}		
						
			return true;			
		}	
		
		return false;	
		
	}
	
	/**
	 * Get missing dates from TF
	*/
	public function insertMissingDates(){
		
		$arrets   = array();
		$arranged = array();
		
		// Get list of dates from TF		
		$dates = $this->grab->getLastDates($this->urlList);

		// Test if there's today's date in the list and if not in the database already				
		$toInsert = $this->dates->datesToUpdate($dates);
		
		if(!empty($toInsert))
		{
			foreach($toInsert as $list)
			{	
				if(!$this->insertForDate($list))
				{
					return false;	
				}
			}
			
			return true;
		}
		
		// LOGGING
		$this->log->write('Nothing to insert');
		// END LOGGIN	

		return false;	
	}
	
	
}
