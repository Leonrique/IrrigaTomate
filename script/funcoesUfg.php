<?php

  //include 'getDadosNasa.php';

  function media( $strVar ) {

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
     $media = 0;
     for($i = 0; $i < $num; $i++)
        $media = $media + (float)$list[$i] ;
     $media = $media/$num;
     return ($media);
  }


  //function insereDados( $estacao, $diaX, $opt, $id, $idCidade, $codigoEstacaoCidade, $numCidadesGoias ) {

  function insereDados( $estacao, $diaX, $opt, $id, $idCidade ) {

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
               $pos2 = stripos($output, "]");
               $output = substr($output, 0, $pos2);

               $pos2 = stripos($output, "[");
               $output = substr($output, $pos2 + 1, strlen($output) );


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

     $retorno = "";
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

            $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where id = $id;#";
            $update2 = " update evapoTranspiracaoTomateEstacao set eto = $etoX where id = $id;#";
            $update3 = " update evapoTranspiracaoTomateEstacao set validado = 1 where id = $id;";
            $retorno = $update.$update2.$update3;
          }
          else {

                $insert = "insert into evapoTranspiracaoTomateEstacao(dia, mes, ano, idCidade, temMedia, eto, codEstacao, validado) values ($dia, $mes, $ano, $idCidade, $tempAr, $etoX, \"$estacao\", 1); ";
                $retorno = $insert;

          }

     }

     return($retorno);

  } // final de function insereDados


function getDadosUfg() {

     //
     // Abrir base de dados para inserir um atualizar dados
     //
     //include 'pathConfig.php';
     //$arquivoPath = configPath;
     //include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "Erro ao se conectar com a base de dados. Veja arquivo config.php";
            exit(1);
     }

     date_default_timezone_set('America/Sao_Paulo');
     $hojeDia = date('d');
     $hojeMes = date('m');
     $hojeAno = date('Y');

     //
     // Atualizar dados faltantes na base de dados
     //

     // Nome, IdCidade, CodigoEstacao
     // Itaberai  2259  00203E1D
     // Palmeiras de Goias  3488  00203E21
     // Hidrolandia  2037  00203E22
     // Piracanjuba  3763  00203E24
     // Silvania  4960  00203E26
     // Alexania  109  00206B1B

     // Incluir as cidades do estado de goias
     //   include ("dadosEstacoes.php");
 
     $sql = "select id, dia, mes, ano, codEstacao, idCidade from evapoTranspiracaoTomateEstacao where  ( codEstacao = \"00206B1B\" or codEstacao = \"00203E21\"  or codEstacao = \"00203E1D\"  or codEstacao = \"00203E22\"  or codEstacao = \"00203E24\"  or codEstacao = \"00203E26\") and (eto is null or temMedia is null or validado = 0) ;";
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
 
                //$data = $linha[1]."-".$linha[2]."-".$linha[3];
                $codigo = $linha[4];
                $id = $linha[0];
                if( strlen($linha[1] ) == 1)
                   $diaV = "0".$linha[1];
                else
                   $diaV = $linha[1];
                if( strlen($linha[2] ) == 1)
                   $mesV = "0".$linha[2];
                else
                   $mesV = $linha[2];
                $anoV = $linha[3];
                $data = $diaV."-".$mesV."-".$anoV;
                $idCidadeV = $linha[5];

                //$retorno = insereDados($codigo, $data, $opt, $id, $idCidade, $codigoEstacaoCidade, $numCidadesGoias);

                $retorno = insereDados($codigo, $data, $opt, $id, $idCidadeV);
 
                if( strlen($retorno) > 0 ) {

                      $list = explode("#", $retorno);
                      $queryInsert = mysqli_query($conexao, $list[0]) ;
                      $queryInsert2 = mysqli_query($conexao, $list[1]) ;
                      $queryInsert3 = mysqli_query($conexao, $list[2]) ;
                     
                      if(!$queryInsert || !$queryInsert2 || !$queryInsert3 )
                        {          
                            echo "Houve erro nesta Insercao de dados!  $retorno";
                            $registro = "Houve erro nesta consulta ==>  $retorno";
                            $arquivo = "log-Update-".$hojeDia."-".$hojeMes."-".$hojeAno.".log";
                            if ( ! ($fp = fopen($arquivo, 'w')) ) {
     
                                fprintf($fp, "%s", $retorno);
                                fclose($fp);
                            }
                        }

                } // final de if( strlen($retorno) > 0 )
                else {

                   $codigo = $linha[4];
                   if( !strcmp($codigo , "00203E1D") ) {

                    $lat ="-16.0206";
                    $lon = "-49.806";
                   }
                   else
                    if( !strcmp($codigo , "00203E21") ) {

                      $lat ="-16.8044";
                      $lon = "-49.924";
                    }
                    else
                     if( !strcmp($codigo , "00203E22") ) {

                      $lat ="-16.9626";
                      $lon = "-49.2265";
                     }
                     else
                       if( !strcmp($codigo , "00203E24") ) {

                        $lat ="-17.302";
                        $lon = "-49.017";
                       }
                       else
                         if( !strcmp($codigo , "00203E26") ) {

                         $lat ="-16.6769";
                         $lon = "-48.6181";
                        }
                        else
                          if( !strcmp($codigo , "00206B1B") ) {

                            $lat ="-16.0834";
                            $lon = "-48.5076";
                          }

                        if( strlen($linha[1] ) == 1)
                           $diaV = "0".$linha[1];
                        else
                           $diaV = $linha[1];
                        if( strlen($linha[2] ) == 1)
                           $mesV = "0".$linha[2];
                        else
                           $mesV = $linha[2];
                        $anoV = $linha[3];
                        $diaNasa = $anoV.$mesV.$diaV; // para o caso da NASA, o dia tem que ter "0" caso soh tenha um digito
                        $diaV = $linha[1]; // para o banco de dados
                        $mesV = $linha[2];
                       
                        $resultadoNasa =  getvarNasa($diaNasa, $lat, $lon);
                        $varClimaticas = explode(",", $resultadoNasa);
                        $tempAr = $varClimaticas[0];
                        $eto = $varClimaticas[1];
                        if( $tempAr > -40 && $eto > -40 ) {

                                 $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where mes = $mesV and dia = $diaV and ano = $anoV and idCidade = $idCidadeV;";
                                 $pesquisa = mysqli_query($conexao, $update);
                                 $update = " update evapoTranspiracaoTomateEstacao set eto = $eto where mes = $mesV and dia = $diaV and ano = $anoV and idCidade = $idCidadeV;";
                                 $pesquisa = mysqli_query($conexao, $update);
                                 $update = " update evapoTranspiracaoTomateEstacao set validado = 0 where mes = $mesV and dia = $diaV and ano = $anoV and idCidade = $idCidadeV;";
                                 $pesquisa = mysqli_query($conexao, $update);
                        }
                        else
                          if( $tempAr > -40 ) {

                                 $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where mes = $mesV and dia = $diaV and ano = $anoV and idCidade = $idCidadeV;";
                                 $pesquisa = mysqli_query($conexao, $update);
                                 $update = " update evapoTranspiracaoTomateEstacao set validado = 0 where mes = $mesV and dia = $diaV and ano = $anoV and idCidade = $idCidadeV;";
                                 $pesquisa = mysqli_query($conexao, $update);
                          }

                } // final do esle do  if( strlen($retorno) > 0 ) {


              }// fim do while


       } // final de if ($numLinhas > 0)


    //
    // Inserir dados novos na base de dados
    //
     // Nome, IdCidade, CodigoEstacao
     // Itaberai  2259  00203E1D
     // Palmeiras de Goias  3488  00203E21
     // Hidrolandia  2037  00203E22
     // Piracanjuba  3763  00203E24
     // Silvania  4960  00203E26
     // Alexania  109  00206B1B
     $idCidade1[1] =   2259; $codigo1[1] = "00203E1D";
     $idCidade1[2] =   3488; $codigo1[2] = "00203E21";
     $idCidade1[3] =   2037; $codigo1[3] = "00203E22";
     $idCidade1[4] =   3763; $codigo1[4] = "00203E24";
     $idCidade1[5] =   4960; $codigo1[5] = "00203E26";
     $idCidade1[6] =   109; $codigo1[6] = "00206B1B";
     $numEstacoesUFG = 6; // quantidade de estacoes da ufg

     date_default_timezone_set('America/Sao_Paulo');
 
