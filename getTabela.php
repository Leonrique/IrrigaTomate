<?php

     $idCidade = $_GET["id"];
     $dia = $_GET["dia"];
     $mes = $_GET["mes"];
     $dia1 = $_GET["dia1"];
     $mes1 = $_GET["mes1"];
/*
     $idCidade = 3;
     $dia = 1;
     $mes = 1;
     $dia1 = 10;
     $mes1 = 10;
*/
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "<busca30anos>1</busca30anos>";
            exit(1);
       }

     if( $mes < $mes1)
        $dataBusca = "((mes = $mes and dia >= $dia) or (mes = $mes1 and dia <= $dia1) or (mes > $mes and mes < $mes1) )";
     else
        $dataBusca = "(mes = $mes and dia >= $dia and dia <= $dia1)";

     $sql= "select dia, mes, eto, tempMax, tempMin from  evapoTrasnpiracaoTomate  where idCidade = $idCidade and $dataBusca order by mes,dia";
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
              mysqli_close($conexao);
              echo "<busca30anos>3</busca30anos>";
              echo $sql;
              exit(1);
           }

     $numLinhas = $query->num_rows;

     if ($numLinhas == 0)
      {
        $registro2 = '<busca30anos>0</busca30anos>';
        echo $registro2;
        mysqli_close($conexao);
        exit(1);
      }
      else {


              $registro2 = "<busca30anos>";
              $i = 1;
              while( $linha=$query->fetch_row() ) {

                  if($i == 1)
                     $registro2 = $registro2.$linha[0]."|".$linha[1]."|".$linha[2]."|".$linha[3]."|".$linha[4];
                  else
                     $registro2 = $registro2."#".$linha[0]."|".$linha[1]."|".$linha[2]."|".$linha[3]."|".$linha[4];
                  $i = 2;
              }// fim do while
             
              echo $registro2."</busca30anos>";		
              mysqli_close($conexao);

      }

?>
