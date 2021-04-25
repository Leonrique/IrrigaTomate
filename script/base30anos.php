<?php

/*

1 - fazer a consulta  a seguir e pegar os valores de idCidade ->   select id, cidade from municipios where idEstado = 9 order by id;
2 - fazer um while, para cada idCidade, para verificar se tem dados de 1/1 a 31/12 do corrente ano na tabela evapoTranspiracaoTomateEstacao  
2.1 - Para cada dia, $dia / $mes, se nãtiver o dado, fazer a consulta na base evapoTranspiracaoTomateMediaTrintaAnos para aquela data 
         $sql= "select eto, tempMax, tempMin from  evapoTranspiracaoTomateMediaTrintaAnos  where idCidade = $idCidade and ( (mes = $mes and dia = $dia )  );";
2.2. Inserir os dados, caso existam, na base evapoTranspiracaoTomateEstacao
insert into evapoTranspiracaoTomateEstacao(mes, dia, eto, ano, idCidade, eto, temMedia, validado) values(...);
Final do programa

*/

  function base30anos() {

     // Abrir base de dados para inserir um atualizar dados
     
     //include 'pathConfig.php';
     //$arquivoPath = configPath;
     //include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "Erro ao se conectar com a base de dados. Veja arquivo config.php";
            exit(1);
     }
     $sql = "select id, cidade from municipios where idEstado = 9 order by id;";
     $query = mysqli_query($conexao, $sql) ;

     if(!$query) {

         echo "\nAlgum erro aconteceu com a consulta \n$sql \n";
         mysqli_close($conexao);     
         exit(0);
     }

     $numLinhas = $query->num_rows;
     if( $numLinhas == 0) {

         echo "\nA consulta nao etornou dados. \n$sql \n";
         mysqli_close($conexao);     
         exit(0);
     }

     date_default_timezone_set('America/Sao_Paulo');
     $hojeDia = date('d');
     $hojeMes = date('m');
     $hojeAno = date('Y');
     $ano = date('Y');
  



     while($linha = $query->fetch_row()) {

        $idCidade = $linha[0];
        $mes = 1;
        $dia = 1;
        $diaInicial = $dia.'-'.$mes.'-'.$ano;
        $data2 = new DateTime($diaInicial);
        while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {

          $sql = " select dia, mes, eto, idCidade from evapoTranspiracaoTomateEstacao where dia = $dia and mes = $mes and idCidade = $idCidade;";
          $query2 = mysqli_query($conexao, $sql) ;
          if(!$query2) {

              echo "\nAlgum erro aconteceu com a consulta \n$sql \n";
              $dia = $hojeDia;
              $mes = $hojeMes;
              $ano = $hojeAno;
              break;
          }
          else {

              $numLinhas2 = $query2->num_rows;
              if( $numLinhas2 == 0 ) {

                $sql = "select eto, tempMax, tempMin from  evapoTranspiracaoTomateMediaTrintaAnos  where idCidade = $idCidade and ( (mes = $mes and dia = $dia )  );";         
                $query3 = mysqli_query($conexao, $sql) ;
                if(!$query3) {

                  echo "\nAlgum erro aconteceu com a consulta \n$sql \n";
                  $dia = $hojeDia;
                  $mes = $hojeMes;
                  $ano = $hojeAno;
                  break;
                }
                else {

                 $numLinhas3 = $query3->num_rows;
                 if( $numLinhas3 > 0) {

                   $linha2 = $query3->fetch_row();
                   $etc = $linha2[0];
                   $tmax = $linha2[1];
                   $tmin = $linha2[2];
           
                   if( (is_null($etc) || strlen($etc) == 0 ) || ( ( is_null($tmax) || strlen($tmax) == 0 ) && (is_null($tmin) || strlen($tmin) == 0 ) ) ) {

                     echo "\nAlguma variavel eh vazia ou nula -> etc = $etc   tmax = $tmax    tmin = $tmin  dia = $dia  mes = $mes  \n";
                     $dia = $hojeDia;
                     $mes = $hojeMes;
                     $ano = $hojeAno;
                     break;
                   }
                   else {

                    $etc2 = (float)$etc;
                    $tmax2 = (float)$tmax;
                    $tmin2 = (float)$tmin;
                    if(   strlen($tmax) > 0 &&  strlen($tmin) > 0 )
                       $temp = ($tmax2 + $tmin2)/2.0;
                    else
                       if(   strlen($tmax) > 0 )
                        $temp = $tmax2;
                       else
                        $temp = $tmin2;

                    // inserir os dados na evapoTranspiracaoTomateMediaTrintaAno para a base de dados  evapoTranspiracaoTomateEstacao
                    //

                        $sql = "insert into evapoTranspiracaoTomateEstacao(mes, dia, eto, ano, idCidade, temMedia, validado) values( $mes, $dia, $etc2, $ano, $idCidade, $temp, 0);";   
echo "\n$sql\n";
                        $query4 = mysqli_query($conexao, $sql) ;
                        if( ! $query4 ) {

                             echo "\nAlgum erro aconteceu com a insercao de dados \n$sql \n";
                        }
                    $data2->modify('+1 day');
                    $dia = $data2->format('d');
                    $mes = $data2->format('m');

                   }// final de else de if( (is_null($etc) || strlen($etc) == 0 ) || ( (  .....

                 }// final de if( $numLinhas3 > 0) {
                 else {

                    $data2->modify('+1 day');
                    $dia = $data2->format('d');
                    $mes = $data2->format('m');

                 } // final de else  de final de if( $numLinhas3 > 0) {


                }  // final do else de if(!$query3) {

              } // final de if( $numLinhas2 == 0 ) 
              else {

                    // Verificar se as variaveis temperatura e eto existem
                    //
                    $sql = "select id, temMedia, eto from evapoTranspiracaoTomateEstacao where idCidade = $idCidade and (eto is null or temMedia is null ) ;";
                    $queryT = mysqli_query($conexao, $sql) ;
                    $numLinhasT = $queryT->num_rows;
                    if( $numLinhasT > 0) {

                       $linha2 = $queryT->fetch_row();
                       $id = $linha2[0];
                       $temp2 = $linha2[1];
                       $etcT = $linha2[2];

                       $sql = "select eto, tempMax, tempMin from  evapoTranspiracaoTomateMediaTrintaAnos  where idCidade = $idCidade and ( (mes = $mes and dia = $dia )  );";         
                       $query3 = mysqli_query($conexao, $sql) ;
                       $linha2 = $query3->fetch_row();
                       $etc = $linha2[0];
                       $tmax = $linha2[1];
                       $tmin = $linha2[2];

                       if( (is_null($etc) || strlen($etc) == 0 ) || ( ( is_null($tmax) || strlen($tmax) == 0 ) && (is_null($tmin) || strlen($tmin) == 0 ) ) ) {

                             echo "\nAlguma variavel eh vazia ou nula -> etc = $etc   tmax = $tmax    tmin = $tmin  dia = $dia  mes = $mes  \n";
                             $dia = $hojeDia;
                             $mes = $hojeMes;
                             $ano = $hojeAno;
                      }
                      else {

                             $etc2 = (float)$etc;
                             $tmax2 = (float)$tmax;
                             $tmin2 = (float)$tmin;

                             if(   strlen($tmax) > 0 &&  strlen($tmin) > 0 )
                                 $temp = ($tmax2 + $tmin2)/2.0;
                            else
                               if(   strlen($tmax) > 0 )
                                 $temp = $tmax2;
                               else
                                 $temp = $tmin2;

                            $sql = "update evapoTranspiracaoTomateEstacao set ";
                            if( !is_numeric($temp2) &&  !is_numeric($etcT))
                              $sql = $sql . " temMedia = $temp and eto = $etc2 where id = $id;";
                            else
                              if( !is_numeric($etcT) )
                                $sql = $sql . " eto = $etc2 where id = $id;";
                              else
                                $sql = $sql . " temMedia = $temp where id = $id;"; 

                            $queryST = mysqli_query($conexao, $sql) ;
                            if( ! $queryST ) {
                             echo "\nAlgum erro aconteceu com a insercao de dados \n$sql \n";
                            }

                      }// final de else de if( (is_null($etc) || strlen($etc) == 0 ) || ( (  .....

                      $data2->modify('+1 day');
                      $dia = $data2->format('d');
                      $mes = $data2->format('m');

                    } // final de if( $numLinhasT > 0) {

              } // final de else de if( $numLinhas2 == 0 )


          } // else de if(!$query2) { 
               
        } // final de while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {



     } // final de while($linha = $query->fetch_row()) {

  }

?>
