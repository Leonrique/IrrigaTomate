<?php

     $idEstado = $_GET["id"];
$idEstado = 9;
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "<municipio>1</municipio>";
            exit(1);
       }

     $sql= "select id, cidadeHTML from  municipios  where idEstado = $idEstado and irrigacao = 1 order by cidade";
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
              mysqli_close($conexao);
              echo "<municipio>3</municipio>";
              echo $sql;
              exit(1);
           }

     $numLinhas = $query->num_rows;

 if ($numLinhas == 0)
 {
        $registro2 = '<municipio>0</municipio>';
        echo $registro2;
        mysqli_close($conexao);
        exit(1);
 }
 else {


              $registro2 = "<municipio>";
              $i = 1;
              while( $linha=$query->fetch_row() ) {

              $cidade = utf8_decode(htmlspecialchars_decode($linha[1]));
                  if($i == 1)
                     $registro2 = $registro2.$linha[0]."|".$cidade;
                  else
                     $registro2 = $registro2."#".$linha[0]."|".$cidade;
                  $i = 2;
              }// fim do while
             
              echo $registro2."</municipio>";		
              mysqli_close($conexao);

}

?>
