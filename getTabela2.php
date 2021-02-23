<?php

     $idCidade = $_GET["id"];
     $dia = $_GET["dia"];
     $mes = $_GET["mes"];
     $ano = $_GET["ano"];
     $dia1 = $_GET["dia1"];
     $mes1 = $_GET["mes1"];
     $ano1 = $_GET["ano1"];

     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "<buscaEstacoes>1</buscaEstacoes>";
            exit(1);
       }

     if( $ano < $ano1) {

         $dataBusca = "( (mes = $mes and dia >= $dia and ano = $ano)  or (mes > $mes and ano = $ano) or ";
         $dataBusca = $dataBusca . "(mes = $mes1 and dia <= $dia1 and ano = $ano1)  or (mes < $mes1 and ano = $ano1) or ";
         $dataBusca = $dataBusca . "(ano > $ano and ano < $ano1) )";
     }
     else {

         $dataBusca = "( (mes = $mes and dia >= $dia and ano = $ano)  or (mes > $mes and mes < $mes1 and ano = $ano) or ";
         $dataBusca = $dataBusca . " (mes = $mes1 and dia <= $dia1 and ano = $ano1)   ) ";
       
     }
 
 

     $sql= "select dia, mes, ano, eto, temMedia from  evapoTranspiracaoTomateEstacao  where idCidade = $idCidade and $dataBusca order by ano,mes,dia";
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
              mysqli_close($conexao);
              echo "<buscaEstacoes>3</buscaEstacoes>";
              echo $sql;
              exit(1);
           }

     $numLinhas = $query->num_rows;

     if ($numLinhas == 0)
      {
        $registro2 = '<buscaEstacoes>0</buscaEstacoes>';
        echo $registro2;
        mysqli_close($conexao);
        exit(1);
      }
      else {


              $registro2 = "<buscaEstacoes>";
              $i = 1;
              while( $linha=$query->fetch_row() ) {

                  if($i == 1)
                     $registro2 = $registro2.$linha[0]."|".$linha[1]."|".$linha[2]."|".$linha[3]."|".$linha[4];
                  else
                     $registro2 = $registro2."#".$linha[0]."|".$linha[1]."|".$linha[2]."|".$linha[3]."|".$linha[4];
                  $i = 2;
              }// fim do while
             
              echo $registro2."</buscaEstacoes>";		
              mysqli_close($conexao);

      }

?>
