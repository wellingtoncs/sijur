<html>
    <head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <style>
            table tr td{
                font-family: arial;
                font-size: 12px;
            }
        </style>
        <script>

			$( document ).ready(function() {

				setTimeout(function(){fc_submit('')}, 500);				

			});
			
            function fc_submit(res)
            {
				$('#div_form').hide(); 
				$('#div_wait').show();
				
                document.frm_robo.resultado.value = res;
				document.frm_robo.robo.value = 'SRS';

				$.post("../ajax.php", $("#frm_robo").serialize(), function (ret_ajax) {
									
					if (ret_ajax == 'N')
					{
						setTimeout(function(){fc_submit('')}, 2000);
					}	
					else	
					{	
						$('#div_form').html(ret_ajax);									
						$('#div_wait').hide();
						$('#div_form').show(); 
					}	
				});
            }
        </script>         	
    </head>    
    <body>
        <table width="100%"> 
            <tr>
                <td width="80px"> 
                    <img src="img/robot.png" />
                </td>   
                <td>
                    Rob&ocirc;: EA002<br />
                    Atualiza&ccedil;&atilde;o dos eventos jur&iacute;dicos no SRS<br />
                    Criado em Abril de 2015<br />
					Contato: julianophp@gmail.com; alexandre.cavalcanti@bb.com.br (81) 9924-5330
                </td>    
            </tr>
            <tr>
                <td colspan="2"><hr></td>
            </tr>
            <tr>
                <td colspan="2">				 
                    <div style="padding: 5px; display: none;" id="div_form">
						<form method="POST" name="frm_robo" id="frm_robo">
							<input type="hidden" name="txt_id_followup" value="">
							<input type="hidden" name="resultado" value="">					
							<input type="hidden" name="robo" value="">
						</form>					
					</div>
					<div id="div_wait" style="padding: 5px;">Aguardando...</div>									
                </td>    
            </tr>    
        </table>   		
    </body>    
</html>    
