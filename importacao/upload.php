<?php 

date_default_timezone_set('America/Recife');
ini_set('memory_limit', '1024M');
/* Insira aqui a pasta que deseja salvar o arquivo*/

function dividir_nomes($str){
	$nome = explode(" ",$str);
	$primeiro_nome = $nome[0];
	unset($nome[0]);
	$resto = implode(" ", $nome);
	return array('primeiro_nome'=> $primeiro_nome, 'resto_nome' => $resto);
}

function dateFormatEn($date){
	$date = explode('/', $date);
	$date = $date[2].'-'.$date[1].'-'.$date[0];
	return $date;
}

function remover_caracter($string){
	return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $string ) );
}

require 'Excel/reader.php';

$data = new Spreadsheet_Excel_Reader();
$data->setOutputEncoding('UTF-8');

$uploaddir = '/var/www/html/importacao/Arquivos/';

$uploadfile = $uploaddir . $_FILES['upfile']['name'];

if (move_uploaded_file($_FILES['upfile']['tmp_name'], $uploadfile) && $_FILES['upfile']['type'] == "application/vnd.ms-excel"){

	rename($uploadfile, $uploaddir."importacao_".date('YmdHis').".xls");
	$data->read($uploaddir."importacao_".date('YmdHis').".xls");

} else {
	echo $_FILES['upfile']['type'] . "<br>";
	echo "Arquivo não corresponde!<br> <i>Lembre-se que o tipo do arquivo deve ser 'xls'.";
	exit;
}

//exit;

$conexao = mysql_connect('localhost','fabio','torres@#');
$banco = mysql_select_db('contratos_db');

$titulo 	= array();
$tit 		= array();
$cont_adver = array();

