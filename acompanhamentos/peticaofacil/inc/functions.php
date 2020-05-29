<?php
function formata_data_extenso($strDate)
{
	$arrMonthsOfYear = array(1 => 'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
	$intDayOfMonth = date("d");
	$intMonthOfYear = date("n");
	$intYear = date("Y");
	return $intDayOfMonth . ' de ' . $arrMonthsOfYear[$intMonthOfYear] . ' de ' . $intYear. '.';
}

function fc_select($p_tb,$p_id,$val_id,$val_nome,$usu,$conex,$p_setor="")
{
	$q = mysql_query("SELECT $val_id , $val_nome FROM " . $p_tb. " " . ($usu!="" ? "where tipo_usu = " . $usu : "") . " " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " GROUP BY " . $val_nome . " ORDER BY " . $val_nome. " ",$conex);
	echo "<option></option>";
	
	while($w = mysql_fetch_array($q))
	{
		echo "<option value='" . $w[$val_id] . "' " . ($w[$val_id] == "$p_id" ? "selected" : "") . ">" . $w[$val_nome] . "</option>";
	}
}
function fc_select_li($p_tb,$p_id,$val_id,$val_nome,$usu,$conex,$p_setor="")
{
	$q = mysql_query("SELECT $val_id , $val_nome FROM " . $p_tb. " " . ($usu!="" ? "where tipo_usu = " . $usu : "") . " " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " GROUP BY " . $val_nome . " ORDER BY " . $val_nome. " ",$conex);	
	while($w = mysql_fetch_array($q))
	{
		//echo "<li><a class='icon-16-copy' href='form.php?TIPOPET=".$w[$val_id]."' >" . $w[$val_nome] . "</a></li>";
		echo "<li><a class='icon-16-copy' href='#' onclick='EnviarDados(\"form.php\",\"$p_id\",".$w[$val_id].");' >" . $w[$val_nome] . "</a></li>";
	}
}
function fc_select_div($p_tb,$p_id,$val_id,$val_nome,$usu,$se,$conex,$p_setor="")
{
	$q = mysql_query("SELECT $val_id , $val_nome FROM " . $p_tb. " " . ($usu!="" ? "where tipo_usu = " . $usu : "") . " " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " GROUP BY " . $val_nome . " ORDER BY " . $val_nome. " ",$conex);
	while($w = mysql_fetch_array($q))
	{
		echo "<div class='icon-wrapper'>
				<div class='icon'>";
				if($se=="E"){
					echo "<a href='#' onclick='mark_active(this)' class='clspet' grupo='0' numpet='" . $w[$val_id] . "'>";
				}elseif($se=="S"){
					echo "<a href='#' onclick='EnviarDados(\"form.php\",\"$p_id\",".$w[$val_id].");'>";
				}
					echo "<img src='css/images/header/icon-48-article.png' alt=''  />";
					echo "<span style='position:relative;margin-top:-30px'> &nbsp; " . trim($w[$val_nome]) . " &nbsp; </span>
					</a>
				</div>
			</div>";
	}
}
function fc_select_dados($id_input,$conex,$p_setor="")
{
	$q = mysql_query("SELECT id_dados, nome_dados FROM tp_dados_tb where id_input = '$id_input' " . ($p_setor!="" ? "and id_setor = " . $p_setor : "") . " ORDER BY nome_dados asc ",$conex);
	echo "<option></option>";
	
	while($w = mysql_fetch_array($q))
	{
		echo "<option value='" . $w['id_dados'] . "' " . ($w[$val_id] == "$p_id" ? "selected" : "") . ">" . $w['nome_dados'] . "</option>";
	}
}

function fc_select_name($cond,$where,$col,$banco,$conex)
{
	if($where!='' && $col !='' && $banco !='')
	{
		$campo = explode("|_|",$col);
		$sel  = " SELECT ";
		
		for($i=0;$i<=count($campo);$i++)
		{
			if($campo[$i] != '')
			{
				$sel .= ($i> 0 ? (',' . $campo[$i]) : $campo[$i] );
			}
		}
		$sel .= " FROM $banco";
		$sel .= " where $cond = $where";
		$sel .= " limit 1";			
		$q = mysql_query($sel,$conex);
		$w = mysql_fetch_array($q);
		return $w[0];
		//return "SELECT $col FROM $banco where $cond = $where limit 1"; //$w[0];
	}
	else
	{
		return '';
	}
	
}

//Maiúscula
function upwords($str){
	return preg_replace('#\s(como?|d[aeo]s?|desde|para|por|que|sem|sob|sobre|trás)\s#ie', '" ".strtolower("\1")." "', ucwords($str));
}

function convertemin($term) {
    $palavra = strtr(strtolower($term),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß","àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");
    return $palavra;
}

function limita_caracteres($texto, $limite, $quebra = true)
{
   $tamanho = strlen($texto);
   if($tamanho <= $limite){ //Verifica se o tamanho do texto é menor ou igual ao limite
      $novo_texto = $texto;
   }else{ // Se o tamanho do texto for maior que o limite
      if($quebra == true){ // Verifica a opção de quebrar o texto
         $novo_texto = trim(substr($texto, 0, $limite))."...";
      }else{ // Se não, corta $texto na última palavra antes do limite
         $ultimo_espaco = strrpos(substr($texto, 0, $limite), " "); // Localiza o útlimo espaço antes de $limite
         $novo_texto = trim(substr($texto, 0, $ultimo_espaco)).""; // Corta o $texto até a posição localizada
      }
   }
   return $novo_texto; // Retorna o valor formatado
}
function fc_botoes($valor,$displ)
{
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_inputs(\"E\",\"" . $valor . "\");' class='button_del' title='Editar Campo'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_input(\"" . $valor . "\");' class='button_del' title='Excluir Campo'>Excluir</a></span>
			</div>";
}
function fc_botoes_usu($id_usu,$displ,$nome="")
{
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_usu(\"$id_usu\",\"U\");' class='button_del' title='Editar Usuário'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_usu(\"$id_usu\",\"$nome\");' class='button_del' title='Excluir Usuário'>Excluir</a></span>
			</div>";
}
function fc_botoes_setor($id_setor,$displ,$nome="")
{
	return "<div id='module-status' style='display:" . $displ . ";'>
				<span class='editar'><a href='javascript:fc_edit_setor(\"$id_setor\",\"U\");' class='button_del' title='Editar Setor'>Editar</a></span>
				<span class='excluir'><a href='javascript:fc_del_setor(\"$id_setor\",\"$nome\");' class='button_del' title='Excluir Setor'>Excluir</a></span>
			</div>";
}

function cabecalhoerodape($tipoid,$rodcab,$rtfpdf){
	if($rtfpdf=="rtf"){
		require_once("Html2Rtf/class_rtf_2.php");
		if($rodcab=="cab"){
			$codcab = new rtf("Html2Rtf/rtf_config.php");
			$queryc = mysql_query("SELECT t.cod_cabec FROM tp_tipo_tb AS t WHERE t.tipo_id = '".$tipoid."' ");
			$whilec = mysql_fetch_array($queryc);
			$codcab->addText($whilec['cod_cabec']);
			return $codcab->getDocument();
			
		} elseif($rodcab=="rod"){
			$codrod = new rtf("Html2Rtf/rtf_config.php");
			$queryr = mysql_query("SELECT t.cod_rodap FROM tp_tipo_tb AS t WHERE t.tipo_id = '".$tipoid."' ");
			$whiler = mysql_fetch_array($queryr);
			$codrod->addText($whiler['cod_rodap']);
			return $codrod->getDocument();
		}else{
			return "";
		}
	}elseif($rtfpdf=="pdf"){
		require_once("seguranca.php");
		$querycr = mysql_query("SELECT t.cod_cabec, t.cod_rodap FROM tp_tipo_tb AS t WHERE t.tipo_id = '".$tipoid."' ");
		$whilecr = mysql_fetch_array($querycr);
		$dire = $_SERVER['DOCUMENT_ROOT'];
		
		if($rodcab=="cab"){
			return str_replace('src="','src="'.$dire.'',$whilecr['cod_cabec']);
		} elseif($rodcab=="rod"){
			return str_replace('src="','src="'.$dire.'',$whilecr['cod_rodap']);
		}else{
			return "";
		}
	}else{
		return "";
	}
}

?>
