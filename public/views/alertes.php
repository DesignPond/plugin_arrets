<?php

	ob_start();
	
	include('header-alertes.php');
	
?>
		<!-- ------- main section ------- -->                	
    	<tr>
    		<td bgcolor="bccfdb">
    			<table border="0" width="560" align="center" cellpadding="0" cellspacing="0" class="container-middle">	                				
        			<tr bgcolor="ffffff"><td height="14"></td></tr>	                			
        			<tr bgcolor="ffffff">
        				<td align="center">
        					<a href="<?php echo $home; ?>">
        						<img style="display: block;" class="main-image" width="538" height="100" src="<?php echo $url; ?>img/main-img-alerte.png" alt="Arrêts du TF" />
        					</a>
						</td>
        			</tr>	                			
        			<tr bgcolor="ffffff"><td height="20"></td></tr>	                			
        			<tr bgcolor="ffffff">
        				<td>
        					<table width="528" border="0" align="center" cellpadding="0" cellspacing="0" class="mainContent">
        						<tr>	
        							<td class="main-header" style="text-align:center; color: #0f4060; font-size: 16px; font-weight: normal; font-family: Helvetica, Arial, sans-serif;">
	                					Semaine du				                					
	                				</td>
	                			</tr>		
        					</table>
        				</td>
        			</tr>	                			
        			<tr bgcolor="ffffff"><td height="20"></td></tr>		                				
    			</table>
    		</td>
    	</tr>
    	<!-- ------- end main section ------- -->                	                	
    	<tr><td height="35"></td></tr>					
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
					
					<?php  foreach($arrets as $arret){ ?>
					
    				<tr bgcolor="ffffff">
    					<td>
            				<table border="0" width="550" align="center" class="container-middle" cellpadding="0" cellspacing="0">
								<tr style="color: #0f4060; font-size: 11px; font-weight: normal; font-family: Helvetica, Arial, sans-serif;" bgcolor="ffffff">
                					<td width="70" align="left"><?php echo $arret['datep_nouveaute']; ?></td>
                					<td width="70" align="left"><?php echo $arret['dated_nouveaute']; ?></td>
                					<td width="130" align="left"><?php echo $arret['nameCat']; ?></td>
                					<td width="75" align="left">
                					   <a style="color:#0f4060;font-weight:bold;" target="_blank" href="<?php echo $home;?>/?page_id=1143&arret=<?php echo $arret['numero_nouveaute'];?>">
                						  <?php echo $arret['numero_nouveaute']; ?>
                					   </a>
                					</td>
                					<td width="90" align="left">Mots clés, mots clés encore</td>
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
    	               	
    	<tr><td height="35"></td></tr>                	
    </table>               
    <!------------ end main Content ----------------->
	    
<?php

include('footer-alertes.php');

$content = ob_get_clean();


