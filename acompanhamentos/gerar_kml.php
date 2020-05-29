<?php

include('php/functions.php');

$conexao1 = mysql_connect("localhost", "fabio", "torres@#") or die("MySQL: Não foi possível conectar-se ao servidor [".$_SG['servidor']."].");
mysql_select_db("processos_db", $conexao1) or die("MySQL: Não foi possível conectar-se ao banco de dados [".$_SG['banco']."].");

function acentos($valor){
	$Valor2 = str_replace("&Aacute;","Á",$valor);
	$Valor2 = str_replace("&aacute;","á",$Valor2);
	$Valor2 = str_replace("&Acirc;","Â",$Valor2);
	$Valor2 = str_replace("&acirc;","â",$Valor2);
	$Valor2 = str_replace("&Agrave;","À",$Valor2);
	$Valor2 = str_replace("&agrave;","à",$Valor2);
	$Valor2 = str_replace("&Aring;","Å",$Valor2);
	$Valor2 = str_replace("&aring;","å",$Valor2);
	$Valor2 = str_replace("&Atilde;","Ã",$Valor2);
	$Valor2 = str_replace("&atilde;","ã",$Valor2);
	$Valor2 = str_replace("&Auml;","Ä",$Valor2);
	$Valor2 = str_replace("&auml;","ä",$Valor2);
	$Valor2 = str_replace("&AElig;","Æ",$Valor2);
	$Valor2 = str_replace("&aelig;","æ",$Valor2);
	$Valor2 = str_replace("&Eacute;","É",$Valor2);
	$Valor2 = str_replace("&eacute;","é",$Valor2);
	$Valor2 = str_replace("&Ecirc;","Ê",$Valor2);
	$Valor2 = str_replace("&ecirc;","ê",$Valor2);
	$Valor2 = str_replace("&Egrave;","È",$Valor2);
	$Valor2 = str_replace("&egrave;","è",$Valor2);
	$Valor2 = str_replace("&Euml;","Ë",$Valor2);
	$Valor2 = str_replace("&euml;","ë",$Valor2);
	$Valor2 = str_replace("&ETH;","Ð",$Valor2);
	$Valor2 = str_replace("&eth;","ð",$Valor2);
	$Valor2 = str_replace("&Iacute;","Í",$Valor2);
	$Valor2 = str_replace("&iacute;","í",$Valor2);
	$Valor2 = str_replace("&Icirc;","Î",$Valor2);
	$Valor2 = str_replace("&icirc;","î",$Valor2);
	$Valor2 = str_replace("&Igrave;","Ì",$Valor2);
	$Valor2 = str_replace("&igrave;","ì",$Valor2);
	$Valor2 = str_replace("&Iuml;","Ï",$Valor2);
	$Valor2 = str_replace("&iuml;","ï",$Valor2);
	$Valor2 = str_replace("&Oacute;","Ó",$Valor2);
	$Valor2 = str_replace("&oacute;","ó",$Valor2);
	$Valor2 = str_replace("&Ocirc;","Ô",$Valor2);
	$Valor2 = str_replace("&ocirc;","ô",$Valor2);
	$Valor2 = str_replace("&Ograve;","Ò",$Valor2);
	$Valor2 = str_replace("&ograve;","ò",$Valor2);
	$Valor2 = str_replace("&Oslash;","Ø",$Valor2);
	$Valor2 = str_replace("&oslash;","ø",$Valor2);
	$Valor2 = str_replace("&Otilde;","Õ",$Valor2);
	$Valor2 = str_replace("&otilde;","õ",$Valor2);
	$Valor2 = str_replace("&Ouml;","Ö",$Valor2);
	$Valor2 = str_replace("&ouml;","ö",$Valor2);
	$Valor2 = str_replace("&Uacute;","Ú",$Valor2);
	$Valor2 = str_replace("&uacute;","ú",$Valor2);
	$Valor2 = str_replace("&Ucirc;","Û",$Valor2);
	$Valor2 = str_replace("&ucirc;","û",$Valor2);
	$Valor2 = str_replace("&Ugrave;","Ù",$Valor2);
	$Valor2 = str_replace("&ugrave;","ù",$Valor2);
	$Valor2 = str_replace("&Uuml;","Ü",$Valor2);
	$Valor2 = str_replace("&uuml;","ü",$Valor2);
	$Valor2 = str_replace("&Ccedil;","Ç",$Valor2);
	$Valor2 = str_replace("&ccedil;","ç",$Valor2);
	$Valor2 = str_replace("&Ntilde;","Ñ",$Valor2);
	$Valor2 = str_replace("&ntilde;","ñ",$Valor2);
	$Valor2 = str_replace("&lt;","<",$Valor2);
	$Valor2 = str_replace("&gt;",">",$Valor2);
	$Valor2 = str_replace("&amp;","&",$Valor2);
	$Valor2 = str_replace("&quot;","'",$Valor2);
	$Valor2 = str_replace("&reg;","®",$Valor2);
	$Valor2 = str_replace("&copy;","©",$Valor2);
	$Valor2 = str_replace("&Yacute;","Ý",$Valor2);
	$Valor2 = str_replace("&yacute;","ý",$Valor2);
	$Valor2 = str_replace("&THORN;","Þ",$Valor2);
	$Valor2 = str_replace("&thorn;","þ",$Valor2);
	$Valor2 = str_replace("&szlig;","ß",$Valor2);
	$Valor2 = str_replace("&ordf;","a",$Valor2);
	$Valor2 = str_replace("&nbsp;","",$Valor2);
	$Valor2 = str_replace("&ordm;","º",$Valor2);
	return $Valor2; 
}

