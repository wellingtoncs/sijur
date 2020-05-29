<?php

    $arr_dados = array();
    
    $arr_dados[1]['cod_int']        = 1234; 
    $arr_dados[1]['vara']           = 'PROCON';
    $arr_dados[1]['data']           = date('d/m/Y');
    $arr_dados[1]['atividade']      = 'ALVARÁ LEVANTADO';
    $arr_dados[1]['comentario']     = 'TESTE';

    $arr_dados[2]['cod_int']        = 1234; 
    $arr_dados[2]['vara']           = 'OUTRO';
    $arr_dados[2]['data']           = date('d/m/Y');
    $arr_dados[2]['atividade']      = 'ALVARÁ LEVANTADO';
    $arr_dados[2]['comentario']     = 'TESTE';
    
    $arr_dados[3]['cod_int']        = 1234; 
    $arr_dados[3]['vara']           = 'PROCON';
    $arr_dados[3]['data']           = date('d/m/Y');
    $arr_dados[3]['atividade']      = 'ALVARÁ LEVANTADO';
    $arr_dados[3]['comentario']     = 'TESTE';
    
    $arr_dados[4]['cod_int']        = 1234; 
    $arr_dados[4]['vara']           = 'OUTRO';
    $arr_dados[4]['data']           = date('d/m/Y');
    $arr_dados[4]['atividade']      = 'ALVARÁ LEVANTADO';
    $arr_dados[4]['comentario']     = 'TESTE';
    
    
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
                    Rob&otilde;: EA001<br />
                    Atualiza&ccedil;&atilde;o dos eventos jur&iacute;dicos no Legem<br />
                    Criado em Abril de 2015
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
                                <tr><td>C&oacute;digo Interno:</td><td><input name="cod_int" type="text" value="<?php echo $arr_dado['cod_int']; ?>" /></td></tr>
                                <tr><td>Vara:</td><td><input name="vara" type="text" value="<?php echo $arr_dado['vara']; ?>"/></td></tr>
                                <tr><td>Data:</td><td><input name="data" type="text" value="<?php echo $arr_dado['data']; ?>"/></td></tr>
                                <tr><td>Atividade:</td><td><input name="atividade" type="text" value="<?php echo utf8_encode($arr_dado['atividade']); ?>"/></td></tr>
                                <tr><td>Coment&aacute;rios:</td><td><input name="comentario" type="text" value="<?php echo $arr_dado['comentario']; ?>"/></td></tr>
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
