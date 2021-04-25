<?php

  function removeDadosEstacoesFisicas() {

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

        $whereEstacoes = " ( a.idCidade = 157 or a.idCidade =  306 or a.idCidade =  873 or a.idCidade =  1171 or a.idCidade =  1446 or a.idCidade =  16436 or a.idCidade =  1625 or a.idCidade =  1902 or a.idCidade =  1904 or a.idCidade =  1908 or a.idCidade =  2213 or a.idCidade =  2322 or a.idCidade =  2407 or a.idCidade =  2513 or a.idCidade =  2782 or a.idCidade =  3027 or a.idCidade =  3087 or a.idCidade =  3132 or a.idCidade =  3552 or a.idCidade =  3794 or a.idCidade = 3871 or a.idCidade =  3918 or a.idCidade =  4177 or a.idCidade =  4757 or a.idCidade =  16453 or a.idCidade =  16454 or a.idCidade =  16455 or a.idCidade =  16456 or a.idCidade =  1908 or a.idCidade =  2259 or a.idCidade =  3488 or a.idCidade =  2037 or a.idCidade =  3763 or a.idCidade =  4960 or a.idCidade =  109 or a.idCidade =  1 or a.idCidade =  217 or a.idCidade =  566 or a.idCidade =  4879 or a.idCidade =  5207 or a.idCidade =  4469 or a.idCidade =  4337 or a.idCidade =  16444 or a.idCidade =  4331 or a.idCidade =   2985 or a.idCidade =  304 ) "; 

        for($mes = 1; $mes <= 12; $mes ++) {

          $sql = " delete b.* FROM `evapoTranspiracaoTomateEstacao` a INNER JOIN `evapoTranspiracaoTomateEstacao` b ON a.`idCidade` = b.`idCidade` AND a.`ano` = b.`ano` AND a.`mes` = b.`mes` AND a.`dia` = b.`dia` AND a.`id` < b.`id` AND $whereEstacoes AND  a.`ano` = $ano AND a.`mes` = $mes AND a.`dia` > 0  AND a.`radSol` is not null WHERE a.`idCidade` > 1 AND a.`ano` > 1 and a.`mes` > 1 AND a.`dia` > 1;";

          $query = mysqli_query($conexao, $sql) ;
           if(!$query) {

              echo "\n A consulta abaixo nãfoi realizada com sucesso. \n $sql \n";
           }

        }

     }

  }

?>