$a=0;

$kml_m1='';
$kml_m2='';
$kml_m3='';
$kml_m4='';
$kml_m5='';
$kml_m8='';
$kml_m0='';

$m=0;
$cd=0;
$h1=0;
$h2=0;
$h3=0;
$h4=0;
$h5=0;
$h8=0;
$h0=0;

$qr = mysql_query(" SELECT c.id_case, c.p_adverso
					FROM lcm_case AS c
					WHERE c.`status` = 'open' 
					AND c.p_cliente REGEXP 'BANCO GMAC' 
					AND c.legal_reason REGEXP 'BUSCA E APREEN|REINTEGRA|DEP' 
					GROUP BY c.id_case ",$conexao1);
	
while($wr = mysql_fetch_array($qr)){
	//normal///
	//seleciona os casos que foram aponados como AGUARDANDO DISTRIBUIÇÃO
	$qDistr = mysql_query(" SELECT ff.id_case
							FROM lcm_followup AS ff
							WHERE ff.`type` IN ('followups11') 
							AND ff.id_case = '" . $wr['id_case'] . "' 
							GROUP BY ff.id_case  ",$conexao1);
	
	if(mysql_num_rows($qDistr)>0){
		
		//seleciona os casos que NÃO foram aponados como VEÍCULO APREENDIDO
		$qApree = mysql_query(" SELECT ff.id_case
								FROM lcm_followup AS ff
								WHERE ff.`type` IN ('followups45')
								AND ff.id_case = '" . $wr['id_case'] . "'
								GROUP BY ff.id_case' ",$conexao1);
								
		if(mysql_num_rows($qApree)==0){
			
			$qCord = mysql_query(" 	SELECT * from lcm_contact as c 
									join lcm_case_adverso_cliente as ac on ac.id_adverso=c.id_of_person 
									where ac.id_case = '" . $wr['id_case'] . "' limit 1 ",$conexao1);
			$wCord = mysql_fetch_array($qCord);
				$a++;		
				$htmlA .= "<tr style='height:30px; text-align:center; id='trs_$a' onclick='javascrip:$(\"#trs_$a\").attr(\"bgcolor\",\"yellow\");' >
							<td style='width:80px'>" . $wr['id_case']  . "</td>
							<td style='width:80px'>" . $wr['p_adverso']. "</td>
							<td style='width:80px'>" . $wCord['value'] . "</td>
							<td style='width:80px'>" . $wCord['extra'] . "</td>
						  </tr>";
						  
			//gerando o kml
			$m++;
			$Qajui = mysql_query("SELECT f.id_case FROM lcm_followup as f where f.`type` = 'followups11' and f.id_case = " . $wr['id_case'] . " limit 1");
			$Valor = mysql_num_rows($Qajui);
			//DISTRIBUIDOS 
			$Qdist = mysql_query("SELECT f.id_case FROM lcm_followup as f where f.`type` = 'followups24' and f.id_case = " . $wr['id_case'] . " limit 1");
			$Valor = (mysql_num_rows($Qdist)==1?2:$Valor);
			//LIMINARES DEFERIDAS
			$Qlimi = mysql_query("SELECT f.id_case FROM lcm_followup as f where f.`type` = 'followups31' and f.id_case = " . $wr['id_case'] . " limit 1");
			$Valor = (mysql_num_rows($Qlimi)==1?3:$Valor);
			//MANDADOS EXPEDIDOS
			$Qmand = mysql_query("SELECT f.id_case FROM lcm_followup as f where f.`type` = 'followups33' and f.id_case = " . $wr['id_case'] . " limit 1");
			$Valor = (mysql_num_rows($Qmand)==1?4:$Valor);
			//MANDADOS DEVOLVIDOS
			$Qdevo = mysql_query("SELECT f.id_case FROM lcm_followup as f where f.`type` = 'followups35' and f.id_case = " . $wr['id_case'] . " limit 1");
			$Valor = (mysql_num_rows($Qdevo)==1?5:$Valor);
			//DILIGENCIA RETORNO
			$Qreto = mysql_query("SELECT f.id_case FROM lcm_followup as f where f.`type` = 'followups65' and f.id_case = " . $wr['id_case'] . " limit 1");
			$Retor = mysql_num_rows($Qreto);
			if($wCord['type_contact']==23){
				$Valor="8";
			}else{
				$Valor=$Valor;
			}
			$address = htmlentities($wCord['value']);
			if($address!=''){			
				$lat_lon = explode(",",$wCord['extra']);			
				$lat = $lat_lon[0];			
				$lng = $lat_lon[1];			
				if($lat!='' && $lng!=''){
					
					$pt = trim($wr['id_case']);				
					$nm = htmlentities($wr['p_adverso']);
					
					switch ($Valor.$Retor) {					
						case 10:
							$kml_m1 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Ajuizar]]></description><styleUrl>#icon-503-CCCCCC</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';						
							$h1++;
							break;
						case 20:
							$kml_m2 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Distribuido]]></description><styleUrl>#icon-503-7C3592</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h2++;
							break;
						case 30:
							$kml_m3 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Liminar Deferida]]></description><styleUrl>#icon-503-3F5BA9</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h3++;
							break;
						case 40:
							$kml_m4 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Mandado Expedido]]></description><styleUrl>#icon-503-009D57</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h4++;
							break;
						case 50:
							$kml_m5 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Mandado Devolvido]]></description><styleUrl>#icon-503-DB4436</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h5++;
							break;
						case 80:
							$kml_m8 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Endereço Novo]]></description><styleUrl>#icon-503-9FC3FF</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h8++;
							break;
						case 00:
							$kml_m0 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Sem Evento]]></description><styleUrl>#icon-503-F8971B</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h0++;
							break;
						//casos já visitados:
						case 11:
							$kml_m1 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Ajuizar]]></description><styleUrl>#icon-503-BBBBBB</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';						
							$h1++;
							break;
						case 21:
							$kml_m2 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Distribuido]]></description><styleUrl>#icon-503-A247BC</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h2++;
							break;
						case 31:
							$kml_m3 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Liminar Deferida]]></description><styleUrl>#icon-503-506FBE</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h3++;
							break;
						case 41:
							$kml_m4 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Mandado Expedido]]></description><styleUrl>#icon-503-00C66C</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h4++;
							break;
						case 51:
							$kml_m5 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Mandado Devolvido]]></description><styleUrl>#icon-503-E05F54</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h5++;
							break;
						case 81:
							$kml_m8 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Endereço Novo]]></description><styleUrl>#icon-503-BFD8FF</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h8++;
							break;
						case 01:
							$kml_m0 .= '<Placemark><name>Veículo:'.acentos($wr['notes']).'</name><description><![CDATA['.$pt.'<br>Nome: '.acentos($nm).' <br> Endereço: ' . str_replace(",",", ",str_replace("+"," ",acentos($address))).' <br> Sem Evento]]></description><styleUrl>#icon-503-FAAD50</styleUrl><Point><coordinates>'.$lng.','.$lat.'</coordinates></Point></Placemark>';
							$h0++;
							break;
					}				
				} 
			}
			$n++;	
		}
	}
}

