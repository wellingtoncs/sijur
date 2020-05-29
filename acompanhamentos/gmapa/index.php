<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="utf-8" />
        <title>Google Maps API v3: Criando um mapa personalizado</title>
        <link rel="stylesheet" type="text/css" href="css/estilo.css">
    </head>
 
    <body>
    	<div id="mapa">
        </div>
		
		<script src="js/jquery.min.js"></script>
 
        <!-- Maps API Javascript -->
        <script src="http://maps.googleapis.com/maps/api/js?sensor=false"></script>
        
        <!-- Caixa de informação -->
        <script src="js/infobox.js"></script>
		
        <!-- Agrupamento dos marcadores -->
		<script src="js/markerclusterer.js"></script>
 
        <!-- Arquivo de inicialização do mapa -->
		<?php 
		if($_GET['pag']=='all'){ ?>
			<script src="js/mapa_all.js"></script>
		<?php } else { ?>
			<script src="js/mapa.js"></script>
		<?php }?>
    </body>
</html>