for($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
	
	for ($j = 1; $j <= $data->sheets[0]['numCols']; $j++) {
		
		$celldata = utf8_encode((!empty($data->sheets[0]['cells'][$i][$j])) ? $data->sheets[0]['cells'][$i][$j] : "");
		
		if($i==1){
			if($j==1   && remover_caracter($celldata)!="Empresa")				{ echo "<br>A coluna 'A' da planilha deve conter 'Empresa'. Ela cont&eacute;m no entanto: " . $celldata; 		 		exit;}
			if($j==2   && remover_caracter($celldata)!="Regional")				{ echo "<br>A coluna 'B' da planilha deve conter 'Regional'. Ela cont&eacute;m no entanto: " . $celldata; 		 		exit;}
			if($j==3   && remover_caracter($celldata)!="PAM")					{ echo "<br>A coluna 'C' da planilha deve conter 'PAM'. Ela cont&eacute;m no entanto: " . $celldata; 			 		exit;}
			if($j==4   && remover_caracter($celldata)!="Nome Cliente")			{ echo "<br>A coluna 'D' da planilha deve conter 'Nome Cliente'. Ela cont&eacute;m no entanto: " . $celldata; 	 		exit;}
			if($j==5   && remover_caracter($celldata)!="Valor")					{ echo "<br>A coluna 'E' da planilha deve conter 'Valor'. Ela cont&eacute;m no entanto: " . $celldata; 		 		exit;}
			if($j==6   && remover_caracter($celldata)!="CPFCNPJ")				{ echo "<br>A coluna 'F' da planilha deve conter 'CPFCNPJ'. Ela cont&eacute;m no entanto: " . $celldata; 		 		exit;}
			if($j==7   && remover_caracter($celldata)!="Parcela")				{ echo "<br>A coluna 'G' da planilha deve conter 'Parcela'. Ela cont&eacute;m no entanto: " . $celldata; 		 		exit;}
			if($j==8   && remover_caracter($celldata)!="Contrato")				{ echo "<br>A coluna 'H' da planilha deve conter 'Contrato'. Ela cont&eacute;m no entanto: " . $celldata; 				exit;}
			if($j==9   && remover_caracter($celldata)!="Conta")					{ echo "<br>A coluna 'I' da planilha deve conter 'Conta'. Ela cont&eacute;m no entanto: " . $celldata; 				exit;}
			if($j==10  && remover_caracter($celldata)!="Taxa de Juros")			{ echo "<br>A coluna 'J' da planilha deve conter 'Taxa de Juros'. Ela cont&eacute;m no entanto: " . $celldata; 		exit;}
			if($j==11  && remover_caracter($celldata)!="Parcelas Contratadas")	{ echo "<br>A coluna 'K' da planilha deve conter 'Parcelas Contratadas'. Ela cont&eacute;m no entanto: " . $celldata; 	exit;}
			if($j==12  && remover_caracter($celldata)!="Vencimento Parcela")	{ echo "<br>A coluna 'L' da planilha deve conter 'Vencimento Parcela'. Ela cont&eacute;m no entanto: " . $celldata;	exit;}
			if($j==13  && remover_caracter($celldata)!="Dias de Atraso")		{ echo "<br>A coluna 'M' da planilha deve conter 'Dias de Atraso'. Ela cont&eacute;m no entanto: " . $celldata; 		exit;}
			if($j==14  && remover_caracter($celldata)!="Agente")				{ echo "<br>A coluna 'N' da planilha deve conter 'Agente'. Ela cont&eacute;m no entanto: " . $celldata; 				exit;}
			if($j==15  && remover_caracter($celldata)!="Endereco")				{ echo "<br>A coluna 'O' da planilha deve conter 'Endere&ccedil;o'. Ela cont&eacute;m no entanto: " . $celldata; 				exit;}
			if($j==16  && remover_caracter($celldata)!="Numero")				{ echo "<br>A coluna 'P' da planilha deve conter 'Número'. Ela cont&eacute;m no entanto: " . $celldata; 				exit;}
			if($j==17  && remover_caracter($celldata)!="Complemento")			{ echo "<br>A coluna 'Q' da planilha deve conter 'Complemento'. Ela cont&eacute;m no entanto: " . $celldata; 			exit;}
			if($j==18  && remover_caracter($celldata)!="Bairro")				{ echo "<br>A coluna 'R' da planilha deve conter 'Bairro'. Ela cont&eacute;m no entanto: " . $celldata; 				exit;}
			if($j==19  && remover_caracter($celldata)!="CEP")					{ echo "<br>A coluna 'S' da planilha deve conter 'CEP'. Ela cont&eacute;m no entanto: " . $celldata; 					exit;}
			if($j==20  && remover_caracter($celldata)!="Tel")					{ echo "<br>A coluna 'T' da planilha deve conter 'Tel'. Ela cont&eacute;m no entanto: " . $celldata; 					exit;}
			if($j==21  && remover_caracter($celldata)!="Cidade")				{ echo "<br>A coluna 'U' da planilha deve conter 'Cidade'. Ela cont&eacute;m no entanto: " . $celldata;				exit;}
			if($j==22  && remover_caracter($celldata)!="UF")					{ echo "<br>A coluna 'V' da planilha deve conter 'UF'. Ela cont&eacute;m no entanto: " . $celldata; 					exit;}
			
			$tit[$j] = $celldata;
		}else{
			if($j==6){
				$campo = str_replace(".","",str_replace("-","",trim($celldata)));
				$titulo[$tit[$j]][] = $campo;
			} else {
				$campo = trim($celldata);
				$titulo[$tit[$j]][] = $campo;
			}
			
			if($j==8){
				$cont_adver[$celldata][str_replace(".","",str_replace("-","",trim($data->sheets[0]['cells'][$i][6])))] = 0;
				
				${$celldata}[trim($data->sheets[0]['cells'][$i][7])] = array(dateFormatEn($data->sheets[0]['cells'][$i][12]),trim($data->sheets[0]['cells'][$i][11]),trim($data->sheets[0]['cells'][$i][5]));
				asort(${$celldata});
				$cont_parce[$celldata] = ${$celldata};
			}
		}
	}
}

//reset($cont_parce['805']);
//echo "<pre>";
//print_r($cont_adver['2359']);
//echo "</pre>";
//exit;