$kml  = "<?xml version='1.0' encoding='UTF-8' ?>";
$kml .= '<kml xmlns="http://www.opengis.net-kml-2.2"><Document><name>Busca</name><description><![CDATA[]]></description>';
$kml .= '<Folder><name>Ajuizar - '.		 $h1.'</name>'.$kml_m1.'</Folder>';
$kml .= '<Folder><name>Distribuidos - '. $h2.'</name>'.$kml_m2.'</Folder>';
$kml .= '<Folder><name>Liminares - '.	 $h3.'</name>'.$kml_m3.'</Folder>';
$kml .= '<Folder><name>Mandados - '.	 $h4.'</name>'.$kml_m4.'</Folder>';
$kml .= '<Folder><name>Devolvidos - '.	 $h5.'</name>'.$kml_m5.'</Folder>';
$kml .= '<Folder><name>Novo Endereço - '.$h8.'</name>'.$kml_m8.'</Folder>';
$kml .= '<Folder><name>Sem Evento - '.	 $h0.'</name>'.$kml_m0.'</Folder>';

//Ajuizar - kml_1
$kml .= "<Style id='icon-503-CCCCCC-normal'>
			<IconStyle>
				<color>ffCCCCCC</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador10.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-CCCCCC-highlight'>
			<IconStyle>
				<color>ffCCCCCC</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador10.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-CCCCCC'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-CCCCCC-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-CCCCCC-highlight</styleUrl>
			</Pair>
		</StyleMap>";	


