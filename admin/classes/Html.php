<?php

class Html{

	// DB tables
	protected $nouveautes_table;
	
	protected $categories_table;
	
	protected $subcategories_table;
	
	// classes
	protected $utils;
		
	protected $log;
		
	// urls	
	protected $urlArret;
	
	protected $urlRoot;

	function __construct( $test = null ) {
		
		// Get classes		
		
		// Set tables		
		$this->nouveautes_table    = ( $test ? 'wp_nouveautes_test' : 'wp_nouveautes' );

		$this->categories_table    = ( $test ? 'wp_custom_categories_test' : 'wp_custom_categories' );

		$this->subcategories_table = ( $test ? 'wp_subcategories_test' : 'wp_subcategories' );
		
		// Set classes		
		$this->utils = new Utils;
		
		$this->log   = new Log;
		
		// urls
		$root = 'http://relevancy.bger.ch';
		
		$url  = 'http://relevancy.bger.ch/php/aza/http/index.php?lang=fr&zoom=&type=show_document&highlight_docid=aza%3A%2F%2F';
		
		$this->urlArret = ( get_option('dd_newsletter_url_arret') ? get_option('dd_newsletter_url_arret') : $url ); 
		
		$this->urlRoot  = ( get_option('dd_newsletter_url') ? get_option('dd_newsletter_url') : $root ); 
		
	}	
	
	/**
	 * Main function to prepare html emails
	*/
	public function setEmailHtml($user, $list){
		
		 global $wpdb;
		 
		 $html = ''; 
		 	
		 $urlRoot   = home_url('/');
		 $pageRoot  = 1143;
		 
		 $first_name = get_user_meta($user,'first_name',true);
		 $last_name  = get_user_meta($user,'last_name',true);

		 // Wrapper 
		 $html .= '<table align="center" style="border:1px solid #dddddd;background:#ffffff;font-family:arial,sans serif; padding:5px; margin:0; width:720px; display:block;">';
		 $html .= '<tr>'; 
		 $html .= '<td>';
		 
		 $html .= '<table width="100%" style="border:none; text-align:left; background:#b2c9d7; font-family:arial,sans serif;height:75px;">';
		 $html .= '<tr valign="middle"><td style="height:50px; display:block;">'; 
		 $html .= '<h1 style="display:block; padding:0 5px; color:#fff; font-size:25px;"><span style="color:#0f4060;">Droit</span> pour le Praticien</h1>';  
		 $html .= '</td></tr>'; 
		 $html .= '</table>'; 
		 
		 $html .= '<p style="color:#000; font-size:15px; margin-bottom:20px;font-family:arial,sans serif; line-height:20px; ">Bonjour';
		 $html .= '<strong> '.$first_name. ' ' .$last_name.'</strong>';
		 $html .= ',<br/>Voici les derniers arr&ecirc;ts correspondant &agrave; vos abonnements</p>';
		 
		 // Debut du mail
		 $html .= '<table style="border:none; text-align:left; font-family:arial,sans serif; " width="100%">';
		 $html .= '<tr style="background:#0f4060; text-align:left; color:#ffffff; font-weight:bold;">
		 		   <th width="75" style="padding:5px;font-size:12px; color:#ffffff;">Date de publication</th>
				   <th width="75" style="padding:5px;font-size:12px; color:#ffffff;">Date de d&eacute;cision</th>
				   <th width="150" style="padding:5px;font-size:12px; color:#ffffff;">Cat&eacute;gorie</th>
				   <th width="185" style="padding:5px;font-size:12px;word-wrap: keep-all ; color:#ffffff;">Sous-cat&eacute;gorie</th>
				   <th width="60" style="padding:5px; color:#ffffff;font-size:12px;">R&eacute;f&eacute;rence</th>
				   <th width="175" style="padding:5px; color:#ffffff;font-size:12px;">Mots cl&eacute;s</th>
				   </tr>';
				   
		 // Loop through array of ids
		 $nouveautes = '';
		 
		 foreach($list as $ids => $words)
		 {
			 $infosNouveaute = $this->getArret($ids);
														 
			 $nouveautes .= '<tr style="background:#f5f5f5; border:1px solid 3ebebeb; text-align:left;">';	
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">'.$infosNouveaute->datep_nouveaute.'</td>';										  
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">'.$infosNouveaute->dated_nouveaute.'</td>';
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">'.$infosNouveaute->nameCat.'</td>';
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;word-break:break-word;">'.$this->utils->limit_words($infosNouveaute->nameSub,8).'</td>';
			 $nouveautes .= '<td style="padding:5px;font-size:13px; color:#343434;text-align:left;">';
			 $nouveautes .= '<a style="color:#343434;font-size:13px;" href="'.$urlRoot.'?page_id='.$pageRoot.'&arret='.$infosNouveaute->numero_nouveaute.'"><strong>';
			 $nouveautes .= $infosNouveaute->numero_nouveaute;
			 $nouveautes .= '</strong></a></td>';
			 $nouveautes .= '<td style="padding:5px;font-size:12px; word-break:break-all;color:#343434; text-align:left;">'.$words.'</td>';
			 $nouveautes .= '</tr>';											 
		 }
		 
		 $html .= $nouveautes;
		 $html .= '</table>'; 
		 
		 // end wrapper
		 $html .= '</td>'; 
		 $html .= '</tr></table>'; 
		 
		 return $html;
		 
	}
	
	public function setAlerteHtml($user, $list){
		
		 global $wpdb;
		 	
		 $urlRoot   = home_url('/');
		 $pageRoot  = 1143;
		 	 
		 $first_name = get_user_meta($user,'first_name',true);
		 $last_name  = get_user_meta($user,'last_name',true);
		 
		 // Start buffer!!!
		 ob_start();
		 
		 // include header
		 include( plugin_dir_path( dirname(dirname( __FILE__ ) ) ).'/public/views/header-alertes.php');
		 
		 include( plugin_dir_path( dirname(dirname( __FILE__ ) ) ).'/public/views/footer-alertes.php');
		 
		 $content = ob_get_clean();
		 
		 return $content;

	}
	
	// Get infos for id arret
	public function getArret($id){
		
		global $wpdb;
		
		$infos = $wpdb->get_row( 'SELECT '.$this->nouveautes_table.'.id_nouveaute , 
										 '.$this->nouveautes_table.'.datep_nouveaute , 
										 '.$this->nouveautes_table.'.dated_nouveaute , 
										 '.$this->nouveautes_table.'.categorie_nouveaute , 
										 '.$this->nouveautes_table.'.link_nouveaute , 
										 '.$this->nouveautes_table.'.numero_nouveaute , 
										 '.$this->nouveautes_table.'.publication_nouveaute , 
										 '.$this->categories_table.'.name as nameCat , 
										 '.$this->subcategories_table.'.name as nameSub 
								  FROM '.$this->nouveautes_table.' 
								  JOIN '.$this->categories_table.'  on '.$this->categories_table.'.term_id  = '.$this->nouveautes_table.'.categorie_nouveaute 
								  LEFT JOIN '.$this->subcategories_table.' on '.$this->subcategories_table.'.refNouveaute = '.$this->nouveautes_table.'.id_nouveaute
								  WHERE id_nouveaute = "'.$id.'" ');
								  
		return $infos;

	}

	
}