$insct_advr = array();
$insid_advr = array();
///////////inserindo os adversos ////////////////////////////////////////////////////////
$id_adv = mysql_query("SELECT max(id_adverso) as 'id_adverso' FROM lcm_adverso ");
$nm_adv = mysql_fetch_array($id_adv);
$n_id = $nm_adv['id_adverso'] +1;
foreach(array_unique($titulo['CPFCNPJ']) as $num => $cnpfcnpj){
	$sql = mysql_query("SELECT * FROM lcm_adverso as a where a.cpfcnpj  = '$cnpfcnpj' ");
	$nomes = dividir_nomes($titulo['Nome Cliente'][$num]);
	if(mysql_num_rows($sql)==0){
		$n_id++;
		echo "insert into lcm_adverso set 
		id_adverso='".$n_id."', 
		name_first='".$nomes['primeiro_nome']."', 
		name_middle='', 
		name_last='".$nomes['resto_nome']."', 
		date_creation='".date("Y-m-d H:i:s")."',	
		date_update='".date("Y-m-d H:i:s")."',	
		date_birth='0000-00-00 00:00:00', 
		citizen_number='', 
		cpfcnpj='$cnpfcnpj', 
		gender='unknown', 
		civil_status='unknown', 
		income='unknown', 
		notes=''; <br>";
		
		//inserindo os contatos
		echo "insert into lcm_contact set 
		type_person = 'adverso',
		id_of_person = '".$n_id."',
		value = '".($titulo['Tel'][$num])."',
		type_contact = 4,
		date_update='".date('Y-m-d H:i:s')."';<br>";
		
		$insct_advr[$titulo['Contrato'][$num]][]=$n_id;
		
		$insid_advr[$cnpfcnpj]=$n_id;
	}else{
		$wins = mysql_fetch_array($sql);
		$insid_advr[$cnpfcnpj]=$wins['id_adverso'];
	}
}

//echo "<pre>";
//print_r($insct_advr);
//echo "</pre>";
//exit;

/// max id cont //
$id_ctr = mysql_query("SELECT max(id_case) as 'id_case' FROM lcm_cont ");
$nm_ctr = mysql_fetch_array($id_ctr);
$n_c = $nm_ctr['id_case'] +1;

///max id stage //
$id_stg = mysql_query("SELECT max(id_entry) as 'id_entry' FROM lcm_cont_stage ");
$nm_stg = mysql_fetch_array($id_stg);
$n_s = $nm_stg['id_entry'] +1;

///max id followup //
$id_fol = mysql_query("SELECT max(id_followup) as 'id_followup' FROM lcm_cont_followup ");
$nm_fol = mysql_fetch_array($id_fol);
$n_f = $nm_fol['id_followup'] +1;

$dataatual = date('YmdHis');
$file_inc   = 'logs_csv/contratos_entrada_'.$dataatual.'.csv';  
$dados_inc  = '';  
$dados_inc .="Contratos;Devedores;Cliente;Data da Criação;Conta;Status";
$dados_inc .="\n"; 

