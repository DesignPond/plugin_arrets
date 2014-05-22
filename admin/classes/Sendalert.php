<?php

class Sendalert{
	
	// classes
	
	protected $dates;
	
	protected $grab;
	
	protected $nouveautes;
	
	protected $user;
	
	protected $html;

	protected $sendalert;
	
	protected $log;
	
	private $username; 
	
	private $apikey; 


	function __construct() {
	
		$mode = get_option('dd_arrets_mode'); 
		
		$this->dates      = new Dates($mode);
		
		$this->grab       = new Grab();

		$this->nouveautes = new Nouveautes($mode);	

		$this->user       = new User($mode);

		$this->html       = new Html($mode);	
						
		$this->log        = new Log();
	
		$this->username = 'cindy.leschaud@gmail.com';
		
	    $this->apikey   = '15663da1-b7ed-4ba5-b305-c807d1d49693';
	}
	
	/*
	 * Prepapre alert for users
	*/
	public function prepareAlert($date,$currentday){

		// Empty arrays to be sur for the merge	
		$week_list  = array();
		$day_list   = array();
		$everything = array();
		
		// Get arrets for day
		$day_arrets = $this->nouveautes->getArretsAndCategoriesForDates($date);
		
		// Get users for day
		$day_users  = $this->user->getUserAbos('all');
		
		if(!empty($day_arrets['arrets']))
		{
			// Assign arrets
			$day_list = $this->nouveautes->assignArretsUsers($day_users, $day_arrets['arrets']);		
			// Clean users with id
			$day_list = $this->nouveautes->cleanEachUser($day_list); 
							
		}
				
		// If it's friday friday! we need all week date range to
		if($currentday == 5)
		{
			// Get 5 last week days
			$week_days   = $this->nouveautes->getWeekDays();		
			// Get arrets for week
			$week_arrets = $this->nouveautes->getArretsAndCategoriesForDates($week_days);			
			// Get users for week
			$week_users  = $this->user->getUserAbos('one');
				
			if(!empty($week_arrets['arrets']))
			{					
				// Assign arrets
				$week_list   = $this->nouveautes->assignArretsUsers($week_users, $week_arrets['arrets']);
				// Clean users with id
				$week_list   = $this->nouveautes->cleanEachUser($week_list); 
			}
		}
		
		// merge all users 		
		$everything = $day_list + $week_list;

		return $everything;
	
	}
	
	/*
	 * Prepapre send data and send!
	*/
	public function prepareSend($user , $body_html){
					 
		$user_info  = get_userdata($user);
		$email      = $user_info->user_email;
		 
		// Params
		$fromName  = 'Droit pour le Praticien';
		$from      = 'info@droitpourlepraticien.ch';
		$to        =  $email;
		$subject   = 'Alertes | Droit pour le Praticien';
		$body_text =  NULL;
		
		$result = $this->sendElasticEmail($to, $subject, $body_text, $body_html, $from, $fromName);
		
		return $result;
		
	}
	
	/*
	 * Main function send alerte
	*/
	function sendElasticEmail($to, $subject, $body_text, $body_html, $from, $fromName)
	{
	    $res = "";
	
	    $data  = "username=".urlencode($this->username);
	    $data .= "&api_key=".urlencode($this->apikey);
	    $data .= "&from=".urlencode($from);
	    $data .= "&from_name=".urlencode($fromName);
	    $data .= "&to=".urlencode($to);
	    $data .= "&subject=".urlencode($subject);
	    
	    if($body_html)
	    {  
	    	$data .= "&body_html=".urlencode($body_html); 	    
	    }
	    if($body_text)
	    {
	    	$data .= "&body_text=".urlencode($body_text);
	    }
	
	    $header  = "POST /mailer/send HTTP/1.0\r\n";
	    $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
	    $header .= "Content-Length: " . strlen($data) . "\r\n\r\n";
	    
	    $fp = fsockopen('ssl://api.elasticemail.com', 443, $errno, $errstr, 30);
	
	    if(!$fp)
	    {
	        return "ERROR. Could not open connection";
	    }
	    else 
	    {
	        fputs ($fp, $header.$data);
	        
	        while (!feof($fp)) 
	        {
		        $res .= fread ($fp, 1024);
	        }
	        
	        fclose($fp);
	    }
	    
	    return $res;
                      
	}
		
	public function testIdSend($string)
	{
		$string  = trim($string);
		$string  = str_replace("\r", " ", $string);
		$string  = str_replace("\n", " ", $string);

		$explode = explode(' ', $string);		
		$explode = array_filter($explode);			
		$end     = end($explode);
		
		$end     = preg_replace('/ {2,}/',' ',$end);
		$end     = trim($end);
					
		$matches = null;
		
		$returnValue = preg_match('/^[a-z0-9-]{3,40}/', $end , $matches);
		
		if($matches) { return $end; }
		
		return false;
		
	}
	
		
}