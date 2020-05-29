<?php

ob_start();
        $user = 'eduardo.andamento';
        $pass = '205575';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'http://www.kurierservicos.com.br/wsservicos/api/KAndamento/ConsultarAndamentos' ); 
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . base64_encode( $user . ':' . $pass ) ) );
        curl_exec( $ch );
        $resposta = ob_get_contents();
        ob_end_clean();
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );


#print_r($resposta);