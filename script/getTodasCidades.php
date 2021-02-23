<?php

          function isEstation($idCidade) {

                   if  ($idCidade == 157 || $idCidade == 306 || $idCidade == 873 || $idCidade == 1171 || $idCidade == 1446 || $idCidade == 16436 || $idCidade == 1625 || $idCidade == 1902 || $idCidade == 1904 || $idCidade ==    1908 || $idCidade ==   2213  || $idCidade ==    2322  || $idCidade ==   2407  || $idCidade ==   2513  || $idCidade ==    2782 || $idCidade ==   3027 || $idCidade ==   3087 || $idCidade ==    3132 || $idCidade ==   3552 || $idCidade ==   3794 || $idCidade ==    3871 || $idCidade ==   3918 || $idCidade ==   4177 || $idCidade ==    4757 || $idCidade ==   16453 || $idCidade ==  16454 || $idCidade ==   16455 || $idCidade ==   16456 || $idCidade ==  1908 || $idCidade ==     2259 || $idCidade ==           3488 || $idCidade ==   2037 || $idCidade ==     3763 || $idCidade ==       4960 || $idCidade ==   109 || $idCidade ==      1 || $idCidade ==217 || $idCidade ==    566 || $idCidade ==      4879 || $idCidade == 5207 || $idCidade ==   4469 || $idCidade ==     4337 || $idCidade ==16444 || $idCidade ==  4331 || $idCidade ==     2985 || $idCidade ==304 )  {

                      return(1);
                   } 
                   else {

                        return(0);
                   }
                  

          }
          // Abrir base de dados para inserir ou atualizar dados
          
           include 'pathConfig.php';
           $arquivoPath = configPath;
           include($arquivoPath);

           $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
           if (!$conexao) {

              echo "\nNao consigo acessar a base dados\n";
              exit(1);

           }
 
           // Obter todas as cidades -> idCidade, latitude, longitude

           $sql = "select id, latitude, longitude from municipios where idEstado = 9 order by id;";           
           $query = mysqli_query($conexao, $sql) ;
           if(!$query) {

              echo "\nErro! Nao foi possivel abrir a base de dados.\n";
              mysqli_close($conexao);
              exit(1);

           }

           $i = 0;
           while ( $linha=$query->fetch_row() ) {

              $i = $i + 1;
              $id[$i] = $linha[0];
              $lat[$i] = $linha[1];
              $long[$i] = $linha[2];

           }// final de while ( $linha=$query->fetch_row() ) {
               
           $numElem = $i;
           if($numElem == 0) {

              echo "\nNao existem dados das estacoes.\n";
              mysqli_close($conexao);
              exit(1);

           }           
           //
           // Criar uma matriz distancia para ver que cidade j (ateh 20 estacoes reais 
           // do conjunto inmet, simehgo e ufg, que somam 46 estacoes) é mais perto da cidade i
           //


           $estacoesReais[1] = 157;     $estacoesReais[2] = 306;      $estacoesReais[3] = 873;
           $estacoesReais[4] = 1171;    $estacoesReais[5] = 1446;     $estacoesReais[6] = 16436;
           $estacoesReais[7] = 1625;    $estacoesReais[8] = 1902;     $estacoesReais[9] = 1904;
           $estacoesReais[10] = 1908;   $estacoesReais[11] = 2213;    $estacoesReais[12] = 2322;
           $estacoesReais[13] = 2407;   $estacoesReais[14] = 2513;    $estacoesReais[15] = 2782;
           $estacoesReais[16] = 3027;   $estacoesReais[17] = 3087;    $estacoesReais[18] = 3132;
           $estacoesReais[19] = 3552;   $estacoesReais[20] = 3794;    $estacoesReais[21] = 3871;
           $estacoesReais[22] = 3918;   $estacoesReais[23] = 4177;    $estacoesReais[24] = 4757;
           $estacoesReais[25] = 16453;  $estacoesReais[26] = 16454;   $estacoesReais[27] = 16455;
           $estacoesReais[28] = 16456;  $estacoesReais[29] = 1908;    $estacoesReais[30] =  2259;
           $estacoesReais[31] = 3488;   $estacoesReais[32] = 2037;    $estacoesReais[33] =  3763;
           $estacoesReais[34] = 4960;   $estacoesReais[35] = 109;     $estacoesReais[36] =  1;
           $estacoesReais[37] = 217;    $estacoesReais[38] = 566;     $estacoesReais[39] =  4879;
           $estacoesReais[40] = 5207;   $estacoesReais[41] = 4469;    $estacoesReais[42] =  4337;
           $estacoesReais[43] = 16444;  $estacoesReais[44] = 4331;    $estacoesReais[45] =  2985;
           $estacoesReais[46] = 304;     

           $numEstacoes = 46;

           for($i = 1; $i <= $numElem; $i++) {

               $t = 0;
               for($j = 1; $j <= $numElem; $j++) {

                    $t = $t + 1;
                    $index[$t] = $j;
                    if($j != $i) {
                        $dist[$t] = sqrt(  ($lat[$j] - $lat[$i])*($lat[$j] - $lat[$i]) + ($long[$j] - $long[$i])*($long[$j] - $long[$i])  );
                    }
                    else {
                        $dist[$t] = 10000000000000000.0;
                    }

               }

               // Ordenar do menor para o maior
               //
               for($j = 1; $j < $numElem; $j++) {
 
                    for($k = 1; $k <= $numElem - $j; $k++) {
 
                       if( $dist[$k] > $dist[$k+1]) {

                           $temp = $dist[$k];
                           $dist[$k] = $dist[$k+1];
                           $dist[$k+1] = $temp;
                           $temp2 = $index[$k];
                           $index[$k] = $index[$k+1];
                           $index[$k+1] = $temp2;

                       }

                    }
                   

               }
               $where[$i] = " (";
               for($j = 1; $j <= 20; $j++) { // pegar os 20 mais proximos

                  if( $j != 1)
                     $where[$i] = $where[$i] . " or idCidade = $index[$j]";
                  else
                     $where[$i] = $where[$i] . " idCidade = $index[$j]";
                
               }
               $where[$i] = $where[$i]  . ") ";


           } // final do comando for ($i = 1; $i <= $numElem; $i++) {  
 

           // Procurar, para cada idCidade, desde 2019, qual o dia faltante e posterior
           // Insersao de dados, conforme a clausula $where levantada nos passos anteriores,
           // que contem as cidades mais proximas

           // Inicialmente, serao colocadas em ordem as estacoes reais
           //
