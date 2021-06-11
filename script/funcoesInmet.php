<?php

     //include 'pathConfig.php';
     //$arquivoPath = configPath;
     //include($arquivoPath);

function curl_get_contents_data($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

 

function getDadosFaltantesInmet() {

     $conexao2 = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao2) {

            echo "Erro ao se conectar com a base de dados. Veja arquivo config.php";
            exit(1);
     }


          date_default_timezone_set('America/Sao_Paulo');
          $hojeDia = date('d');
          $hojeMes = date('m');
          $hojeAno = date('Y');
       
          $dia = "01";
          $mes = "01";
          $ano = $hojeAno; 

          $diaInicial = $dia.'-'.$mes.'-'.$ano;
          $data2 = new DateTime($diaInicial);

         $codigoGO[1] = "A024";
         $codigoGO[2] = "A013";         $codigoGO[3] = "A023";         $codigoGO[4] = "A034";
         $codigoGO[5] = "A036";         $codigoGO[6] = "A011";         $codigoGO[7] = "A029";
         $codigoGO[8] = "A022";         $codigoGO[9] = "A002";         $codigoGO[10] = "A014";
         $codigoGO[11] = "A028";         $codigoGO[12] = "A015";         $codigoGO[13] = "A035";
         $codigoGO[14] = "A016";         $codigoGO[15] = "A012";         $codigoGO[16] = "A026";
         $codigoGO[17] = "A032";         $codigoGO[18] = "A003";         $codigoGO[19] = "A027";
         $codigoGO[20] = "A033";         $codigoGO[21] = "A005";         $codigoGO[22] = "A017";
         $codigoGO[23] = "A025";         $codigoGO[24] = "A031";         $codigoGO[25] = "A042";
         $codigoGO[26] = "A046";         $codigoGO[27] = "A047";         $codigoGO[28] = "A045";

          $lat[1] = -14.1305; $lon[1] =  -47.51;          $lat[2] = -15.8955; $lon[2] =  -52.2372;
          $lat[3] = -16.9539; $lon[3] =  -51.8091;        $lat[4] = -18.1578; $lon[4] =  -47.9264;
          $lat[5] = -16.7676; $lon[5] =  -47.6131;        $lat[6] = -18.9697; $lon[6] =  -50.6289;
          $lat[7] = -17.3406; $lon[7] = -49.9295;         $lat[8] = -15.3118; $lon[8] = -49.1162;
          $lat[9] = -16.63;   $lon[9] = -49.22;           $lat[10] = -15.94; $lon[10] = -50.14;
          $lat[11] = -16.4398; $lon[11] = -51.118;        $lat[12] = -14.9522; $lon[12] = -49.5511;
          $lat[13] = -18.4093; $lon[13] = -49.2158;       $lat[14] = -17.8784; $lon[14] = -51.7204;
          $lat[15] = -16.2634; $lon[15] = -47.9665;       $lat[16] = -17.5654; $lon[16] = -52.5537;
          $lat[17] = -13.2552; $lon[17] = -46.8928;       $lat[18] = -17.7166; $lon[18] = -49.10;
          $lat[19] = -16.9625; $lon[19] = -50.4253;       $lat[20] = -17.3019; $lon[20] = -48.2768;
          $lat[21] = -13.4391; $lon[21] = -49.1503;       $lat[22] = -14.0859; $lon[22] = -46.3704;
          $lat[23] = -17.7923; $lon[23] = -50.9192;       $lat[24] = -13.2731; $lon[24] = -50.1634;
          $lat[25] = -15.5997; $lon[25] = -48.1311;       $lat[26] = -15.9352; $lon[26] = -48.1374;
          $lat[27] = -16.0122; $lon[27] = -47.5574;       $lat[28] = -15.5964; $lon[28] = -47.6258;

          $numEstacoesInmet = 28;
          $where = "( codEstacao = \"$codigoGO[1]\" ";
          for ($i = 2; $i <= $numEstacoesInmet; $i++) {

               $where =  $where . " or codEstacao = \"$codigoGO[$i]\" ";
          }
          $where =  $where . ")";

          # O select abaixo seleciona todas as estacoes. Cada estacao nova da UFG
          # deverah ser inserido na clausula where deste select.

          $sql = "select distinct dia, mes, ano, codEstacao, idCidade, id from evapoTranspiracaoTomateEstacao where  $where and ano = $ano order by codEstacao,ano, mes, dia;";

          $query = mysqli_query($conexao2, $sql) ;
          if(!$query)
           {
              echo "Houve erro nesta consulta!";
              echo $sql;
              mysqli_close($conexao2);
              exit(1);
           }

          $numLinhas = $query->num_rows;

          if ($numLinhas > 0) {

              $flag = 1;
              while( $linha=$query->fetch_row() ) {

//if( $flag == 0)
//echo "$codEstacaoAnterior $linha[3] $linha[0]   $linha[1]  $linha[2] \n";
                   if( $flag == 0 )  {
                       if( ! strcmp( trim($codEstacaoAnterior) , trim($linha[3]) ) ) {

                           $data2->modify('+1 day');
                           $dia = $data2->format('d');
                           $mes = $data2->format('m');
                           $codEstacaoAnterior = $linha[3];
                       } 
                       else {

                           $dia = "01";
                           $mes = "01";
                           $diaInicial = $dia.'-'.$mes.'-'.$ano;
                           $data2 = new DateTime($diaInicial);
                           $codEstacaoAnterior = $linha[3];
                       }
                        
                   }
                   else  {
                     $codEstacaoAnterior = $linha[3];
                     $flag = 0;
                   }
                   //
                   // Por causa de dados repetitos, eh necessario
                   // ser tolerante a este erro.
                   // O if abaixo eh para nao deixar que a varredura
                   // tenha uma data (dia,mes) maior do que a considerada no momento (dia2,mes2)
                   $dia2 = (int)$linha[0];
                   $mes2 = (int)$linha[1];
                   $dia1 = (int)$dia;
                   $mes1 = (int)$mes;
                   if( (int)$mes == (int)$mes2 ) {

                       if( (int)$dia > (int)$dia2 ) {
                   
                           $dia = $dia2;
                           $dia1 = $dia2;
                           $mes1 = (int)$mes;
                           $diaInicial = $dia.'-'.$mes.'-'.$ano;
                           $data2 = new DateTime($diaInicial);

                       }

                   }
                   else {

                             if( (int)$mes > (int)$mes2 ) {

                               $dia = $dia2;
                               $mes = $mes2;
                               $dia1 = $dia2;
                               $mes1 = $mes2;
                               $diaInicial = $dia.'-'.$mes.'-'.$ano;
                               $data2 = new DateTime($diaInicial);
                             }
                   }
                     

//echo "$dia   $mes  $linha[3] $dia2   $mes2\n";                
                   while( !($dia1 == $dia2 && $mes1 == $mes2) && !( $dia1 == $hojeDia && $mes1 == $hojeMes ))  {

                     $data = $ano."-".$mes1."-".$dia1;
                     $codigo = trim($linha[3]);
                     $id = $linha[5];
                     $idCidade = trim($linha[4]); 

                     $resultado = getvar($data, $codigo);
                     $varClimaticas = explode(",", $resultado);
                     $tempAr = (float)$varClimaticas[0];
                     $ur = (float)$varClimaticas[1];
                     $velVento = (float)$varClimaticas[2];
                     $radSolar = (float)$varClimaticas[3];
                     $eto = (float)$varClimaticas[5];
                     $umidRel = $ur;
//echo "$tempAr   $ur  $velVento  $radSolar  $eto  ---  \n";
                     if( $tempAr < -10  || $ur < 0   || $velVento < 0   || $radSolar < 0 || $eto < 0 )
                        $retorno = "";
                     else {
                                  $retorno = "insert into evapoTranspiracaoTomateEstacao(dia, mes, ano, temMedia, eto, codEstacao, idCidade, validado, ur, radSol, velVento) values($dia1, $mes1, $ano, $tempAr, $eto, \"$codigo\", $idCidade, 1, $umidRel, $radSolar, $velVento );";
//echo "insercao --> $ins \n";
                        
                     }

                     if ( strlen( $retorno ) > 10 )  {
                         $queryInsert = mysqli_query($conexao2, $retorno) ;
                         if(!$queryInsert) {

                            echo "Nao deu certo o comando ===>  $retorno\n"; 
                         }

                     }
                     else {

                        for ($u = 1; $u <= $numEstacoesInmet; $u++)
                           if( !strcmp(trim($codigoGO[$u]), trim($codigo) ) ) {
                             $latV = $lat[$u];
                             $lonV = $lon[$u];
                           }

                        if( strlen($dia1) < 2)
                             $diaV = "0".$dia1;
                        else
                             $diaV = $dia1;

                        if( strlen($mes1) < 2)
                             $mesV = "0".$mes1;
                        else
                             $mesV = $mes1;

                        $diaNasa = $ano.$mesV.$diaV;
                        $resultadoNasa =  getvarNasa2($diaNasa, $latV, $lonV);
//echo "dia -->$diaNasa   res --> $resultadoNasa\n";
                        $varClimaticas = explode(",", $resultadoNasa);
                            $tempAr2 = $varClimaticas[0];
                            $eto = $varClimaticas[1];
                            $velVento2 = $varClimaticas[2];
                            $umidRel2 = $varClimaticas[3];
                            $radSol2 = $varClimaticas[4];

                            $tar = -99.0;
                            if( ! is_null($tempAr2) &&  strlen($tempAr2) > 0 )
                               if( $tempAr2 > -10)
				 $tar = $tempAr2;
                            if( ! is_null($tempAr) &&  strlen($tempAr) > 0 )
                               if( $tempAr > -10)
				 $tar = $tempAr;
                            if($tar > -10)
                                 $tempAr = $tar;
                            
                            $ur = -99.0;
                            if( ! is_null($umidRel2) &&  strlen($umidRel2) > 0 )
                               if( $umidRel2 > -1)
				 $ur = $umidRel2;

                            if( ! is_null($umidRel) &&  strlen($umidRel) > 0 )
                               if( $umidRel > -1)
				 $ur = $umidRel;
                            if($ur > -10)
                                 $umidRel = $ur;
                            
                            $rs = -99.0;
                            if( ! is_null($radSol2) &&  strlen($radSol2) > 0 )
                               if( $radSol2 > -10)
				 $rs = $radSol2;
                            if( ! is_null($radSolar) &&  strlen($radSolar) > 0 )
                               if( $radSolar > -10)
				 $rs = $radSolar;
                            if($tar > -10)
                                 $radSol = $rs;
                            
                            $vv  = -99.0;
                            if( ! is_null($velVento2) &&  strlen($velVento2) > 0 )
                               if( $velVento2 > -1)
				 $vv = $velVento2;
                            if( ! is_null($velVento) &&  strlen($velVento) > 0 )
                               if( $velVento > -1)
				 $vv = $velVento;
                            if($vv > -1)
                                 $velVento = $vv;


                            if( $tempAr >= -40 && $radSol >= 0 && $velVento >= 0 && $umidRel >= 0 ) {

                               $eto1 = pow(10, ((7.5*$tempAr)/(237.3+$tempAr)) );
                               $eto2 = pow( ($tempAr +237.3), 2);
                               $eto=((0.408*((4098*(0.6108*$eto1))/$eto2)*$radSol*0.55*0.0864)+(0.063*900*$velVento*((0.6108*$eto1)-(((0.6108*$eto1)*$umidRel)/100)))/($tempAr+275))/(((4098*(0.6108*$eto1))/$eto2)+0.063*(1+0.34*$velVento));

                            }


        		    if( $tempAr > -40 && $eto > -40 ) {

                                  $ins = "insert into evapoTranspiracaoTomateEstacao(dia, mes, ano, temMedia, eto, codEstacao, idCidade, validado, ur, radSol, velVento) values($diaV, $mesV, $ano, $tempAr, $eto, \"$codigo\", $idCidade, 1, $umidRel, $radSol, $velVento );";
//echo "insercao --> $ins \n";
                                  $pesquisa = mysqli_query($conexao2, $ins);

                            }
//
// Terminar. Mesclar dados do inmet, q nao forem vazios
// com dados do nasapower e entao calcular o etco
//


                     }
                     //echo "O dia $dia1/$mes1/$ano, para a estacao $linha[3], nao tem registro. \n";
                     $data2->modify('+1 day');
                     $dia1 = $data2->format('d');
                     $mes1 = $data2->format('m');
                   }
                  //echo "sau do while mais interno \n"; 
              } // final de while( $linha=$query->fetch_row() ) {
          }

          mysqli_close($conexao2);

     } // final da funcao getDadosFaltantes()

