<?php

class Update{

	// Get classes		
	protected $grab;
	
	protected $database;
	
	protected $search;
		
	protected $log;
	
	// Set tables
	protected $nouveautes_table;

	protected $keywords_table;
	
	protected $extracategory_table;
	
	protected $updated_table;
	
	// Urls
	protected $urlRoot;
	
	protected $urlArret;
	
	function __construct( $test = null ) {
		
		// Get classes		
		$this->grab     = new Grab;
		
		$this->database = new Database($test);
		
		$this->search   = new Search($test);
		
		$this->log      = new Log;
		
		// Set tables
		$this->nouveautes_table    = ( $test ? 'wp_nouveautes_test' : 'wp_nouveautes' );
	
		$this->keywords_table      = 'wp_keyword_extra';
		
		$this->extracategory_table = ( $test ? 'wp_extracategories_test' : 'wp_extracategories' );
						
		$this->updated_table       = 'wp_updated';
		
		// Urls
		$root = 'http://relevancy.bger.ch';
		
		$url  = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';
		
		$this->urlArret = ( get_option('dd_newsletter_url_arret') ? get_option('dd_newsletter_url_arret') : $url ); 
		
		$this->urlRoot  = ( get_option('dd_newsletter_url') ? get_option('dd_newsletter_url') : $root ); 
						
	}
	
	/**
	 * MAIN FUNCTION
	*/
	public function initUpdate(){
	
		/*
			Return values			
			0 or false 
			1 or true
			2 : problem for date
			3 : nothing to update			
		*/
	
		// Get arrets and dates to update
		$arrets = $this->getArretsToUpdate();
		
		// LOGGING
		$this->log->write('Arrets to update yes!');
		// END LOGGIN
		
		if(!empty($arrets))
		{
			foreach($arrets as $date => $update)
			{			
				// Update text for arrets	
				if( $this->updateTextArret($update) )
				{					
					// Pass date to updated					
					if( !$this->dateIsUpdated($date) )
					{
						return 0;
					}										 				
				}
				else
				{
					// LOGGING
					$this->log->write('Problème with update of arret for date : '.$date);
				 	// END LOGGIN	
				 	
					return 2;									
				}
			}
			
			return 1;
		}
		else
		{
			// LOGGING
			$this->log->write('Nothing to update : '.$date);
			// END LOGGIN
			
			return 3;	
		}
			
	}
	
	// All arrets for date are updated
	public function dateIsUpdated($date){

		global $wpdb;
			
		$data = array( 'date' => $date );  
		
		if( $wpdb->insert( $this->updated_table , $data , array( '%s') ) === FALSE )
		{	
			return 0;
		}
				
		return 1;  

	}
	
	// Get list of dates with all arrtes to update
	public function getArretsToUpdate(){

		global $wpdb;
		
		$arrets = array();
				
		// Get last date
		$toUpdate = $wpdb->get_results('SELECT id_nouveaute,datep_nouveaute,dated_nouveaute,numero_nouveaute FROM '.$this->nouveautes_table.' WHERE updated = 0 ');	
		
		if( !empty($toUpdate) )
		{
		   foreach($toUpdate as $id)
		   {
		      $arrets[$id->datep_nouveaute][$id->id_nouveaute]['dated_nouveaute']  = $id->dated_nouveaute;
		      $arrets[$id->datep_nouveaute][$id->id_nouveaute]['numero_nouveaute'] = $id->numero_nouveaute;
		   }
		}
		
		return $arrets;				
	}	
	
	// Main function to update the arret with the text
	public function updateTextArret($listLinks){

	 	global $wpdb;
		
		if(!empty($listLinks))
		{	 	
			foreach($listLinks as $id => $link)
			{								 	
	 			$urlArret = '';
				
				// Format the url
				$urlArret = $this->formatArretUrl($link);				
				
				// Grab the text
				$text     = $this->grab->getArticle($urlArret , $this->urlRoot);
				
				// We have the text, update the arret
				if( !empty($text) )
				{		
					$data = array( 'texte_nouveaute' => $text , 'updated' => 1 ); 
						
					$wpdb->update( $this->nouveautes_table , $data , array( 'id_nouveaute' => $id), array( '%s' , '%d' ), array( '%d' ));
					
					// Test for extra categries
					$this->addExtraKeywords($id);
					
					// unset the current arret
					unset($listLinks[$id]);
				}				
			}
		}
		
		// if we have updated all texts we are ok 
		$result = ( empty($listLinks) ? true : false );
		
		return $result;	

	}
	
	// Add extra categorie to arret with certain keaywords
	public function addExtraKeywords($id){

	 	global $wpdb;
		
		$needles = $this->getExtraKeywords();
			
		if( $needles )
		{				
			foreach($needles as $needle_id => $needle)
			{
				foreach($needle as $word)
				{
					if( $this->search->searchDatabaseKeywords($word , $id) !== 0 )
					{
						$data = array( 
							'parent_extra'    => $needle_id,  
							'nouveaute_extra' => $id
						); 
								
						$wpdb->insert( $this->extracategory_table , $data , array( '%d' , '%d'));
						
						// break the foreaches 
						return true;							
					} 
				}
			} 			
		} 
		
		return true;				
	}
	
	// Get all subcategories 
	public function getExtraKeywords(){
	
		global $wpdb;
		
		$needles = array();
		
		$extraKeywords = $wpdb->get_results('SELECT * FROM '.$this->keywords_table.'');
		
		if($extraKeywords)
		{
			foreach($extraKeywords as $extra)
			{
				$needles[$extra->parent_keywords][] = $extra->extra_keywords ;
			}
		}
		
		return $needles;			
	}
	 
	public function formatArretUrl($arret){
				
		$date      = new DateTime($arret['dated_nouveaute']);
		$dated     = $date->format('d-m-Y');
		$numero    = str_replace("/","-",$arret['numero_nouveaute']);
		
		$urlArret  = $this->urlArret;				
		$urlArret .= $dated.'-'.$numero;
		
		return $urlArret;		
	}

}
