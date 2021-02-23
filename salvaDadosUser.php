<?php

     $chuva = $_GET["chuva"];
     $irrigacao = $_GET["irrigacao"];

     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

              $registro2 = '<dadosIrriga>0</dadosIrriga>'; //  erro ao acessar a base de dados
              echo $registro2;
              mysqli_close($conexao);
              exit(1);

     }

        if(!isset($_COOKIE["idUser"])) {

              $registro2 = '<dadosIrriga>6</dadosIrriga>'; // Usuario com senha/user expirado ou inexistente
              echo $registro2;
              mysqli_close($conexao);
              exit(1);

        }

        $idUser = $_COOKIE["idUser"];

     date_default_timezone_set('America/Sao_Paulo'); 
     $ano = date('Y');

     $chuva = trim($chuva);
     if ( strlen($chuva) == 0) {

         $contaValoresChuva = 0;
     }
     else {

        $dadosChuva = explode("|", $chuva); 
        $contaValoresChuva = sizeof($dadosChuva);

     }
     $irrigacao = trim($irrigacao);
     if ( strlen($irrigacao) == 0) {

         $contaValoresIrrigacao = 0;
     }
     else {

         $dadosIrrigacao = explode("|", $irrigacao); 
         $contaValoresIrrigacao = sizeof($dadosIrrigacao);

     }

//echo "\ncontaValoresIrrigacao = $contaValoresIrrigacao \n";
//echo "\ncontaValoresChuva =  $contaValoresChuva \n";


     if($contaValoresChuva > 0 ) {


         for($i=0; $i < $contaValoresChuva; $i++) {

            list($idPivot, $dia, $mes, $qtdChuva) = explode(";", $dadosChuva[$i]);
            $sql = "select * from  dadosChuvaUserTomate where idDadosPivotUserTomate = $idPivot and  idUser = $idUser and  dia = $dia and mes = $mes and  ano = $ano; ";
//echo "<br>$sql<br>";
            $query = mysqli_query($conexao,$sql);
            $numLinhas = $query->num_rows;
            if($numLinhas == 0) {

               $sql = "insert into dadosChuvaUserTomate(idDadosPivotUserTomate, chuva, dia, mes, ano, idUser) values($idPivot, $qtdChuva, $dia, $mes, $ano, $idUser);"; 
//echo "<br>$sql<br>";
               $query = mysqli_query($conexao,$sql);
               if(!$query){

                    echo "<dadosIrriga>2</dadosIrriga>"; // problema ao inserir dados de chuva
                    mysqli_close($conexao);
                    exit(1);
               }

            }
            else {

               $sql = "update dadosChuvaUserTomate set  chuva = $qtdChuva where idDadosPivotUserTomate=$idPivot and idUser = $idUser and dia = $dia and mes = $mes and ano = $ano;";
//echo "<br>$sql<br>";
               $query = mysqli_query($conexao,$sql);
               if(!$query){

                    echo "<dadosIrriga>3</dadosIrriga>"; // problema ao atualizar dados de chuva
                    mysqli_close($conexao);
                    exit(1);
               }

            }
         }
     }

     if($contaValoresIrrigacao > 0 ) {


         for($i=0; $i < $contaValoresIrrigacao; $i++) {

            list($idPivot, $dia, $mes, $qtdIrrigacao) = explode(";", $dadosIrrigacao[$i]);
            $sql = "select * from  dadosIrrigacaoUserTomate where idDadosPivotUserTomate = $idPivot and dia = $dia and mes = $mes and  ano = $ano and idUser = $idUser ; ";
            $query = mysqli_query($conexao,$sql);
            $numLinhas = $query->num_rows;
            if($numLinhas == 0) {

               $sql = "insert into dadosIrrigacaoUserTomate( idDadosPivotUserTomate, irrigacao, dia, mes, ano, idUser) values($idPivot, $qtdIrrigacao, $dia, $mes, $ano, $idUser)"; 
//echo "<br>$sql<br>";
               $query = mysqli_query($conexao,$sql);
               if(!$query){

                    echo "<dadosIrriga>4</dadosIrriga>"; // problema ao inserir dados de irrigacao
                    mysqli_close($conexao);
                    exit(1);
               }

            }
            else {

               $sql = "update dadosIrrigacaoUserTomate set  irrigacao = $qtdIrrigacao where idDadosPivotUserTomate=$idPivot and dia = $dia and mes = $mes and ano = $ano and idUser = $idUser;";
//echo "<br>$sql<br>";
               $query = mysqli_query($conexao,$sql);
               if(!$query){

                    echo "<dadosIrriga>5</dadosIrriga>"; // problema ao atualizar dados de irrigacao
                    mysqli_close($conexao);
                    exit(1);
               }

            }
         }
     }
     mysqli_close($conexao);
     echo "<dadosIrriga>1</dadosIrriga>"; // ok
 
?>