function getvarNasa2($dia, $lat, $lon) {

 
          $nasa = "https://power.larc.nasa.gov/cgi-bin/v1/DataAccess.py?&request=execute&identifier=SinglePoint&parameters=T2M,PRECTOT,RH2M,WS2M,ALLSKY_SFC_SW_DWN&startDate=$dia&endDate=$dia&userCommunity=AG&tempAverage=DAILY&outputList=ASCII&lat=$lat&lon=$lon";
 
          $a = curl_get_contents_data($nasa);
          if( is_null($a) ) {

            //echo "O valor de a eh nulo ";           
            $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0";
            return($resultado);
          }
          else
             if( strlen($a) == 0 ) {

               echo "O valor de a eh vazio ";          
               $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0"; 
               return($resultado);

             }
 
          $obj = json_decode($a);
          if( is_null($obj)) {

            echo "O valor de obj eh nulo";
            $resultado = "-99.0,-99.0";
            return($resultado);
          }

 
 
          $data1 = $dia;

          if(  isset ($obj->features[0]->properties->parameter->ALLSKY_SFC_SW_DWN->$data1 ) ) {

               $radSol = $obj->features[0]->properties->parameter->ALLSKY_SFC_SW_DWN->$data1; // Em MJ/m2
               $radSol = $radSol * 1000000.0 / (24.0*60.0*60.0); // em w/m3
               if(is_null($radSol) || strlen($radSol) == 0)
                   $radSol = -99.0;
          }
          else
              $radSol = -99.0;
 
          if(  isset ($obj->features[0]->properties->parameter->WS2M->$data1 ) ){
               $velVento = $obj->features[0]->properties->parameter->WS2M->$data1;
               if(is_null($velVento) || strlen($velVento) == 0)
                   $velVento = -99.0;
          }
          else
              $velVento = -99.0;

          if(  isset ($obj->features[0]->properties->parameter->T2M->$data1 ) ) {
               $tempAr = $obj->features[0]->properties->parameter->T2M->$data1;
               if(is_null($tempAr) || strlen($tempAr) == 0)
                   $tempAr = -99.0;
          }
          else
              $tempAr = -99.0;

          if(  isset ($obj->features[0]->properties->parameter->RH2M->$data1 ) ) {
               $umidRel = $obj->features[0]->properties->parameter->RH2M->$data1;
               if(is_null($umidRel) || strlen($umidRel) == 0)
                   $umidRel = -99.0;
          }
          else
              $umidRel = -99.0;   
       
          if( $tempAr >= -40 && $radSol >= 0 && $velVento >= 0 && $umidRel >= 0 ) {

                 $eto1 = pow(10, ((7.5*$tempAr)/(237.3+$tempAr)) );
                 $eto2 = pow( ($tempAr +237.3), 2);
                 $etoX=((0.408*((4098*(0.6108*$eto1))/$eto2)*$radSol*0.55*0.0864)+(0.063*900*$velVento*((0.6108*$eto1)-(((0.6108*$eto1)*$umidRel)/100)))/($tempAr+275))/(((4098*(0.6108*$eto1))/$eto2)+0.063*(1+0.34*$velVento));

          } 
          else
             $etoX = -99.0;

          $resultado = "$tempAr,$etoX,$radSol,$velVento,$umidRel";
          //echo " <br> $resultado <br>";

          return($resultado);

} //function getvarNasa2($dia, $lat, $lon) {