//Girando todos os contratos da planilha
foreach(array_unique($titulo['Contrato']) as $num => $contrato){

	//inserindo contrato quando este não existir//
	$sql_I = mysql_query("SELECT * FROM lcm_cont as c where c.contrato = '$contrato' and c.p_cliente='FINSOL SOCIEDADE DE CREDITO AO MICROEMPREENDEDOR E A EMPRESA DE PEQUENO PORTE S/A' ");
	if(mysql_num_rows($sql_I)==0){
		$dados_inc .= $contrato.";".$titulo['Nome Cliente'][$num].";FINSOL SOCIEDADE DE CREDITO AO MICROEMPREENDEDOR E A EMPRESA DE PEQUENO PORTE S/A;".date("Y-m-d H:i:s").";".$titulo['Conta'][$num].";Open";
		$dados_inc .="\n"; 

		$n_c++;
		$n_s++;
		$n_f++;
		echo "insert into lcm_cont set 
			  id_case='".$n_c."', 
			  id_stage='".$n_s."', 
			  contrato='".$contrato."', 
			  p_adverso='".$titulo['Nome Cliente'][$num]."', 
			  date_creation='".date("Y-m-d H:i:s")."',	
			  date_assignment='".date("Y-m-d H:i:s")."',	
			  date_update='".date("Y-m-d H:i:s")."',	
			  legal_reason='', 
			  p_cliente='FINSOL SOCIEDADE DE CREDITO AO MICROEMPREENDEDOR E A EMPRESA DE PEQUENO PORTE S/A', 
			  comarca='', 
			  state='', 
			  vara='', 
			  notes='',
			  status='open', 
			  stage='investigation', 
			  public='1', 
			  pub_write='1'; 
			  <br>";
		//inserindo o endereço (logradouro)//
		if($titulo['Endereço'][$num]!="#N/D"){
			echo "insert into lcm_cont_lograd set
				  id_cont	='".$n_c."',
				  type		='other',
				  logradouro='".$titulo['Endereço'][$num]."',
				  number	='".$titulo['Número'][$num]."',
				  complement='".$titulo['Complemento'][$num]."',
				  district	='".$titulo['Bairro'][$num]."',
				  city		='".$titulo['Cidade'][$num]."',
				  state		='".$titulo['UF'][$num]."',
				  zipcode	='".$titulo['CEP'][$num]."',
				  fone_1	='".str_replace(".","-",$titulo['Tel'][$num])."',
				  fone_2	='',
				  data_cad	='".date("Y-m-d H:i:s")."',
				  hidden	='N';
				  <br>";
		}
		//inserindo stage //
		echo "insert into lcm_cont_stage set 
			  id_entry='".$n_s."',
			  id_case='".$n_c."',
			  kw_case_stage='investigation',
			  date_creation='".date("Y-m-d H:i:s")."',
			  id_fu_creation='".$n_f."',
			  date_conclusion='0000-00-00 00:00:00',
			  id_fu_conclusion='0',
			  kw_result='',
			  kw_conclusion='',
			  kw_sentence='',
			  sentence_val='',
			  date_agreement='0000-00-00 00:00:00',
			  latest='0';
			  <br>";
		//inserindo o andamento padrão com menos 30 dias para cair logo no acionamento do dia //
		echo "insert into lcm_cont_followup set 
			  id_followup='".$n_f."',
			  id_case='".$n_c."',
			  id_stage='".$n_s."',
			  id_author='1',
			  date_start='".date("Y-m-d H:i:s", strtotime("-30 days"))."',
			  date_end='".date("Y-m-d H:i:s", strtotime("-30 days"))."',
			  type='assignment',
			  description='1',
			  case_stage='investigation',
			  sumbilled='0.0000',
			  hidden='N';
			  <br>";
		//inserindo a ligação do contrato com o ausuário //
		echo "insert into lcm_cont_author set 
			  id_case='".$n_c."',
			  id_author='1',
			  ac_read='1',
			  ac_write='1',
			  ac_edit='1',
			  ac_admin='1';
			  <br>";
		
		foreach($cont_adver[$contrato] as $inscpf => $ref){
			if($insid_advr[$inscpf]!=""){
				echo "delete from lcm_cont_adverso_cliente where id_case='".$n_c."' and id_adverso='".$insid_advr[$inscpf]."' and id_cliente='0'; <br>";
				echo "insert into lcm_cont_adverso_cliente set 	id_case='".$n_c."',	id_adverso='".$insid_advr[$inscpf]."',	id_cliente='0'; <br>";
			}
		}
		
		//inserindo as parcelas//
		$fo=0;
		foreach($cont_parce[$contrato] as $parc => $venc){
			$fo++;
			echo "insert into lcm_cont_plots set  
			id_case='".$n_c."', 
			parcela='".$parc."', 
			tp='', 
			plano='".$venc[1]."', 
			vencimento='".$venc[0]."', 
			pagamento='0000-00-00', 
			principal='".$venc[2]."', 
			stparc='ATI', 
			hidden='N', 
			obs='';
			<br>";
			
			//criando novas parcelas a partir das que foram apontadas//
			if($fo==count($cont_parce[$contrato])){
				$R = $venc[1] - $parc;
				for ($i = 1; $i <= $R; $i++) {
					echo "insert into lcm_cont_plots set  
					id_case='".$n_c."', 
					parcela='".($parc+$i)."', 
					tp='', 
					plano='".$venc[1]."', 
					vencimento='".date('Y-m-d', strtotime('+'.$i.' months',strtotime($venc[0])))."', 
					pagamento='0000-00-00', 
					principal='".$venc[2]."', 
					stparc='ATI', 
					hidden='N', 
					obs='';
					<br>";
				}
			}	
		}
		echo "insert into lcm_cont_adverso_cliente set 	id_case='".$n_c."', id_adverso='0', id_cliente='14';<br>";
		echo "<hr>";
		
		
	}else{
		$wcont_A = mysql_fetch_array($sql_I);
		//a primeira parcela que entra no foreacho é igual a primeira parcela do constrato, caso não, pula //
		$prquery = mysql_query("SELECT MIN(p.parcela) as 'parcela' FROM lcm_cont_plots AS p WHERE p.id_case = ".$wcont_A['id_case']." AND p.stparc = 'ATI'");
		$prwhile = mysql_fetch_array($prquery);
		$parcCH  = array_keys($cont_parce[$contrato]);
		
		if(is_array($insct_advr[$contrato])){
			foreach($insct_advr[$contrato] as $inscpf => $ref){
				echo "delete from lcm_cont_adverso_cliente where id_case='".$wcont_A['id_case']."' and id_adverso='".$ref."' and id_cliente='0'; <br>";
				echo "insert into lcm_cont_adverso_cliente set 	id_case='".$wcont_A['id_case']."',	id_adverso='".$ref."',	id_cliente='0'; <br>";
			}
		}
		
		
		if($prwhile['parcela']!=$parcCH[0]){			
			//por vias das dúvidas retiramos as parcelas para inserir novamente, assim, temos a certeza de parcelas corretas //
			echo "delete from lcm_cont_plots where id_case = ".$wcont_A['id_case']."; <br>";
			$fp=0;
			foreach($cont_parce[$contrato] as $Aparc => $Avenc){
				$fp++;
				echo "insert into lcm_cont_plots set  
				id_case='".$wcont_A['id_case']."', 
				parcela='".$Aparc."', 
				tp='', 
				plano='".$Avenc[1]."', 
				vencimento='".$Avenc[0]."', 
				pagamento='0000-00-00', 
				principal='".$Avenc[2]."', 
				stparc='ATI', 
				hidden='N', 
				obs='';
				<br>";
				
				//criando novas parcelas a partir das que foram apontadas//
				if($fp==count($cont_parce[$contrato])){
					$R = $Avenc[1] - $Aparc;
					for ($f = 1; $f <= $R; $f++) {
						echo "insert into lcm_cont_plots set  
						id_case='".$wcont_A['id_case']."', 
						parcela='".($Aparc+$f)."', 
						tp='', 
						plano='".$Avenc[1]."', 
						vencimento='".date('Y-m-d', strtotime('+'.$f.' months',strtotime($Avenc[0])))."', 
						pagamento='0000-00-00', 
						principal='".$Avenc[2]."', 
						stparc='ATI', 
						hidden='N', 
						obs='';
						<br>";
					}
				}
			}
		}
	}
	
	//Verificando os constratos que foram localizados, porém, estão inativados, para ativar//
	$sql_U = mysql_query("SELECT * FROM lcm_cont as c where c.contrato  = '$contrato' AND status='closed'");
	if(mysql_num_rows($sql_U)>0){
		$wcont = mysql_fetch_array($sql_U);
		//ativamos o contrato //
		echo "UPDATE lcm_cont set status='open' where id_case=".$wcont['id_case']."; <br>";
	}
}

					
///Rodar todos os contratos para verificar o que será inativado //
$id_ina = mysql_query("SELECT * FROM lcm_cont as c 
		where c.p_cliente='FINSOL SOCIEDADE DE CREDITO AO MICROEMPREENDEDOR E A EMPRESA DE PEQUENO PORTE S/A' 
		and c.status='open' ");

//Arquivo csv das exclusões
$file_exc = 'logs_csv/contratos_saida_'.$dataatual.'.csv';  
$dados_exc = '';  
$dados_exc .="Contratos;Devedores;Cliente;Data da Criação;Cidade;Estado;Status";
$dados_exc .="\n"; 

while($nm_ina = mysql_fetch_array($id_ina)){
	if(!in_array($nm_ina['contrato'],array_unique($titulo['Contrato']))){
		echo "UPDATE lcm_cont set status='closed' where id_case=".$nm_ina['id_case']." and contrato = '".$nm_ina['contrato']."'; <br>";
		$dados_exc .= $nm_ina['contrato'].";".$nm_ina['p_adverso'].";".$nm_ina['p_cliente'].";".$nm_ina['date_creation'].";".$nm_ina['comarca'].";".$nm_ina['state'].";Closed";
		$dados_exc .="\n";
	}
}

//Gerando arquivo csv das inclusões
if(fwrite($file=fopen($file_inc,'w+'),$dados_inc)) {
	fclose($file); 
	echo "<br><br><a href='logs_csv/contratos_entrada_".$dataatual.".csv'>contratos_entrada.CSV</a><br>";
} else {
	echo "Problemas ao gerar o arquivo, tente novamente!";  
} 

//Gerando arquivo csv das exclusões
if(fwrite($file=fopen($file_exc,'w+'),$dados_exc)) {
	fclose($file);  
	echo "<a href='logs_csv/contratos_saida_".$dataatual.".csv'>contratos_saida.CSV</a><br>";
} else {
	echo "Problemas ao gerar o arquivo, tente novamente!";  
} 

?>