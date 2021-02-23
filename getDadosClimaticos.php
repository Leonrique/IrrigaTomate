<?php

     $idCidade = $_GET["id"];
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "<buscaDados>1</buscaDados>";
            exit(1);
       }

     $sql = "select dia, mes, ano, temMedia, eto, radSol, velVento, ur from evapoTranspiracaoTomateEstacao where idCidade = $idCidade;"; 
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
              mysqli_close($conexao);
              echo "<buscaDados>3</buscaDados>";
              echo $sql;
              exit(1);
           }

     $numLinhas = $query->num_rows;

     if ($numLinhas == 0)
      {
        $registro2 = '<buscaDados>0</buscaDados>';
        echo $registro2;
        mysqli_close($conexao);
        exit(1);
      }
      else {


              $registro2 = "<buscaDados>";
              $i = 1;
              while( $linha=$query->fetch_row() ) {

                  if($i == 1)
                     $registro2 = $registro2.$linha[0]."|".$linha[1]."|".$linha[2]."|".$linha[3]."|".$linha[4]."|".$linha[5]."|".$linha[6]."|".$linha[7];
                  else
                     $registro2 = $registro2."#".$linha[0]."|".$linha[1]."|".$linha[2]."|".$linha[3]."|".$linha[4]."|".$linha[5]."|".$linha[6]."|".$linha[7];
                  $i = 2;
              }// fim do while
             
              echo $registro2."</buscaDados>";		
              mysqli_close($conexao);

      }

?>