function getvar($dia, $codigoEstacao) {

          $data1 = $dia;
          $data2 = $dia; 
          $estacao = $codigoEstacao;
 
          $inmet = "https://apitempo.inmet.gov.br/estacao/$data1/$data2/$estacao";

//echo $inmet."\n  ";

          $a = curl_get_contents_data($inmet);
          if( is_null($a) ) {

            //echo "O valor de a eh nulo ";           
            $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0,-99.0,-99.0,-99.0";
            return($resultado);
          }
          else
             if( strlen($a) == 0 ) {

               //echo "O valor de a eh vazio ";     
               $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0,-99.0,-99.0,-99.0";     
               return($resultado);

             }
 
          $obj = json_decode($a,true);
          if( is_null($obj)) {

            echo "O valor de obj eh nulo";
            $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0,-99.0,-99.0,-99.0"; 
            return($resultado);
          }
/*
          $a = curl_get_contents_data($inmet);
          //$json = json_encode($a);
          $obj = json_decode($a,true);
          //print_r($obj[0]."<br><br>");
*/

           //$nomeCidade = $obj[0]['DC_NOME'];
//echo $nomeCidade;

          $j = 0;
          $j_t = 0;
          $j_ur = 0;
          $j_vv = 0;
          $j_to = 0;
          $j_torv = 0;
          $j_rad = 0;
          $j_precipt = 0;
          $ur = 0;
          $velVento = 0;
          $temp = 0;
          $radSolar = 0;
          $tempOrvalho = 0;
          $precipt = 0;
          $torv = 0;
         
          
          for($i = 0; $i < 24; $i++) {

             $tempObj = $obj;
             if( $tempObj != null ) {

                $torv1 = $obj[$i]['PTO_MIN'];
                $torv2 = $obj[$i]['PTO_MAX'];
                if ( $torv1 == null && $torv2== null )
                  $torv =  $torv;
                else
                  if( $torv1 == null && $torv2 != null) {
                    $torv = $torv + $torv2;
                    $j_torv = $j_torv + 1;
                  }
                  else
                    if( $torv1 != null && $torv2 == null) {
                       $torv =  $torv + $torv1;
                       $j_torv = $j_torv + 1;
                    }
                    else {

                     $torv = $torv + ($torv1 + $torv2)/2.0;
                     $j_torv = $j_torv + 1;

                    }

                $precipt1 = $obj[$i]['CHUVA'];
                if( $precipt1 != null ) {

                       $precipt =  $precipt + $precipt1;
                       $j_precipt = $j_precipt + 1;

                }


                $ur1 = $obj[$i]['UMD_MAX'];
                $ur2 = $obj[$i]['UMD_MIN'];         
                if ( $ur1 == null && $ur2== null )
                  $ur =  $ur;
                else
                  if( $ur1 == null && $ur2 != null) {
                    $ur = $ur + $ur2;
                    $j_ur = $j_ur + 1;
                  }
                  else
                    if( $ur1 != null && $ur2 == null) {
                       $ur =  $ur + $ur1;
                       $j_ur = $j_ur + 1;
                    }
                    else {

                     $ur = $ur + ($ur1 + $ur2)/2.0;
                     $j_ur = $j_ur + 1;

                    }
 
                   //$ur = $ur + ($obj[$i]->UMD_MAX + $obj[$i]->UMD_MIN)/2.0;	
                $vv = $obj[$i]['VEN_VEL'];
                if( $vv != null) {
                   $velVento = $velVento + $obj[$i]['VEN_VEL'];
                   $j_vv = $j_vv + 1;
                }
 
                $t1 = $obj[$i]['TEM_MIN'];
                $t2 = $obj[$i]['TEM_MAX'];
                if ( $t1 != null && $t2 != null) {
                    $temp = $temp + ($obj[$i]['TEM_MIN'] + $obj[$i]['TEM_MAX'])/2.0;
                    $j_t = $j_t + 1;
                }
                else 
                  if ( $t1 != null && $t2 == null) {
                      $temp = $temp + $t1;
                      $j_t = $j_t + 1;
                  }
                  else
                    if ( $t1 == null && $t2 != null) {
                      $temp = $temp + $t2;
                      $j_t = $j_t + 1;
                    }

                if( ($ur1 != null ||  $ur2 != null)  && ( $t1 != null || $t2 == null ) ) {
                
                    $tprt = ($obj[$i]['TEM_MIN'] + $obj[$i]['TEM_MAX'] )/2.0;
                    $urt = ($obj[$i]['UMD_MAX'] + $obj[$i]['UMD_MIN'] )/2.0;
                    $temp1 = ((7.5*$tprt )/(237.3+ $tprt)); 

                    $temp1 = 6.1 * exp ( $temp1 * log(10) ); 
                    $temp1 = ($urt/100) * $temp1 ;
                    $temp1 = (237.3*( log($temp1/6.1)/log(10) ) )/( 7.5 - log($temp1 /6.1)/log(10) ); 
                    $tempOrvalho = $tempOrvalho + $temp1; 
                    $j_to = $j_to + 1;

 
                }

                $rad = $obj[$i]['RAD_GLO'];
                if( !( $rad == null || !$rad ) ) { 

                    if( $rad > 0) {
                      $radSolar = $radSolar  + $rad;
                      $j_rad = $j_rad + 1;
                    }
                }
                  
             }

          }
          if ($j_ur > 0)
             $ur = $ur / $j_ur;
          else
             $ur = -999.99;
          if ($j_vv > 0)
              $velVento = $velVento / $j_vv;
          else
             $velVento =  -999.99;
          if ($j_t > 0 )
             $temp = $temp / $j_t;
          else
             $temp = -999.99;
          if ($j_to > 0)
              $tempOrvalho = $tempOrvalho / $j_to; // temperatura de orvalho calculada
          else
              $tempOrvalho = -999.99;
          if ($j_torv > 0)
              $torv = $torv / $j_torv; // temperatura de orvalho dada diretamente pela estacao
          else
              $torv = -999.99;
          if($j_rad > 0)
             $radSolar = $radSolar*1000/(24*60*60); // KJ/m2 para w/m
          else
             $radSolar =  -999.99;
          if($j_precipt > 0)
             $precipt = $precipt/$j_precipt;
          else
             $precipt =  -999.99;

          if ($temp > -999 && $radSolar > -999 && $velVento > -999 && $ur > -999 ) {

             $eto1 = pow(10, ((7.5*$temp)/(237.3+$temp)) );
             $eto2 = pow( ($temp +237.3), 2);
             $etoX=((0.408*((4098*(0.6108*$eto1))/$eto2)*$radSolar*0.55*0.0864)+(0.063*900*$velVento*((0.6108*$eto1)-(((0.6108*$eto1)*$ur)/100)))/($temp+275))/(((4098*(0.6108*$eto1))/$eto2)+0.063*(1+0.34*$velVento));

          }
          else
            $etoX = -999.99;

          //$etoX = temperatura de evapotranspiracao. Calculada a partir de tempAr, UR, veloc, vento e radiacao solar

          //echo " <br> UR = $ur      vel vento = $velVento    temp = $temp  radiacao total = $radSolar  tempOrvalho = $tempOrvalho  torv = $torv    eto = $etoX  ";

          if( $j_to < $j_torv)
            $tempOrvalho = $torv;

          //$nomeCidade = $obj[0]['DC_NOME'];
          $nomeCidade = "00";
          $resultado = "$temp,$ur,$velVento,$radSolar,$tempOrvalho,$etoX,$nomeCidade,$precipt";
          //echo " <br> $resultado <br>";
          return($resultado);


} // final de function getvar($dia, $codigoEstacao) {

 
// insert into evapoTranspiracaoTomateEstacao(codEstacao, temMedia, dia, mes, ano, eto, idCidade) values ();

