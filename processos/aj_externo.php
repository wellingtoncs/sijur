<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<?php
include('inc/inc.php');

$arquivo = $_POST['flag']. "_" . date('YmdHis');

	//FT Salvando o excel
	 include("inc/excelwriter.inc.php");
	 $excel=new ExcelWriter("arquivos/".$_POST['flag']."_" . date('YmdHis') . ".xls");
	 if($excel==false){
        echo $excel->error;
	 }
	
	$qe = explode('WHERE', $_POST['id_dados']);
	$q  = " " . $qe[0];
	$q .= " WHERE" . $qe[1];
	
	$q = lcm_query($q)or die(mysql_error());
	
	$html  = '<table align="center" border="1" cellpadding="0" cellspacing="0" bordercolor="#CFCFCF" >';
	$html .=	'<tr>';
	if($_POST['flag']=='exp_proc')
	{
		$html .=		'<td><b>'. htmlentities("Pasta") .'				  </b></td>';
		$html .=		'<td><b>'. htmlentities("Ref.Tipo") .'			  </b></td>';
		$html .=		'<td><b>'. htmlentities("Ref.Valor") .'			  </b></td>';
		$html .=		'<td><b>'. htmlentities("Cliente") . '			  </b></td>';
		$html .=		'<td><b>'. htmlentities("Adverso") . '			  </b></td>';
		$html .=		'<td><b>'. htmlentities("A&ccedil;&atilde;o") . ' </b></td>';
		$html .=		'<td><b>'. htmlentities("Data de Criação") . '	  </b></td>';
		$html .=		'<td><b>'. htmlentities("Número do processo") . ' </b></td>';
		$html .=		'<td><b>'. htmlentities("Vara") . '			  	  </b></td>';
		$html .=		'<td><b>'. htmlentities("Comarca") . '			  </b></td>';
		$html .=		'<td><b>'. htmlentities("UF") . '			  	  </b></td>';
		$html .=		'<td><b>'. htmlentities("Andamento") . '  		  </b></td>';
		$html .=		'<td><b>'. htmlentities("Descriçãoo") . '		  </b></td>';
		$html .=		'<td><b>'. htmlentities("Status") . '		 	  </b></td>';
		$html .=		'<td><b>'. htmlentities("DP") . '				  </b></td>';
		$html .=		'<td><b>'. htmlentities("Data do Andamento") . '  </b></td>';
		$html .=	'</tr>';
		
		while($result = lcm_fetch_assoc($q)){
			
			$legq = lcm_query("	SELECT k.name, kc.value FROM lcm_keyword_case as kc join lcm_keyword as k on k.id_keyword=kc.id_keyword where kc.id_case = '".$result['id_case']."' and kc.value != '' and kc.id_keyword in (52,54) limit 1  ");
			$legw = lcm_fetch_array($legq);
	
			$html .= "<tr>";
			$html .=	"<td align='center' >" . $result['id_case'] . "								</td>";
			$html .=	"<td align='center' >" . ($legw['name']?_T("kw__refnumbers_" . $legw['name'] . "_title"):'') . "	</td>";
			$html .=	"<td align='center' >" . $legw['value'] . "									</td>";
			$html .=	"<td align='center' >" . $result['name'] . "								</td>";
			$html .=	"<td align='center' >" . $result['p_adverso'] . "		    	 			</td>";
			$html .=	"<td align='center' >" . $result['legal_reason'] ."	    	 				</td>";
			$html .=	"<td align='center' >" . $result['date_creation'] . " 						</td>";
			$html .=	"<td align='center' >" . $result['processo'] . "	   						</td>";
			$html .=	"<td align='center' >" . $result['vara'] . "	   							</td>";
			$html .=	"<td align='center' >" . $result['comarca'] . " 		 					</td>";
			$html .=	"<td align='center' >" . $result['state'] . " 		 						</td>";
			$html .=	"<td align='center' >" . _T("kw_followups_" . $result['type'] . "_title") ."</td>";
			$html .=	"<td align='center' >" . $result['description'] . " 			 			</td>";
			$html .=	"<td align='center' >" . _T('case_status_option_' . $result['status']) . "	</td>";
			$html .=	"<td align='center' >" . $result['stopday'] . " 							</td>";
			$html .=	"<td align='center' >" . $result['date_start'] . " 							</td>";
			$html .= "</tr>";
		}
	}
	elseif($_POST['flag']=='exp_agenda')
	{
		$html .=		'<td><b>'. "Pasta" .'                       </b></td>';
#		$html .=		'<td><b>'. "Ref.Tipo." .'                   </b></td>';
#		$html .=		'<td><b>'. "Ref.Valor" .'                   </b></td>';
		$html .=		'<td><b>'. "Cliente" . '                    </b></td>';
		$html .=		'<td><b>'. "Adverso" . '                    </b></td>';
		$html .=		'<td><b>'. "A&ccedil;&atilde;o" . '			</b></td>';
		$html .=		'<td><b>'. "N&uacute;mero do processo" . ' 	</b></td>';
		$html .=		'<td><b>'. "vara" . '			  			</b></td>';
		$html .=		'<td><b>'. "Comarca" . '					</b></td>';
		$html .=		'<td><b>'. "UF" . '					  		</b></td>';
		$html .=		'<td><b>'. "Tipo" . '  		  				</b></td>';
		$html .=		'<td><b>'. "T&iacute;tulo" . '  			</b></td>';
		$html .=		'<td><b>'. "Descri&ccedil;&atilde;o" . '    </b></td>';
		$html .=		'<td><b>'. "Cumprida?" . '    				</b></td>';
		$html .=		'<td><b>'. "Data" . '		 	  			</b></td>';
		$html .=	'</tr>';
		while($result = lcm_fetch_assoc($q)){
	
			$legq = lcm_query("	SELECT k.name, kc.value FROM lcm_keyword_case as kc join lcm_keyword as k on k.id_keyword=kc.id_keyword where kc.id_case = '".$result['id_case']."' and kc.value != '' and kc.id_keyword in (52,54) limit 1  ");
			//$legq = lcm_query("SELECT kc.value FROM lcm_keyword_case as kc where kc.id_case = '".$result['id_case']."' and kc.value != '' and kc.id_keyword in (52,54) limit 1 ");
			$legw = lcm_fetch_array($legq);
	
			$html .= "<tr>";
			$html .=	"<td align='center' >" . $result['id_case'] . "								</td>";
			#$html .=	"<td align='center' >" . ($legw['name']?_T("kw__refnumbers_" . $legw['name'] . "_title"):'') . "	</td>";
			#$html .=	"<td align='center' >" . $legw['value'] . "									</td>";
			$html .=	"<td align='center' >" . $result['p_cliente'] . "	   			 			</td>";
			$html .=	"<td align='center' >" . $result['p_adverso'] . "		    	 			</td>";
			$html .=	"<td align='center' >" . $result['legal_reason'] ."	    	 				</td>";
			$html .=	"<td align='center' >" . $result['processo'] . "	   						</td>";
			$html .=	"<td align='center' >" . $result['vara'] . "	   	 						</td>";
			$html .=	"<td align='center' >" . $result['comarca'] . " 		 					</td>";
			$html .=	"<td align='center' >" . $result['state'] . " 		 						</td>";
			$html .=	"<td align='center' >" . _T("kw_appointments_" . $result['type'] . "") . "	</td>";
			$html .=	"<td align='center' >" . $result['title'] . " 			 					</td>";
			$html .=	"<td align='center' >" . $result['description'] . " 			 			</td>";
			$html .=	"<td align='center' >" . ($result['performed']=='Y'?'S':'N') . " 			</td>";
			$html .=	"<td align='center' >" . $result['start_time'] . " 							</td>";
			$html .= "</tr>";
		}
	}
		$myArr=array($html);
		$excel->writeLine($myArr);
					 
$html .= '</table>';
$excel->close();

header("Content-Type: application/vnd.ms-excel; charset='ISO-8859-1'");
header("Content-Disposition: filename=$arquivo.xls");
header("Pragma: no-cache");
header("Expires: 0");
echo $html;

?>