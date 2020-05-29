<?php

    $arr_dados = array();
    
    $arr_dados[1]['loan_no']		= '541645'; 
    $arr_dados[1]['remarks']		= 'CLI COM AÇÃO SEM INTERESSE EM NEG NO MOMENTO.';
	$arr_dados[1]['evento']			= 'd41';
	
    $arr_dados[1]['loan_no']		= '541645'; 
    $arr_dados[1]['remarks']		= 'CLI COM AÇÃO SEM INTERESSE EM NEG NO MOMENTO.';
	$arr_dados[1]['evento']			= 'd22';
    
    
    if (isset($_POST['cont']))
    {
        $cont = $_POST['cont'] + 1; sleep(1);       
    }
    else
    {
        $cont = 1;
    }
    
    $arr_dado = $arr_dados[$cont];
    
?>
<html>
    <head>
        <style>
            table tr td{
                font-family: arial;
                font-size: 12px;
            }
        </style>
        <script>
            function fc_submit(res)
            {
                document.getElementById('div_form').style.display = 'none';
                document.getElementById('div_proc').style.display = ''; 
                document.frm_robo.resultado.value = res;
                document.frm_robo.submit();
            }
        </script> 
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>    
    <body>
        <table width="100%"> 
            <tr>
                <td width="80px"> 
                    <img src="img/robot.png" />
                </td>   
                <td>
                    Rob&ocirc;: EA001<br />
                    Atualiza&ccedil;&atilde;o dos eventos jur&iacute;dicos no Legem<br />
                    Criado em Abril de 2015<br />
					Contato: julianophp@gmail.com; alexandre.cavalcanti@bb.com.br (81) 9924-5330
                </td>    
            </tr>
            <tr>
                <td colspan="2"><hr></td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="padding: 5px;" id="div_form">
                        <form method="POST" name="frm_robo">
                            <input type="hidden" name="resultado" value="N">
                            <input type="hidden" name="cont" value="<?php echo $cont; ?>"> 
                            <table width="300px">
                                <tr><td>C&oacute;digo Contrato:</td><td><input name="loan_no" type="text" value="<?php echo $arr_dado['loan_no']; ?>" /></td></tr>
                                <tr><td>Vara:</td><td><input name="remarks" type="text" value="<?php echo utf8_encode($arr_dado['remarks']); ?>"/></td></tr>
                                <tr><td>Data:</td><td><input name="evento" type="text" value="<?php echo utf8_encode($arr_dado['evento']); ?>"/></td></tr>
                                <tr>
                                    <td colspan="2" align="center">
                                        <br />
                                        <input type="button" value="Positivo" style="color: white; background: green;" onclick="fc_submit('P')">&nbsp;&nbsp;&nbsp;
                                        <input type="button" value="Negativo" style="color: white; background: red;"   onclick="fc_submit('N')">
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div id="div_proc" style="padding: 5px; display: none;">Processando...</div>
                </td>    
            </tr>    
        </table>   
    </body>    
</html>    
