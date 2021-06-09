<?php
     
      if(isset($_GET["id"])){
         getDadosPivos();
      }   

     function getDadosPivos(){
         $id = $_GET["id"]; 
         include 'pathConfig.php';
         $arquivoPath = configPath;
         include($arquivoPath);
   
         $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
         if (!$conexao) {
            $registro2 = '<id>B</id>';
            echo $registro2;
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

         GetEstacoes($id, 0);
     }
     
     function GetEstacoes($idUsuario, $city_id){
         if($idUsuario == 0){
            return;
         }
         
         $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
         if (!$conexao) {
            $estacoes = '<estacao>B</estacao>';
            echo $estacoes;
            exit(1);
         }

         $sql = "select * from estacoes where user_id = $idUsuario";

         if($city_id != 0){
            $sql." and id_cidade = $city_id";
         }

         $sql.";";

         $query = mysqli_query($conexao, $sql);

         if(!$query)
            {
               $estacoes = '<estacao>A</estacao>';
               echo $estacoes;
               mysqli_close($conexao);
               exit(1);
            }

         $numLinhas = $query->num_rows;
      

         if ($numLinhas == 0)
         {
            $estacoes = '<estacao>F</estacao>';
            echo $estacoes;
         }
         else {
            $j = 0; 
            $estacoes = "<estacao>"; 
            while( $result = $query->fetch_row() ) {
               $idTabela = $result[0];
               $nomeEstacao = $result[2];
               $idEstacaoFieldClimate = $result[3];
               $idCidade = $result[4];
               
               if( $j == 1) {
                  $estacoes = $estacoes . "|$idTabela;$idUsuario;$nomeEstacao;$idEstacaoFieldClimate;$idCidade";
               }
               else {
                  $estacoes = $estacoes . "$idTabela;$idUsuario;$nomeEstacao;$idEstacaoFieldClimate;$idCidade";
                  $j = 1;
               }
            }      
            
            echo "$estacoes</estacao>";	       
         }

         mysqli_close($conexao);
      };
?>