//Distribuido - kml_2	
$kml .= "<Style id='icon-503-7C3592-normal'>
			<IconStyle>
				<color>ff92357C</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador20.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-7C3592-highlight'>
			<IconStyle>
				<color>ff92357C</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador20.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-7C3592'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-7C3592-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-7C3592-highlight</styleUrl>
			</Pair>
		</StyleMap>";	

//Liminar Deferida - kml_3	
$kml .= "<Style id='icon-503-3F5BA9-normal'>
			<IconStyle>
				<color>ffA95B3F</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador30.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-3F5BA9-highlight'>
			<IconStyle>
				<color>ffA95B3F</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador30.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-3F5BA9'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-3F5BA9-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-3F5BA9-highlight</styleUrl>
			</Pair>
		</StyleMap>";	

//Mandado Expedido - kml_4
$kml .= "<Style id='icon-503-009D57-normal'>
			<IconStyle>
				<color>ff579D00</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador40.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-009D57-highlight'>
			<IconStyle>
				<color>ff579D00</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador40.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-009D57'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-009D57-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-009D57-highlight</styleUrl>
			</Pair>
		</StyleMap>";

//Mandado Devolvido - kml_5
$kml .= "<Style id='icon-503-DB4436-normal'>
			<IconStyle>
				<color>ff3644DB</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador50.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-DB4436-highlight'>
			<IconStyle>
				<color>ff3644DB</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador50.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-DB4436'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-DB4436-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-DB4436-highlight</styleUrl>
			</Pair>
		</StyleMap>";		
		
//Endereço Novo - kml_8
$kml .= "<Style id='icon-503-9FC3FF-normal'>
			<IconStyle>
				<color>ffFFC39F</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador80.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-9FC3FF-highlight'>
			<IconStyle>
				<color>ffFFC39F</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador80.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-9FC3FF'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-9FC3FF-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-9FC3FF-highlight</styleUrl>
			</Pair>
		</StyleMap>";		
		
//Sem Evento - kml_0
$kml .= "<Style id='icon-503-F8971B-normal'>
			<IconStyle>
				<color>ff1B97F8</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador70.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-F8971B-highlight'>
			<IconStyle>
				<color>ff1B97F8</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador70.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-F8971B'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-F8971B-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-F8971B-highlight</styleUrl>
			</Pair>
		</StyleMap>";	

////////////////////Casos Visitados/////////////////////////
//Ajuizar - kml_1
$kml .= "<Style id='icon-503-BBBBBB-normal'>
			<IconStyle>
				<color>ffBBBBBB</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador11.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-BBBBBB-highlight'>
			<IconStyle>
				<color>ffBBBBBB</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador11.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-BBBBBB'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-BBBBBB-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-BBBBBB-highlight</styleUrl>
			</Pair>
		</StyleMap>";	
		
