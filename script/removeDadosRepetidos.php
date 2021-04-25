<?php

  function removeDadosDuplicados() {

     //include 'pathConfig.php';
     //$arquivoPath = configPath;
     //include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

            echo "Erro ao se conectar com a base de dados. Veja arquivo config.php";
            exit(1);
     }

     date_default_timezone_set('America/Sao_Paulo');
     $anoCorrente = Date('Y');
     for($ano = 2021; $ano <= $anoCorrente; $ano++) {

        for($mes = 1; $mes <= 12; $mes ++) {

          $sql = " delete b.* FROM `evapoTranspiracaoTomateEstacao` a INNER JOIN `evapoTranspiracaoTomateEstacao` b ON a.`idCidade` = b.`idCidade` AND a.`ano` = b.`ano` AND a.`mes` = b.`mes` AND a.`dia` = b.`dia` AND a.`id` < b.`id` AND a.`ano` = $ano AND a.`mes` = $mes AND a.`dia` > 0  AND a.`radSol` is not null WHERE a.`idCidade` > 1 AND a.`ano` > 1 and a.`mes` > 1 AND a.`dia` > 1;";
          $query = mysqli_query($conexao, $sql) ;
           if(!$query) {

              echo "\n A consulta abaixo nãfoi realizada com sucesso. \n $sql \n";
           }

        }

     }
  }

?>
