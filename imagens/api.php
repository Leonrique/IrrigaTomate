<?php

  function media( $strVar ) {

     //echo "\nstrVar ==>  $strVar\n";
     $list = explode(",", $strVar);
     $num = count($list);
 
     // retirar todos os valores nulos ou nao significativos
     for($i = ($num - 1); $i >=0 ; $i--) {

       //if( is_null($list[$i]) || !is_numeric($list[$i]) || $list[$i] === "null" || $list[$i] === "") {
       if( is_null($list[$i]) || !is_numeric($list[$i])||  $list[$i] === "") {

         if( $i < ($num - 1) )
            for($j = $i; $j < ($num - 1); $j++)
               $list[$j] = $list[$j + 1];
   
         $num = $num - 1; 

       }// final de if( is_null($list[$i]) |.....

     } // final do for
 
 
     if($num == 0)
       return(-1000);
//echo "\n num = $num \n";
     $media = 0;
     for($i = 0; $i < $num; $i++)
        $media = $media + (float)$list[$i] ;
     $media = $media/$num;
     return ($media);
  }


  function insertDados( $estacao, $diaX, $opt, $id ) {

     // Clients public and private key provided by service provider
     $public_key = "07cbfaa20e767584fc192dd491ef11bad98b8f6cee74c761";
     $private_key = "1631dd56c034ea58f52b50a90e31f99e75fe38ea18a5dcae";

     date_default_timezone_set('America/Sao_Paulo');
 
     // Define the request parameter's
     $method = "GET";

     //$request = "/data/$estacao/raw/from/1589241600/to/1589319000";
     $inicio = strtotime("$diaX  00:00:01") ; // timestamp unix menos 7200
     $a = (int) $inicio;
     $a =      $a + 7200;
     $fim = strtotime("$diaX  23:59:59") ;
     $b = (int) $fim;
     $b =      $b + 7200;

     $request = "/data/$estacao/raw/from/$a/to/$b";

     //$timestamp = gmdate('D, d M Y H:i:s T'); // Date as per RFC2616 - Wed, 25 Nov 2014 12:45:26 GMT
     $timestamp = date('D, d M Y H:i:s T'); 

     $list = explode("-", $diaX);
     $dia = $list[2];
     $mes = $list[1];
     $ano = $list[0];

     // Creating content to sign with private key
     $content_to_sign = $method.$request.$timestamp.$public_key;
 
     // Hash content to sign into HMAC signature
     $signature = hash_hmac("sha256", $content_to_sign, $private_key);

     // Add required headers
     // Authorization: hmac public_key:signature
     // Date: Wed, 25 Nov 2014 12:45:26 GMT
     $headers = [
         "Accept: application/json",
         "Authorization: hmac {$public_key}:{$signature}",
         "Date: {$timestamp}"
     ];
 
     // Prepare and make https request
     $ch = curl_init();
     //curl_setopt($ch, CURLOPT_URL, "https://api.fieldclimate.com/THIS-VERSION" . $request);
     curl_setopt($ch, CURLOPT_URL, "https://api.fieldclimate.com/v2" . $request);
 
     // SSL important
     curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
 
     $output = curl_exec($ch);
 
     curl_close($ch);

     // Parse response as json and work on it ..
     //echo $output. PHP_EOL;

     $output2 = $output;
     $tam = strlen($output);
     $fim = 0;
     $i = 0;
     while (!$fim) {
 
          if(!$pos2 = stripos($output2, "name_original")) {

             $fim = 1;
             $tam = $i;
             $i = 200;
 
          }
          if(  $i < 200 ) {
          

                  $pos3 = $pos2 + strlen("name_original") ;
                  $output2 = substr_replace($output2, "0000_original", 0, $pos3);
                  if( !$saida = stristr($output2, 'name_original', true) )
                     $saida = $output2;
                  $i = $i + 1;
                  $posicao[$i] = $saida;
                  //echo "<hr>\n $i   $posicao[$i]";
                  
          }
          else
            $fim = 1;
     }

     // Obter as variaveis para o calculo da temperatura de evapotranspiracao
     // radiacao solar, precipitacao, velocidade do vento, temperatura do ar e umidade relativa
     //

     $outputRadSol = "";
     $outputPrecip = "";
     $outputVelVento = "";
     $outputTempAr = "";
     $outputHumidRelat = "";

     for ($i =1; $i <=  $tam; $i++) {

        if($posVar = stripos($posicao[$i], "Solar radiation"))
           $flag = 1;
        else
           if($posVar = stripos($posicao[$i], "Precipitation"))
              $flag = 2;
        else
           if($posVar = stripos($posicao[$i], "Wind speed max"))
              $flag = 0;
           else
           if($posVar = stripos($posicao[$i], "Air temperature"))
              $flag = 4;
           else
           if($posVar = stripos($posicao[$i], "Relative humidity"))
              $flag = 5;
           else
              if(  $posVar = stripos($posicao[$i], "Wind speed") )
                 $flag = 3;
              else
                { $flag = 0; $posVar = -1; }

        
        if($posVar > 0) {
 
               $pos = stripos($posicao[$i], "values");
               $output = substr($posicao[$i], $pos + 6, -1);
      //echo "\n\n ---> substr = $output\n";
               $pos2 = stripos($output, "]");
               $output = substr($output, 0, $pos2);

      //echo "\n\n ---> substr = $output\n";
               $pos2 = stripos($output, "[");
               $output = substr($output, $pos2 + 1, strlen($output) );

      //echo "\n\n ---> substr = $output\n";

               if($flag == 1)
                  $outputRadSol = $output;
               else
                 if($flag == 2)
                    $outputPrecip = $output;
                 else
                    if($flag == 3)
                       $outputVelVento = $output;
                    else
                       if($flag == 4)
                           $outputTempAr = $output;
                       else
                           if($flag == 5)
                              $outputHumidRelat = $output;


        } // final de   if($pos > 0) {

     } // final de for ($i =1; $i <=  $tam; $i++) {

     $radSol = media($outputRadSol);
     $precipit = media($outputPrecip); // por enquanto nao vai ser usada esta variavel, mas poderah ser usada mais para frente.
     $velVento = media($outputVelVento);
     $tempAr = media($outputTempAr);
     $umidRel = media($outputHumidRelat);
     if  ( $radSol  != -1000 && $velVento != -1000 && $tempAr != -1000 && $umidRel != -1000  ) { 
     
          $eto1 = pow(10, ((7.5*$tempAr)/(237.3+$tempAr)) );
          $eto2 = pow( ($tempAr +237.3), 2);
          $etoX=((0.408*((4098*(0.6108*$eto1))/$eto2)*$radSol*0.55*0.0864)+(0.063*900*$velVento*((0.6108*$eto1)-(((0.6108*$eto1)*$umidRel)/100)))/($tempAr+275))/(((4098*(0.6108*$eto1))/$eto2)+0.063*(1+0.34*$velVento));

          if( $opt == 2) {

            $update = "update evapoTrasnpiracaoTomateEstacao set temMedia = $tempAr where id = $id;";
            $update2 = "update evapoTrasnpiracaoTomateEstacao set eto = $etoX where id = $id;";
            echo "\n   $update \n $update2";
          }
          else {

                $insert = "insert into evapoTrasnpiracaoTomateEstacao(dia, mes, ano, idCidade, temMedia, eto, codEstacao) values ($dia, $mes, $ano, $id, $tempAr, $etoX, \"$estacao\"); ";
                echo "\n   $insert \n";

          }

     }

          //echo "\n   $dia-$mes-$ano  --- > rs = $radSol    ppt = $precipit    vv = $velVento tempAr = $tempAr    UR = $umidRel   eto =  $etoX  \n";

  } // final de function insertDados



     //
     // Abrir base de dados para inserir um atualizar dados
     //
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "<municipio>1</municipio>";
            exit(1);
       }



     //
     // Atualizar dados faltantes na base de dados
     //
     $sql = "select id, dia, mes, ano, codEstacao from evapoTrasnpiracaoTomateEstacao where eto is null or temMedia is null;";
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
              mysqli_close($conexao);
              echo "Houve erro nesta consulta!";
              echo $sql;
              exit(1);
           }

     $numLinhas = $query->num_rows;

     if ($numLinhas > 0)
       {
              $opt = 2; // update = 2, insert = 1
              while( $linha=$query->fetch_row() ) {

                $data = $linha[1]."-".$linha[2]."-".$linha[3];
                $codigo = $linha[4];
                $id = $linha[0];
                insertDados($codigo, $data, $opt, $id);
              }// fim do while


       }


    //
    // Inseir dados novos na base de dados
    //

     $sql = "select distinct codEstacao from evapoTrasnpiracaoTomateEstacao;";
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
              mysqli_close($conexao);
              echo "Houve erro nesta consulta!";
              echo $sql;
              exit(1);
           }

     $numLinhas = $query->num_rows;
     date_default_timezone_set('America/Sao_Paulo');

     if ($numLinhas > 0) {


              $hojeDia = date('d'); 
              $hojeMes = date('m'); 
              $hojeAno = date('Y'); 
              $opt = 1; // update = 2, insert = 1

              while( $linha=$query->fetch_row() ) {

                $codigo = $linha[0];
                $sql = "select  max(ano) from evapoTrasnpiracaoTomateEstacao where codEstacao = \"$codigo\" ;";
                $query2 = mysqli_query($conexao, $sql) ;
                $linha2 = $query2->fetch_row(); 
                $ano = $linha2[0];
                
                $sql = "select  max(mes) from evapoTrasnpiracaoTomateEstacao where codEstacao = \"$codigo\" and ano = $ano ;";
                $query2 = mysqli_query($conexao, $sql) ;
                $linha2 = $query2->fetch_row(); 
                $mes = $linha2[0];
                $sql = "select  max(dia) from evapoTrasnpiracaoTomateEstacao where  codEstacao = \"$codigo\" and ano = $ano  and mes = $mes;";
                $query2 = mysqli_query($conexao, $sql) ;
                $linha2 = $query2->fetch_row(); 
                $dia = $linha2[0];
                $sql = "select  idCidade from evapoTrasnpiracaoTomateEstacao where codEstacao = \"$codigo\" ;";
                $query2 = mysqli_query($conexao, $sql) ;
                $linha2 = $query2->fetch_row(); 
                $id = $linha2[0];
                $diaInicial = $dia.'-'.$mes.'-'.$ano;
                $data2 = new DateTime($diaInicial);
                $data2->modify('+1 day');
                $dia = $data2->format('d');
                $mes = $data2->format('m');
                $ano = $data2->format('Y');
                $data = $ano."-".$mes."-".$dia;

                while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {
   
                   insertDados($codigo, $data, $opt, $id);
                   $data2->modify('+1 day');
                   $dia = $data2->format('d');
                   $mes = $data2->format('m');
                   $ano = $data2->format('Y');
                   $data = $ano."-".$mes."-".$dia;

                }// final do while

              }// fim do while

     }



    mysqli_close($conexao);


?>
