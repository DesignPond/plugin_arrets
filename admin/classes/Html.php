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
	public function setAlerteHtml($user, $list){
		
		 global $wpdb;
		 	
		 $home       = home_url();
		 $url        = plugins_url().'/dd_arrets/assets/';
		 $pageRoot   = 1143;
		 	 
		 $first_name = get_user_meta($user,'first_name',true);
		 $last_name  = get_user_meta($user,'last_name',true);
		 
		 // Start buffer!!!
		 ob_start();
		 
		 // include header
		 include( plugin_dir_path( dirname(dirname( __FILE__ ) ) ).'/public/views/header-alertes.php');
		 
		 /* ============================= LOOP  ================================*/
 
		 ?>

		 <!-- ------- main section ------- -->                	
		 <tr>
			<td bgcolor="bccfdb">
				<table border="0" width="560" align="center" cellpadding="0" cellspacing="0" class="container-middle">	                				
					<tr bgcolor="ffffff"><td height="15"></td></tr>	                			
					<tr bgcolor="ffffff">
						<td align="center">
							<a href="<?php echo $home; ?>">
								<img style="display: block;" class="main-image" width="538" height="100" src="<?php echo $url; ?>img/main-img-alertes.png" alt="Arrêts du TF" />
							</a>
						</td>
					</tr>	
					<tr bgcolor="ffffff">
						<td>
							<table border="0" width="540" align="center" class="container-middle" cellpadding="0" cellspacing="0">
								<tr bgcolor="ffffff"><td height="14"></td></tr>	 
								<tr style="color:#43637c;font-size:13px;font-weight:bold importat!;font-family:Helvetica,Arial,sans-serif;">
									<td align="left">
										Bonjour <?php echo $first_name; ?> <?php echo $last_name; ?>,
										Voici les derniers arrêts correspondant à vos abonnements 
									</td> 
								</tr>	
							</table>
						</td>
					</tr>	              				                			
					<tr bgcolor="ffffff"><td height="15"></td></tr>		                				
				</table>
			</td>
		 </tr>
		 
		 <!-- ------- end main section ------- -->                	                	
		 <tr><td height="25"></td></tr>					
		 <!-- ------- section  ------- -->
		 
		 <tr>
			<td>
				<table border="0" width="560" align="center" cellpadding="0" cellspacing="0" class="container-middle">               			
					<tr bgcolor="43637c"><td colspan="10" height="5"></td></tr>	
					<tr bgcolor="43637c">
						<td>
		    				<table border="0" width="550" align="center" class="container-middle" cellpadding="0" cellspacing="0">
								<tr bgcolor="43637c" style="color:#fff;font-size:12px;font-weight:normal importat!;font-family:Helvetica,Arial,sans-serif;">
									<th width="70" align="left" style="font-weight:normal;">Date de<br/> publication</th>
									<th width="70" align="left" style="font-weight:normal;">Date de<br/> décision</th>
									<th width="130" align="left" style="font-weight:normal;">Catégorie</th>
									<th width="75" align="left" style="font-weight:normal;">Référence</th>
									<th width="90" align="left" style="font-weight:normal;">Mots clés</th>
								</tr>
							</table>
						</td>
					</tr>
					<tr bgcolor="43637c"><td height="5"></td></tr>									
					<tr bgcolor="ffffff"><td height="10"></td></tr>	
					
					<?php  
					
						foreach($list as $id => $words)
						{ 					
							$one = $this->getArret($id);
						
					?>
					
						<tr bgcolor="ffffff">
							<td>
			    				<table border="0" width="550" align="center" class="container-middle" cellpadding="0" cellspacing="0">
									<tr style="color: #0f4060; font-size: 11px; font-weight: normal; font-family: Helvetica, Arial, sans-serif;" bgcolor="ffffff">
			        					<td width="70" align="left"><?php  echo $one->datep_nouveaute; ?></td>
			        					<td width="70" align="left"><?php  echo $one->dated_nouveaute; ?></td>
			        					<td width="130" align="left"><?php echo $one->nameCat; ?></td>
			        					<td width="75" align="left">
			        					   <a style="color:#0f4060;font-weight:bold;" target="_blank" href="<?php echo $home;?>/?page_id=<?php echo $pageRoot; ?>&arret=<?php echo $one->numero_nouveaute;?>">
			        						  <?php echo $one->numero_nouveaute; ?>
			        					   </a>
			        					</td>
			        					<td width="90" align="left"><?php if(!empty($words)){ echo $words; } ?></td>
									</tr>
								</table>
							</td>	
						</tr>
						<tr bgcolor="ffffff"><td colspan="6" height="10"></td></tr>	
					
					<?php } ?>	
					
    			</table>
    		</td>
    	 </tr>
    	 <!--------- end section --------->
    				
		 <?php 
				 
		 /* ============================= END LOOP  ============================*/
		 
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