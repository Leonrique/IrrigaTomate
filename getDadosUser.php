<?php

     $id = $_GET["id"]; 
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

              $registro2 = '<id>B</id>';
              echo $registro2;
              mysqli_close($conexao);
              exit(1);

     }

     $sql = "select  idCidade, identificacao, idPivot, dataPlantio, eficiencia, laminaAplicada, tipoPlantio, tipoSolo, AreaPivot, id, idUser  from dadosPivotUserTomate where  idUser = $id;";
     $query = mysqli_query($conexao,$sql);

     if(!$query)
           {
              $registro2 = '<id>A</id>';
              echo $registro2;
              mysqli_close($conexao);
              exit(1);
           }

     $numLinhas = $query->num_rows;
 

     if ($numLinhas == 0)
      {
        $registro2 = '<id>F</id>';
        echo $registro2;

      }
     else {

             $j = 0; 
             $registro2 = "<id>"; 
             while( $result = $query->fetch_row() ) {
                $idCidade = $result[0];
                $identificacao = $result[1];
                $idPivot = $result[2];
                $dataPlantio = $result[3];
                $eficiencia = $result[4];
                $laminaAplicada = $result[5];
                $tipoPlantio = $result[6];
                $tipoSolo = $result[7];
                $areaPivot = $result[8];
                if( $j == 1) {

                   $registro2 = $registro2 . "#%&@1743@$idCidade;$identificacao;$idPivot;$dataPlantio;$eficiencia;$laminaAplicada;$tipoPlantio;$tipoSolo;$areaPivot";

                }
                else {

                   $registro2 = $registro2 . "$idCidade;$identificacao;$idPivot;$dataPlantio;$eficiencia;$laminaAplicada;$tipoPlantio;$tipoSolo;$areaPivot";
                   $j = 1;

                }

                
 
             }      
             echo "$registro2</id>";	       

     }
     mysqli_close($conexao);

?>