function getDadosInmet() { 

         $codigoGO[1] = "A024";
         $codigoGO[2] = "A013";         $codigoGO[3] = "A023";         $codigoGO[4] = "A034";
         $codigoGO[5] = "A036";         $codigoGO[6] = "A011";         $codigoGO[7] = "A029";
         $codigoGO[8] = "A022";         $codigoGO[9] = "A002";         $codigoGO[10] = "A014";
         $codigoGO[11] = "A028";         $codigoGO[12] = "A015";         $codigoGO[13] = "A035";
         $codigoGO[14] = "A016";         $codigoGO[15] = "A012";         $codigoGO[16] = "A026";
         $codigoGO[17] = "A032";         $codigoGO[18] = "A003";         $codigoGO[19] = "A027";
         $codigoGO[20] = "A033";         $codigoGO[21] = "A005";         $codigoGO[22] = "A017";
         $codigoGO[23] = "A025";         $codigoGO[24] = "A031";         $codigoGO[25] = "A042";    
         $codigoGO[26] = "A046";         $codigoGO[27] = "A047";         $codigoGO[28] = "A045";     

         $cidadeGO[1] = "Alto Paraiso";         $cidadeGO[2] = "Aragarcas";         $cidadeGO[3] = "Caiaponia";
         $cidadeGO[4] = "Catalao";         $cidadeGO[5] = "Cristalina";         $cidadeGO[6] = "Sao Simao";
         $cidadeGO[7] = "Edeia";         $cidadeGO[8] = "Goianesia";         $cidadeGO[9] = "Goiania";
         $cidadeGO[10] = "Goias";         $cidadeGO[11] = "Ipora";         $cidadeGO[10] = "Itapaci";
         $cidadeGO[13] = "Itumbiara";         $cidadeGO[14] = "Jatai";         $cidadeGO[15] = "Luziania";
         $cidadeGO[16] = "Mineiros";         $cidadeGO[17] = "Monte Alegre de Goias";         $cidadeGO[18] = "Morrinhos";
         $cidadeGO[19] = "Parauna";         $cidadeGO[20] = "Pires do Rio";         $cidadeGO[21] = "Porangatu";
         $cidadeGO[22] = "Posse";         $cidadeGO[23] = "Rio Verde";         $cidadeGO[24] = "Sao Miguel do Araguaia";
         $cidadeGO[25] = "Brazlandia";   $cidadeGO[26] = "Gama (Ponte Alta)";   $cidadeGO[27] = "Paranoa";
         $cidadeGO[28] = "Aguas Emendadas";       
//a056 fazenda sta monica/cristalina, goiania 002, goias 014, 

          $idGO[1] = 157;   $idGO[2] = 306;    $idGO[3] = 873;
          $idGO[4] = 1171;   $idGO[5] = 1446;    $idGO[6] = 16436;
          $idGO[7] = 1625;   $idGO[8] = 1902;    $idGO[9] = 1904;
          $idGO[10] = 1908;  $idGO[11] = 2213;   $idGO[12] = 2322;
          $idGO[13] = 2407;  $idGO[14] = 2513;   $idGO[15] = 2782;
          $idGO[16] = 3027;  $idGO[17] = 3087;   $idGO[18] = 3132;
          $idGO[19] = 3552;  $idGO[20] = 3794;   $idGO[21] = 3871;
          $idGO[22] = 3918;  $idGO[23] = 4177;   $idGO[24] = 4757;
          $idGO[25] = 16453;  $idGO[26] = 16454;   $idGO[27] = 16455;
          $idGO[28] = 16456;  

          $latitude[1] = -14.1305; $longitude[1] =  -47.51;          $latitude[2] = -15.8955; $longitude[2] =  -52.2372;	 
          $latitude[3] = -16.9539; $longitude[3] =  -51.8091;	     $latitude[4] = -18.1578; $longitude[4] =  -47.9264;	 
          $latitude[5] = -16.7676; $longitude[5] =  -47.6131;	     $latitude[6] = -18.9697; $longitude[6] =  -50.6289;	 
          $latitude[7] = -17.3406; $longitude[7] = -49.9295;	     $latitude[8] = -15.3118; $longitude[8] = -49.1162;	 
          $latitude[9] = -16.63; $longitude[9] = -49.22;	     $latitude[10] = -15.94; $longitude[10] = -50.14;	 
          $latitude[11] = -16.4398; $longitude[11] = -51.118;	     $latitude[12] = -14.9522; $longitude[12] = -49.5511;	 
          $latitude[13] = -18.4093; $longitude[13] = -49.2158;       $latitude[14] = -17.8784; $longitude[14] = -51.7204;
          $latitude[15] = -16.2634; $longitude[15] = -47.9665;       $latitude[16] = -17.5654; $longitude[16] = -52.5537;
          $latitude[17] = -13.2552; $longitude[17] = -46.8928;       $latitude[18] = -17.7166; $longitude[18] = -49.10;
          $latitude[19] = -16.9625; $longitude[19] = -50.4253;	     $latitude[20] = -17.3019; $longitude[20] = -48.2768;	  
          $latitude[21] = -13.4391; $longitude[21] = -49.1503;	     $latitude[22] = -14.0859; $longitude[22] = -46.3704;	 
          $latitude[23] = -17.7923; $longitude[23] = -50.9192;	     $latitude[24] = -13.2731; $longitude[24] = -50.1634;

          $latitude[25] = -15.5997; $longitude[25] = -48.1311;	     $latitude[26] = -15.9352; $longitude[26] = -48.1374;
          $latitude[27] = -16.0122; $longitude[27] = -47.5574;	     $latitude[28] = -15.5964; $longitude[28] = -47.6258;
  
          // $codigoGO[14] = "A756 ";  $idGO[14] = 64;
          // $idGO[1] = 10;  $codigoGO[1] = "A731"; 
          //INSERT INTO `municipios` (`latitude`, `longitude`, `sigla`, `idEstado`, `cidade`, `irrigacao`) VALUES ('-15.5997', '-48.1311', 'GO', '9', 'Brazlandia', '1');
          //INSERT INTO `municipios` (`latitude`, `longitude`, `sigla`, `idEstado`, `cidade`, `irrigacao`) VALUES ('-15.9352', '-48.1374', 'GO', '9', 'Ponte Alta', '1');
          //INSERT INTO `municipios` (`latitude`, `longitude`, `sigla`, `idEstado`, `cidade`, `irrigacao`) VALUES ('-16.0122', '-47.5574', 'GO', '9', 'Paranoa', '1');
          //INSERT INTO `municipios` (`latitude`, `longitude`, `sigla`, `idEstado`, `cidade`, `irrigacao`) VALUES ('-15.5964', '-47.6258', 'GO', '9', 'Aguas Emendadas', '1');
 
          //INSERT INTO `goiasInmet` (`latitude`, `longitude`, `tipoEstacao`, `idCidade`, `nomeCidade`) VALUES ('-15.5997', '-48.1311', 'Automatica', '16453', 'Brazlandia');
          //INSERT INTO `goiasInmet` (`latitude`, `longitude`, `tipoEstacao`, `idCidade`, `nomeCidade`) VALUES ('-15.9352', '-48.1374', 'Automatica', '16454', 'Paranoa');
          //INSERT INTO `goiasInmet` (`latitude`, `longitude`, `tipoEstacao`, `idCidade`, `nomeCidade`) VALUES ('-16.0122', '-47.5574', 'Automatica', '16455', 'Aguas Emendadas');
          //INSERT INTO `goiasInmet` (`latitude`, `longitude`, `tipoEstacao`, `idCidade`, `nomeCidade`) VALUES ('-15.5964', '-47.6258', 'Automatica', '16456', 'Aguas Emendadas');


          $numElemEstacoes = 28;
 
          // As estacoes do inmet estao em https://mapas.inmet.gov.br/

          //
          // Abrir base de dados para inserir um atualizar dados
          
           //include 'pathConfig.php';
           //$arquivoPath = configPath;
           //include($arquivoPath);

           $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
           if (!$conexao) {

            echo "\nNao consigo acessar a base dados\n";
            exit(1);

           }

           date_default_timezone_set('America/Cuiaba');
           $hoje = date('j/m/Y');
           list ($diaHoje, $mesHoje, $anoHoje) = explode("/",$hoje);
           $anoFim = $anoHoje;
           $mesFim = $mesHoje;
           $diaFim = $diaHoje;
	//echo " $anoFim   $mesFim  $diaFim  \n";
           //
          // Atualizar dados faltantes na base de dados
          //

          
          for ($i = 1; $i <=  $numElemEstacoes; $i++ ) {
 
           
                //
                // Este trecho iria economizar muito
                // Porem, por causa dos dados faltantes
                // sempre eh bom verificar os dados anuais
                // da cultura e ver se nao tem algum dado
                // da nasa e entao corrigir, caso a estacao esteja ok
                //
                $sql = "select max(ano) from evapoTranspiracaoTomateEstacao  where idCidade = $idGO[$i]  and validado = 1;"; 
                $query = mysqli_query($conexao, $sql) ;
                if(!$query) {

                   $dia = 01;
                   $mes = 01;
                   $ano = $anoHoje;

                }
                else {
                        $num_registros = $query->num_rows;
                        if($num_registros > 0) {

                            $linha=$query->fetch_row();
                            if( ! is_null($linha[0]) && is_numeric($linha[0]) )
                               $ano = $linha[0];
                            else
                               $ano = $anoHoje;

                            $sql = "select max(mes) from evapoTranspiracaoTomateEstacao  where idCidade = $idGO[$i] and ano = $ano and validado = 1;";
                            $query = mysqli_query($conexao, $sql) ;
                            if(!$query) {

                                $dia = 01;
                                $mes = 01;

                            }
                            else {

                              $num_registros = $query->num_rows;
                              if($num_registros > 0) {

                                 $linha=$query->fetch_row();
                                 $mes = $linha[0];
                                 $sql = "select max(dia) from evapoTranspiracaoTomateEstacao  where idCidade = $idGO[$i] and ano = $ano and mes = $mes and validado = 1;";
                                 $query = mysqli_query($conexao, $sql) ;
                                 if(!$query) {

                                   $dia = 01;

                                 }
                                 else {

                                   $num_registros = $query->num_rows;
                                   if($num_registros > 0) {

                                      $linha=$query->fetch_row();
                                      $dia = $linha[0];
                                                           
                                   }
                                   else {

                                     $dia = 01;

                                   }
                                 }
                            
                              }
                              else {

                                 $dia = 01;
                                 $mes = 01;

                              }
                            }
                            
                        }
                        else {

                                 $dia = 01;
                                 $mes = 01;
                                 $ano = $anoHoje;

                        }
                         
                }
                         
                $anoInicio = $ano;
                $mesInicio = $mes;
                $diaInicio = $dia;
                //$dia = 1;
                //$mes = 1;
                //$ano = 2019;

 
                $fim = 0;
                while (!$fim) {
                      

                   $novaData =  date("m-j-Y", mktime(0, 0, 0, $mes, $dia + 1, $ano));
                   list ($mes, $dia, $ano) = explode("-",$novaData);
                   if( $dia != $diaHoje || $mes != $mesHoje || $ano != $anoHoje) {

                      $dia2 = "$ano-$mes-$dia"; 
                      $codigoEstacao = $codigoGO[$i];
                      $resultado = getvar($dia2, $codigoEstacao);
//echo "$dia2, $codigoEstacao $resultado \n";
//echo "<br>---- $resultado<br>";
                      $varClimaticas = explode(",", $resultado);
                      $tempAr = $varClimaticas[0];
                      $ur = $varClimaticas[1];
                      $velVento = $varClimaticas[2];
                      $radSolar = $varClimaticas[3];
                      $tempOrvalho = $varClimaticas[4];
                      $eto = $varClimaticas[5];
                      $nomeCidade = $varClimaticas[6];
                      $precipitacao = $varClimaticas[7];
                      $idCidade = $idGO[$i] ;
                      $flagFlag = 0;

                      if( $eto > -40 && $tempAr > -40) {  // por hipotese, entende-se que a temperatura do Pantanal nunca chegara a -20 grais centigrados

                         //Verificar se existe dado no dia e atualizar
                          $sql = "select temMedia, eto, radSol, velVento, ur, validado  from evapoTranspiracaoTomateEstacao where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                          $pesquisa = mysqli_query($conexao, $sql);
                          $numItens = $pesquisa->num_rows;
                        
                          if( $numItens > 0 ) //update
                            {

                               $resCon = $pesquisa->fetch_row();

                               if( is_null($resCon[5]) )
                                    $resCon[2] = 0;

                               $update = "";
                               if( is_null($resCon[0]) || $resCon[5] == 0  )
                                   $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                               if(strlen ($update) > 5 )
                                   $pesquisa = mysqli_query($conexao, $update);

                               $update = "";
                               if( is_null( $resCon[1] ) || $resCon[5] == 0   )
                                   $update = " update evapoTranspiracaoTomateEstacao set eto = $eto where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                               if(strlen ($update) > 5 )
                                   $pesquisa = mysqli_query($conexao, $update);

                               $update = "";
                               if( is_null( $resCon[2] ) || $resCon[5] == 0   )
                                   $update = " update evapoTranspiracaoTomateEstacao set radSol = $radSolar  where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                               if(strlen ($update) > 5 )
                                   $pesquisa = mysqli_query($conexao, $update);

                               $update = "";
                               if( is_null( $resCon[3] ) || $resCon[5] == 0   )
                                   $update = " update evapoTranspiracaoTomateEstacao set velVento = $velVento  where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                               if(strlen ($update) > 5 )
                                   $pesquisa = mysqli_query($conexao, $update);

                               $update = "";
                               if( is_null( $resCon[4] ) || $resCon[5] == 0   )
                                   $update = " update evapoTranspiracaoTomateEstacao set ur = $ur  where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                               if(strlen ($update) > 5 )
                                   $pesquisa = mysqli_query($conexao, $update);

                               $update = "";
                               if( $resCon[5] == 0 )
                                   $update = " update evapoTranspiracaoTomateEstacao set validado = 1 where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                               if(strlen ($update) > 5 )
                                   $pesquisa = mysqli_query($conexao, $update);
          


  //echo " <br> -000000 update = $sql numItens = $numItens  resCon = $resCon[0]  $resCon[1]  <br>";

                                 
                            }
                          else // insert
                            {
 //echo " <br> 1111111 <br>";
                               //$sql = "insert into evapoTranspiracaoTomateEstacao(codEstacao, temMedia, dia, mes, ano, eto, idCidade) values (\"$codigoGO[$i]\", $tempAr, $dia, $mes, $ano, $eto, $idCidade);";
                               $insere = "insert into evapoTranspiracaoTomateEstacao(codEstacao, dia, mes, ano, idCidade, validado, ";
                               $valores = " values(\"$codigoGO[$i]\", $dia, $mes, $ano, $idCidade, 1, ";
                               if( $eto >= -40) {
                                   $insere = $insere ."eto, ";
                                   $valores = $valores ."$eto, ";
                               }
                               if( $tempAr >= -40) {

                                  $insere = $insere ."temMedia, ";
                                  $valores = $valores ."$tempAr, ";
                               }
 
  
                               if( $radSolar >= -40) {

                                  $insere = $insere ."radSol, ";
                                  $valores = $valores ."$radSolar, ";
                               }
                               if( $velVento >= -40) {

                                  $insere = $insere ."velVento,  ";
                                  $valores = $valores ."$velVento, ";
                               }
                               if( $tempAr >= -40) {

                                  $insere = $insere ."ur) ";
                                  $valores = $valores ."$ur);";
                               }
                               $ins = $insere.$valores;
//echo $ins."  1\n";
                               $pesquisa = mysqli_query($conexao, $ins);
                               //echo "\n $ins \n";
                               //echo "<br> $sql <br> $ins <br>";
                            }

                         //echo "<br>  nomeCidade = $nomeCidade          UR = $ur      vel vento = $velVento    temp = $tempAr  radiacao total = $radSolar  tempOrvalho = $tempOrvalho  eto = $eto   precipt = $precipitacao \n";
 
    


                      } // final de if( $eto > -40 && $tempAr > -40) { 
                      else { 
                              // Procurar valores usando o nasapower e Inmet(tempAr, caso exista)

                           if( $tempAr > -40) { // ver se existe pelo menos a temperatura do Inmet

                               //Verificar se existe dado no dia e atualizar
                               $sql = "select temMedia  from evapoTranspiracaoTomateEstacao where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                               $pesquisa = mysqli_query($conexao, $sql);
                               $numItens = $pesquisa->num_rows;
                              
                               if( $numItens > 0 ) //update
                                 {

                                          $resCon = $pesquisa->fetch_row();
                                          $update = "";
                                          if( is_null( $resCon[0] ) )
                                                   $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                          else
                                            if( $resCon[0] < -40 )
                                                $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                          if(strlen ($update) > 5 )
                                          $pesquisa = mysqli_query($conexao, $update);
                                    
                                 }
                                 else // insert
                                  {
 
                                      $insere = "insert into evapoTranspiracaoTomateEstacao(codEstacao, dia, mes, ano, idCidade,  ";
                                      $valores = " values(\"$codigoGO[$i]\", $dia, $mes, $ano, $idCidade, ";
                                      $insere = $insere ."temMedia) ";
                                      $valores = $valores ."$tempAr);";
                                      $ins = $insere.$valores;
//echo $ins."   2\n";
                                      $pesquisa = mysqli_query($conexao, $ins);
 
                                  } // final de else de if( $numItens > 0 )

                           }
                           else { // Admitindo que nao exista dado do inmet, entao tem que inserir os dados

                     
                                   if( strlen($dia) == 1 )
                                     $diaV = "0".$dia;
                                   else
                                     $diaV = $dia;
                                   if( strlen($mes) == 1 )
                                     $mesV = "0".$mes;
                                   else
                                     $mesV = $mes;
 
                                   $dia2 = "$ano$mesV$diaV"; 
                                   $lat = $latitude[$i];
                                   $lon = $longitude[$i];
                                   $resultado = getvarNasa2($dia2, $lat, $lon);
 
                                   $varClimaticas = explode(",", $resultado);
                                   $tempAr = $varClimaticas[0];
                                   $eto = $varClimaticas[1];
                                   $radSolar = $varClimaticas[2];
                                   $velVento = $varClimaticas[3];
                                   $ur = $varClimaticas[4];

                                   if( $tempAr > -40 && $eto > -40 ) {

                                      $sql = "select temMedia, eto, radSol, velVento, ur  from evapoTranspiracaoTomateEstacao where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                      $pesquisa = mysqli_query($conexao, $sql);
                                      $numItens = $pesquisa->num_rows;
                                      $update = "";
                                      $updateFlag = 0;
                                      if( $numItens > 0 ) //update
                                        {
                                
                                           $resCon = $pesquisa->fetch_row();
                                           if( is_null( $resCon[0] ) ) {
                                              $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                              $pesquisa = mysqli_query($conexao, $update);
                                              $updateFlag = 1;
                                           }
                                           if( is_null( $resCon[1] ) ) {
                                              $update = " update evapoTranspiracaoTomateEstacao set eto = $eto where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                              $pesquisa = mysqli_query($conexao, $update);
                                              $updateFlag = 1;
                                           }
                                           if( is_null( $resCon[2] ) ) {
                                              $update = " update evapoTranspiracaoTomateEstacao set radSol = $radSolar where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                              $pesquisa = mysqli_query($conexao, $update);
                                              $updateFlag = 1;
                                           }
                                           if( is_null( $resCon[3] ) ) {
                                              $update = " update evapoTranspiracaoTomateEstacao set velVento = $velVento where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                              $pesquisa = mysqli_query($conexao, $update);
                                              $updateFlag = 1;
                                           }
                                           if( is_null( $resCon[4] ) ) {
                                              $update = " update evapoTranspiracaoTomateEstacao set ur = $ur where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                              $pesquisa = mysqli_query($conexao, $update);
                                              $updateFlag = 1;
                                           }
                                           if( $updateFlag == 1) {

                                                $update = " update evapoTranspiracaoTomateEstacao set validado = 0 where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                                $pesquisa = mysqli_query($conexao, $update);

                                           }
 
                                        }
                                      else {

                                                    $insere = "insert into evapoTranspiracaoTomateEstacao(codEstacao, dia, mes, ano, idCidade, validado, ";
                                                    $valores = " values(\"$codigoGO[$i]\", $dia, $mes, $ano, $idCidade, 0, ";
                                                    $insere = $insere ."eto, ";
                                                    $valores = $valores ."$eto, ";
                                                    $insere = $insere ."radSol, ";
                                                    $valores = $valores ."$radSolar, ";
                                                    $insere = $insere ."velVento, ";
                                                    $valores = $valores ."$velVento, ";
                                                    $insere = $insere ."ur, ";
                                                    $valores = $valores ."$ur, ";
                                                    $insere = $insere ."temMedia) ";
                                                    $valores = $valores ."$tempAr);";                     
                                                    $ins = $insere.$valores;
//echo $ins."   3\n";
                                                    $pesquisa = mysqli_query($conexao, $ins);

                                      }

                                   }// final de if( $tempAr > -40 && $eto > -40 ) {
                                   else // relativo a if( $tempAr > -40 && $eto > -40 ) 
                                     if( $tempAr > -40) {

                                         $sql = "select temMedia, radSol, velVento, ur  from evapoTranspiracaoTomateEstacao where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                         $pesquisa = mysqli_query($conexao, $sql);
                                         $numItens = $pesquisa->num_rows;
                                         $update = "";
                                         $updateFlag = 0;
                                         if( $numItens > 0 ) //update
                                          {
                                
                                           $resCon = $pesquisa->fetch_row();
                                           if( is_null( $resCon[0] ) ) {
                                                $update = "update evapoTranspiracaoTomateEstacao set temMedia = $tempAr where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                                $pesquisa = mysqli_query($conexao, $update);
                                                $updateFlag = 1;
                                           }
                                           if( is_null( $resCon[1] ) ) {
                                                $update = "update evapoTranspiracaoTomateEstacao set radSol = $radSolar where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                                $pesquisa = mysqli_query($conexao, $update);
                                                $updateFlag = 1;
                                           }
                                           if( is_null( $resCon[2] ) ) {
                                                $update = "update evapoTranspiracaoTomateEstacao set velVento = $velVento where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                                $pesquisa = mysqli_query($conexao, $update);
                                                $updateFlag = 1;
                                           }
                                           if( is_null( $resCon[3] ) ) {
                                                $update = "update evapoTranspiracaoTomateEstacao set ur = $ur where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                                $pesquisa = mysqli_query($conexao, $update);
                                                $updateFlag = 1;
                                           }
                                           if( $updateFlag == 1) {

                                                $update = " update evapoTranspiracaoTomateEstacao set validado = 0 where mes = $mes and dia = $dia and ano = $ano and idCidade = $idCidade;";
                                                $pesquisa = mysqli_query($conexao, $update);

                                           }
 
                                          }
                                         else {

                                                    $insere = "insert into evapoTranspiracaoTomateEstacao(codEstacao, dia, mes, ano, idCidade, validado, ";
                                                    $valores = " values(\"$codigoGO[$i]\", $dia, $mes, $ano, $idCidade, 0, ";
                                                    $insere = $insere ."radSol, ";
                                                    $valores = $valores ."$radSolar, "; 
                                                    $insere = $insere ."velVento, ";
                                                    $valores = $valores ."$velVento, "; 
                                                    $insere = $insere ."ur, ";
                                                    $valores = $valores ."$ur, "; 
                                                    $insere = $insere ."temMedia) ";
                                                    $valores = $valores ."$tempAr);";                     
                                                    $ins = $insere.$valores;
//echo $ins."  4\n";
                                                    $pesquisa = mysqli_query($conexao, $ins);

                                         }

                                     }// final de else de if( $tempAr > -40) mais interno

                           } // final de else de if( $tempAr > -40)
                           


                      }// final de else de // final de if( $eto > -40 && $tempAr > -40) { 

     
                  } // if( $dia != $diaHoje || $mes != $mesHoje || $ano != $anoHoje) {
                  else
                    $fim = 1;
 
                } // while (!$fim) {
 
          } // for ($i = 1; $i <=  $numElemEstacoes; $i++ ) {
     
          mysqli_close($conexao);
      getDadosFaltantesInmet();
} // final da funcao  getDadosInmet() {

    //getDadosInmet();
      //getDadosFaltantes();
?>