//Distribuido - kml_2	
$kml .= "<Style id='icon-503-A247BC-normal'>
			<IconStyle>
				<color>ffBC47A2</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador21.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-A247BC-highlight'>
			<IconStyle>
				<color>ffBC47A2</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador21.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-A247BC'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-A247BC-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-A247BC-highlight</styleUrl>
			</Pair>
		</StyleMap>";	

//Liminar Deferida - kml_3	
$kml .= "<Style id='icon-503-506FBE-normal'>
			<IconStyle>
				<color>ffBE6F50</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador31.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-506FBE-highlight'>
			<IconStyle>
				<color>ffBE6F50</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador31.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-506FBE'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-506FBE-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-506FBE-highlight</styleUrl>
			</Pair>
		</StyleMap>";	

//Mandado Expedido - kml_4
$kml .= "<Style id='icon-503-00C66C-normal'>
			<IconStyle>
				<color>ff6CC600</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador41.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-00C66C-highlight'>
			<IconStyle>
				<color>ff6CC600</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador41.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-00C66C'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-00C66C-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-00C66C-highlight</styleUrl>
			</Pair>
		</StyleMap>";

//Mandado Devolvido - kml_5
$kml .= "<Style id='icon-503-E05F54-normal'>
			<IconStyle>
				<color>ff545FE0</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador51.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-E05F54-highlight'>
			<IconStyle>
				<color>ff545FE0</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador51.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-E05F54'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-E05F54-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-E05F54-highlight</styleUrl>
			</Pair>
		</StyleMap>";		
		
//Endereço Novo - kml_8
$kml .= "<Style id='icon-503-BFD8FF-normal'>
			<IconStyle>
				<color>ffFFD8BF</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador81.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-BFD8FF-highlight'>
			<IconStyle>
				<color>ffFFD8BF</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador81.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-BFD8FF'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-BFD8FF-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-BFD8FF-highlight</styleUrl>
			</Pair>
		</StyleMap>";		
		
//Sem Evento - kml_0
$kml .= "<Style id='icon-503-FAAD50-normal'>
			<IconStyle>
				<color>ff50ADFA</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador71.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>0.0</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<Style id='icon-503-FAAD50-highlight'>
			<IconStyle>
				<color>ff50ADFA</color>
				<scale>1.1</scale>
				<Icon>
					<href>http://www.direito2010.com.br/gmapa/img/marcador71.png</href>
				</Icon>
				<hotSpot x='16' y='31' xunits='pixels' yunits='insetPixels'></hotSpot>
			</IconStyle>
			<LabelStyle>
				<scale>1.1</scale>
			</LabelStyle>
			<BalloonStyle>
				<text><![CDATA[<h3>$[name]</h3>]]></text>
			</BalloonStyle>
		</Style>";
$kml .= "<StyleMap id='icon-503-FAAD50'>
			<Pair>
				<key>normal</key>
				<styleUrl>#icon-503-FAAD50-normal</styleUrl>
			</Pair>
			<Pair>
				<key>highlight</key>
				<styleUrl>#icon-503-FAAD50-highlight</styleUrl>
			</Pair>
		</StyleMap>";			

$kml .= "</Document></kml>";

$fn = fopen("arr_no.txt", "w");
$escreve = fwrite($fn, $arr_no);
fclose($fn);

$kn = fopen("meu_mapa.kml", "w");
$kescreve = fwrite($kn, $kml);
fclose($fn);

$table .= "<table align='center' id='tbf1' class='tbf1' width='100%' border='1' cellspacing='1' cellpadding='1' bordercolor='#ccc' style='border-collapse:collapse; color:red; font-size:7pt; font-family:arial; width:990px'>";
$table .= "<tr>
				<th style='color:blue;' >Pasta</td>
				<th style='color:blue;' >Contrato</td>
				<th style='color:blue;' >Adverso</td>
				<th style='color:blue;' >Banco</td>
		  </tr>";
$table .= $htmlA;
$table .= "</table>";
	
echo $table;

?>