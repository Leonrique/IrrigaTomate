<?php

  function cidadesSemEstacoes() {

      //include 'pathConfig.php';
      //$arquivoPath = configPath;
      //include($arquivoPath);

      // Estacoes do Inmet - Id dos municipios onde ficam as estacoes
      $idCidadeGoias[1] = 157; $idCidadeGoias[2] = 306; $idCidadeGoias[3] = 873;
      $idCidadeGoias[4] = 1171; $idCidadeGoias[5] = 1446; $idCidadeGoias[6] = 16436;
      $idCidadeGoias[7] = 1625; $idCidadeGoias[8] = 1902; $idCidadeGoias[9] = 1904;
      $idCidadeGoias[10] = 1908; $idCidadeGoias[11] = 2213; $idCidadeGoias[12] = 2322;
      $idCidadeGoias[13] = 2407; $idCidadeGoias[14] = 2513; $idCidadeGoias[15] = 2782;
      $idCidadeGoias[16] = 3027; $idCidadeGoias[17] = 3087; $idCidadeGoias[18] = 3132;
      $idCidadeGoias[19] = 3552; $idCidadeGoias[20] = 3794; $idCidadeGoias[21] = 3871;
      $idCidadeGoias[22] = 3918; $idCidadeGoias[23] = 4177; $idCidadeGoias[24] = 4757;
      $idCidadeGoias[25] = 16453; $idCidadeGoias[26] = 16454; $idCidadeGoias[27] = 16455;
      $idCidadeGoias[28] = 16456; $idCidadeGoias[29] = 1908;

      // Estacoes da UFG - Id dos municipios onde ficam as estacoes

      $idCidadeGoias[30] = 2259; $idCidadeGoias[31] = 3488; $idCidadeGoias[32] = 2037;
      $idCidadeGoias[33] = 3763; $idCidadeGoias[34] = 4960; $idCidadeGoias[35] = 109;

      // Estacoes SIMEHGO - Id dos municipios onde ficam as estacoes

      $idCidadeGoias[36] = 1; $idCidadeGoias[37] = 217; $idCidadeGoias[38] = 566;
      $idCidadeGoias[39] = 4879; $idCidadeGoias[40] = 5207; $idCidadeGoias[41] = 4469;
      $idCidadeGoias[42] = 4337; $idCidadeGoias[43] = 16444; $idCidadeGoias[44] = 4331;
      $idCidadeGoias[45] = 2985; $idCidadeGoias[46] = 304;

      $numEstacoes = 46;

      function isStation( $estacao) {
 
            global $numEstacoes, $idCidadeGoias;
            $flag = 0;

            for( $j = 1; $j < $numEstacoes; $j++) {

               if($idCidadeGoias[$j] == $estacao )
                  $flag = 1;
            }
            return( $flag);
     }

     function insereDadosCidadesSemEstacoes($dia, $mes, $ano) {

         global $numEstacoes, $idCidadeGoias;
         $where = "(";

         for( $j = 1; $j < $numEstacoes; $j++) {

            $where = $where . "idCidade = $idCidadeGoias[$j] or ";
         }
         $where = $where . "idCidade = $idCidadeGoias[$numEstacoes] )";

         // Abrir base de dados para inserir um atualizar dados
         $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
         if (!$conexao) {
            echo "Erro ao se conectar com a base de dados. Veja arquivo config.php";
            exit(1);
         }

         $sql = "select id, latitude, longitude, cidade from municipios where idEstado = 9 order by id;";
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

         $i = 0;
         while($linha = $query->fetch_row()) {

            $i = $i + 1;
            $idCidade[$i] = $linha[0];
            $latitude[$i] = (float)$linha[1];
            $longitude[$i] = (float)$linha[2];
            $cidade[$i] = $linha[3];
         }

         $numCidades = $i;
         $sql = " select idCidade, temMedia, eto, validado, radSol, velVento, ur, codEstacao from evapoTranspiracaoTomateEstacao ";
         $sql = $sql . " where dia = $dia and mes = $mes and ano = $ano group by idCidade;";
         // group by foi inserido para desconsiderar linhas repetidas caso existam
         //$sql = $sql . " where dia = $dia and mes = $mes and ano = $ano and $where;";

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

         $i = 0;
         while($linha = $query->fetch_row()) {

            if( isStation( $linha[0] ) ) {

 
               $j = 1;

               while ( $j <= $numCidades) {

                  if( (int)$linha[0] == $idCidade[$j]) {

                     $i = $i + 1;
                     $id[$i] = $linha[0];
                     if( $linha[1]  == null || $linha[1] == "")
                          $temMedia[$i] = -99.0;
                     else
                          $temMedia[$i] = $linha[1];
                     if( $linha[2]  == null || $linha[2] == "")
                          $eto[$i] = -99.0;
                     else
                          $eto[$i] = $linha[2];

                     $validado[$i] = $linha[3];
                     if( $linha[4]  == null || $linha[4] == "")
                          $radSol[$i] = -99.0;
                     else
                          $radSol[$i] = $linha[4];
                     if( $linha[5]  == null || $linha[5] == "")
                          $velVento[$i] = -99.0;
                     else
                          $velVento[$i] = $linha[5];

                     if( $linha[6]  == null || $linha[6] == "")
                          $ur[$i] = -99.0;
                     else
                          $ur[$i] = $linha[6];
                     $codEstacao[$i] = $linha[7];
                     $latitude2[$i] = $latitude[$j] ;
                     $longitude2[$i] = $longitude[$j];
                     $idCidade[$j] = 0;
                     $j = $numCidades + 2;

                  }

                  $j = $j + 1;
               }

            }
            else {

               $j = 1;

               while ( $j <= $numCidades) {


                  if( (int)$linha[0] == (int)$idCidade[$j]) {

                     $idCidade[$j] = 0;
                     $j = $numCidades + 2;
                  }
                  $j = $j + 1;
               }
            }

         }// final de while($linha = $query->fetch_row()) {

         $numRegistros = $i;


         //echo $numRegistros . "num reg <br>";
         for( $j = 1; $j <= $numCidades; $j++) {

            if( $idCidade[$j] > 0) {

                 // Procurar a cidade $i que seja mais perto da cidade $j
                 $i = 1;
                 $min = 10000000.0;
                 $indice = 1; // Se nada for encontrado, por default eh escolhida a cidade 1
                 while( $i <= $numRegistros ) {

                    $dist = ($longitude2[$i] - $longitude[$j]) * ($longitude2[$i] - $longitude[$j]) ;
                    $dist = $dist + ($latitude2[$i] - $latitude[$j]) * ($latitude2[$i] - $latitude[$j]);

                    if( $min > $dist) {

                       $min = $dist;
                       $indice = $i;

                    }
                    $i = $i + 1;
                 } // final de while( $i <= $numRegistros ) {

                 $i = $indice;
                 $sql = "insert into evapoTranspiracaoTomateEstacao(dia, mes, ano, idCidade, temMedia, eto, validado, radSol, velVento, ur, codEstacao ) ";
                 $sql = $sql . " values($dia, $mes, $ano, $idCidade[$j], $temMedia[$i], $eto[$i], $validado[$i], $radSol[$i], $velVento[$i], $ur[$i], \"$codEstacao[$i]\");";
 
                 $query = mysqli_query($conexao, $sql) ;
                 if(!$query) {

                    echo "\nAlgum erro aconteceu com a insercao de dados \n$sql \n";
 
                 }
            } // final de if( $idCidade[$j] > 0) {

         }// final de for( $j = 1; $j <= $numCidades; $j++) {

         mysqli_close($conexao);

      } // final da funcao insereDadosCidadesSemEstacoes


      date_default_timezone_set('America/Sao_Paulo');
      $dia = 1;
      $mes = 1;
      //$ano = 2020;
      $ano = date('Y');
      $diaInicial = $dia.'-'.$mes.'-'.$ano;
      $data2 = new DateTime($diaInicial);
      //$hojeDia = 31;
      //$hojeMes = 10;
      //$hojeAno = 2020;
      $hojeDia = date('d');
      $hojeMes = date('m');
      $hojeAno = date('Y');
      while( !( $dia == $hojeDia && $mes == $hojeMes && $ano == $hojeAno)) {

           insereDadosCidadesSemEstacoes($dia, $mes, $ano);
           $data2->modify('+1 day');
           $dia = $data2->format('d');
           $mes = $data2->format('m');
           $ano = $data2->format('Y');

      }

  }

?>
 