/*

Cidades com problema

+----------+
| idCidade |
+----------+
|     3488 |
|     2213 |
|     1902 |
|     3087 |
|     4177 |
|      217 |
|      566 |
|     5207 |
|     4879 |
|      304 |
|     4337 |
|     4331 |
|     2782 |
|     3871 |
|      157 |
|     2985 |
|     4469 |
|        1 |
|    16444 |
|      306 |
|     2513 |
+----------+
21 rows in set (0,20 sec)

*/
 
           date_default_timezone_set('America/Sao_Paulo');
           $hojeDia = date('d');
           $hojeMes = date('m');
           $hojeAno = date('Y');



           for ($i = 1; $i <= $numElem; $i++) {

               $idCidade = $id[$i];
               if( isEstation($idCidade) && $idCidade == 3488) {

                 $dia = "01";
                 $mes = "01";
                 $ano = "2020";
                 $diaInicial = $dia.'-'.$mes.'-'.$ano;
                 $data2 = new DateTime($diaInicial);
                 while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {

                     $sql = "select codEstacao, temMedia, eto, idCidade, validado, radSol, velVento, ur from evapoTranspiracaoTomateEstacao where dia = $dia and mes = $mes and ano = $ano and idCidade = $idCidade ; ";
                     $query = mysqli_query($conexao, $sql) ;
                     if(!$query) {

                      ; //nao faca nada
                     }
                     else {


                        $numLinhas = $query->num_rows;
                        if( $numLinhas == 0 ){ // inserir uma nova linha

                             $sql = "select codEstacao, temMedia, eto, idCidade, validado, radSol, velVento, ur from evapoTranspiracaoTomateEstacao where dia = $dia and mes = $mes and ano = $ano and $where[$i];";
                             $query2 = mysqli_query($conexao, $sql) ;
                             $flag = 1;
                             while ( $linha=$query2->fetch_row() ) {

                               $temMedia = $linha[1];
                               $eto = $linha[2];
                               $radSol = $linha[5];
                               $velVento = $linha[6];
                               $ur = $linha[7];
                               if( $temMedia > -10 && $eto > 0 && $radSol > -10 && $velVento >= 0 && $ur > 0 && $flag == 1 ) {

                                   $sql = "insert into (codEstacao, dia, mes, ano,temMedia, eto, idCidade, validado, radSol, velVento, ur ) values ($linha[0], $dia, $mes, $ano, $temMedia, $eto, $idCidade, 1, $radSol, $velVento, $ur);";
echo $sql."\n";
/*
                                   $query3 = mysqli_query($conexao, $sql) ;
                                   if(!query3) 
                                      ;
                                   else (
                                            $flag = 0;
                                            break;
                                   }
*/
                               }


                             } // final de  while ( $linha=$query2->fetch_row() ) {
                        }
                        else { //Ver se os dados sao coerente ou se tem q atualizar algum dado

                        } // final de if( $numLinhas == 0 ){
                     } // final de if(!$query) {


                 } // final de while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {
                 $data2->modify('+1 day');
                 $dia = $data2->format('d');
                 $mes = $data2->format('m');
                 $ano = $data2->format('Y');

               } // final de if( isEstation($idCidade) ) {
               
           } 

           // Em seguida, serao colocadas em ordem as estacoes nao reais
           //


           mysqli_close($conexao);

?>