/*
     $sql = "select distinct codEstacao from evapoTranspiracaoTomateEstacao where idCidade = 2259 or idCidade = 3488 or idCidade = 2037 or idCidade = 3763 or idCidade = 4960 or idCidade = 109;";
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
              mysqli_close($conexao);
              echo "Houve erro nesta consulta!";
              echo $sql;
              exit(1);
           }

     $numLinhas = $query->num_rows;
*/


     //if ($numLinhas > 0) {


              $opt = 1; // update = 2, insert = 1
              $contLoop = 1;

              //while( $linha=$query->fetch_row() ) {
              while(  $contLoop  <= $numEstacoesUFG ) {

                //$codigo = $linha[0];
                $codigo = $codigo1[$contLoop];

                $sql = "select  max(ano) from evapoTranspiracaoTomateEstacao where codEstacao = \"$codigo\" and validado = 1;";
                $query2 = mysqli_query($conexao, $sql) ;
                if(!$query2) {


                       $num_rows = $query2->num_rows;
                       if( $num_rows > 0) {
                           $linha2 = $query2->fetch_row();
                           $ano = $linha2[0];  
                       }
                       else
                           $ano = date('Y');                
                       $sql = "select  max(mes) from evapoTranspiracaoTomateEstacao where codEstacao = \"$codigo\" and ano = $ano  and validado = 1;";
                       $query2 = mysqli_query($conexao, $sql) ;
                       if(!$query2) {

                                       $num_rows = $query2->num_rows;
                                       if( $num_rows > 0) {
                                          $linha2 = $query2->fetch_row();                                        
                                          $mes = $linha2[0];
                                       }
                                       else
                                         $mes =  1;

                                       $sql = "select  max(dia) from evapoTranspiracaoTomateEstacao where  codEstacao = \"$codigo\" and ano = $ano  and mes = $mes and validado = 1;";
                                       $query2 = mysqli_query($conexao, $sql) ;
                                       if(!$query2) {

                                            $num_rows = $query2->num_rows;
                                            if( $num_rows > 0) {
                                               $linha2 = $query2->fetch_row();
                                               $dia = $linha2[0];
                                            }
                                            else {
                                              $dia = 1;
                                            }

                                       }
                                       else {
                                              $dia = 1;
                                       }
                       } else {

                               $dia = "01";
                               $mes = "01";
                         }
                } else {


                      $dia = "01";
                      $mes = "01";
                      $ano = date('Y');
                  }

                $diaSemana = date('w');
                if( $diaSemana == 4) // 3 representa Quarta-feira, dia de vareduta anual
                  {

                      $dia = "01";
                      $mes = "01";
                      $ano = date('Y');
                  }

               // $sql = "select  distinct idCidade from evapoTranspiracaoTomateEstacao where codEstacao = \"$codigo\" ;";
 
               // $query2 = mysqli_query($conexao, $sql) ;
               // if(!$query2) {
               //      $linha2 = $query2->fetch_row();
               //      $id = $linha2[0];
               //      $idCidade = $id ;
               // }
                $idCidade = $idCidade1[$contLoop] ;
                $diaInicial = $dia.'-'.$mes.'-'.$ano;
                $data2 = new DateTime($diaInicial);

                $data2->modify('+1 day');
                $dia = $data2->format('d');
                $mes = $data2->format('m');
                $ano = $data2->format('Y');
                $data = $ano."-".$mes."-".$dia;

                while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {
 
                       
                      $sql = "select * from evapoTranspiracaoTomateEstacao where dia = $dia and mes = $mes and ano = $ano and idCidade = $idCidade;";
                      $queryZ = mysqli_query($conexao, $sql) ;
                      $numLinhasZ = $queryZ->num_rows;
                      $retorno = "";
                      $flagInsert = 1;
                      if( $numLinhasZ == 0 ) {
                            //$retorno = insereDados($codigo, $data, $opt, $id, $idCidade, $codigoEstacaoCidade, $numCidadesGoias);
                            $id = 30000; // um numero qualquer pois o insert nao depende do id. Id eh necessario para update
                            $retorno = insereDados($codigo, $data, $opt, $id, $idCidade);
                            if ( strlen( $retorno ) > 0 )  {
                               $queryInsert = mysqli_query($conexao, $retorno) ;
                               if(!$queryInsert) {

                                 $flagInsert = 0;
                               }
                             
                            }
                      }

                   if(  $flagInsert == 0  &&  $opt == 1) {

                            if( strlen($dia) == 1)
                              $diaV = "0".$dia;
                            else
                              $diaV = $dia;
                            if( strlen($mes) == 1)
                              $mesV = "0".$mes;
                            else
                              $mesV = $mes;
                            $diaNasa = $ano.$mesV.$diaV;
                            $resultadoNasa =  getvarNasa($diaNasa, $lat, $lon);
                            $varClimaticas = explode(",", $resultadoNasa);
                            $tempAr = $varClimaticas[0];
                            $eto = $varClimaticas[1];
                            if( $tempAr > -40 && $eto > -40 ) {

                                  $ins = "insert into evapoTranspiracaoTomateEstacao(dia, mes, ano, temMedia, eto, codEstacao, idCidade, validado) values($dia, $mes, $ano, $tempAr, $eto, $codigo, $idCidade, 0 );";
                                  $pesquisa = mysqli_query($conexao, $ins);

                            }
                            else
                              if( $tempAr > -40 ) {

                                  $ins = "insert into evapoTranspiracaoTomateEstacao(dia, mes, ano, temMedia, codEstacao, idCidade, validado) values($dia, $mes, $ano, $tempAr, $codigo, $idCidade, 0 );";
                                  $pesquisa = mysqli_query($conexao, $ins);
                              }




                            echo "Houve erro nesta insercao de dados!  $retorno";
                            $registro = "Houve erro nesta consulta ==>  $retorno";
                            $arquivo = "log-Insert-".$hojeDia."-".$hojeMes."-".$hojeAno.".log";
                            if ( ! ($fp = fopen($arquivo, 'w')) ) {
     
                                fprintf($fp, "%s primeira insercao -> ", $retorno);
                                fprintf($fp, "%s segunda insercao -> ", $ins);
                                fclose($fp);
                            }

                   } // final de if( strlen($retorno) > 0 )

 

                   $data2->modify('+1 day');
                   $dia = $data2->format('d');
                   $mes = $data2->format('m');
                   $ano = $data2->format('Y');
                   $data = $ano."-".$mes."-".$dia;

                }// final do while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {

                $contLoop = $contLoop + 1;

              }// fim do while( $contLoop <= $numEstacoesUFG ) {

     //} // final de if(num_linhas > 0.....

 

    mysqli_close($conexao);

  } // final da funcao getDadosUfg() {

  //getDadosUfg();

?>

