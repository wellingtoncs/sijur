<?php
	
	
	require_once("functions.php");
	include("seguranca.php");
	protegePagina();
	$doc_buffer = $_POST['name_text'];
	$tipo_id = $_POST['tipo_id'];
	$nomtipo = fc_select_name('tipo_id',$tipo_id,'tipo_nome','tp_tipo_tb',$conexao1);
	$nomtipo = limita_caracteres($nomtipo,20,false);
	 
	$nomecli = preg_replace("[^a-zA-Z0-9_]", "", strtr($_POST['nomepet'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ ", "aaaaeeiooouucAAAAEEIOOOUUC_"));
	$nomtipo = preg_replace("[^a-zA-Z0-9_]", "", strtr($nomtipo, "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ= ", "aaaaeeiooouucAAAAEEIOOOUUC-_"));
	$nompeca = $nomtipo."-".$nomecli;
	
    ob_start();
	echo '<style>.titulos{text-align: center; border: solid 1px black; font-weight:bold;} p{margin:0px;line-height:115%;font-size:12pt;}</style>';
    echo '<page backtop="28mm" backbottom="10mm" backleft="25mm" backright="15mm" >';
	echo '<page_header>';
	echo '<div style="margin-left:20mm; margin-right:15mm; margin-top:0mm; ">'.cabecalhoerodape($tipo_id,"cab","pdf").'</div>';
	echo '</page_header>';
    echo '<page_footer>';
	echo '<div style="margin-left:20mm; margin-right:15mm; margin-bottom:0mm; color: #333333">'.cabecalhoerodape($tipo_id,"rod","pdf").'</div>';
	echo '</page_footer>';
if($_POST['is_pecas']==1){
	$query_pecas = mysql_query("SELECT * from tp_pecas_tb where id_pecas='".$_POST['id_pecas']."' ", $conexao1) or die(mysql_error());
	$arr_pecas = mysql_fetch_array($query_pecas);
	echo $arr_pecas['cod_pecas'];
} else {
	echo $doc_buffer;
}
	echo '</page>';
    $content = ob_get_clean();
    // convert in PDF
    require_once('../html2pdf_v4.03/html2pdf.class.php');
    try
    {
        $html2pdf = new HTML2PDF('P','A4','pt');
	//  $html2pdf->setModeDebug();
        $html2pdf->setDefaultFont('Times');
        $html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        $html2pdf->Output($nompeca.'.pdf','D');
    }
    catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }

?>