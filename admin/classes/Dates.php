<?php 

class Dates {
	
	// DB tables
	protected $nouveautes_table;
	
	// urls		
	protected $urlArret;

	function __construct( $test = null) {
		
		$this->nouveautes_table = ( $test ? 'wp_nouveautes_test' : 'wp_nouveautes' );
		
		$url  = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';
		
		$this->urlArret = ( get_option('dd_newsletter_url_arret') ? get_option('dd_newsletter_url_arret') : $url ); 

	}
	 	 	
	/* ===============================================
		Dates functions
	 =============================================== */	
	
	// Get last date from db
	public function lastDayInDb(){
	
		global $wpdb;
				
		// Get last date
		$lastDate = $wpdb->get_row('SELECT datep_nouveaute FROM '.$this->nouveautes_table.' ORDER BY datep_nouveaute DESC LIMIT 0,1 ');	
		
		$date = ( !empty($lastDate) ? $lastDate->datep_nouveaute : '');
		
		return $date;
	}
	
	public function lastDateToInsert($list){
		
		// Get first date of list from TF, has to be the last update if the time is right , I still don't know when exactly they are making updates :(
		$date = array_shift($list);
		
		$last = $this->lastDayInDb();
		
		if(!empty($last))
		{
			$last = strtotime($last);
			$last = date("ymd", $last);				
			
			if( $this->isToday($date) && ($date > $last) )
			{
				$d = DateTime::createFromFormat('ymd', $date);
				
				return $d->format('Y-m-d');
			}				
		}
		
		return false;
		
	}
	
	// 
	public function isToday($date){
		
	    $yesterday = date("Y-m-d", strtotime("-1 day"));
		$yesterday = strtotime($yesterday);
		$yesterday = date("ymd", $yesterday);
		
		$date      = strtotime($date);
		$date      = date("ymd", $date);	
		
		$isToday = ( $date > $yesterday ? true : false);
		
		return $isToday;
	}
	
	public function datesToUpdate($dates){
	
		$toUpdate = array();
		
		$last = $this->lastDayInDb();
		
		if(!empty($last))
		{
			$last = strtotime($last);
			$last = date("ymd", $last);	
			
			if(!empty($dates))
			{
				foreach($dates as $date)
				{			
					if( $this->isToday($date) && ($date > $last) )
					{
						$toUpdate[] = $date;
					}				
				}
			}
		}

		return $toUpdate;		
	}
	
	public function validateDate($date)
	{
	    $d = DateTime::createFromFormat('Y-m-d', $date);
	    return $d && $d->format('Y-m-d') == $date;
	}
				
}