<?php
     include 'getDadosUser.php';

     // O dado relativo a nome estah em utf8 na base de dados.
     // este dado pode conter acento. Para recuperar o dado com acento,
     // basta usar o comando
     // utf8_decode(htmlspecialchars_decode($nome)).
     // $nome contem o dado com acento e que estava na base de dados.
     //
     
     $idUser = $_GET["idUser"];
     $station_name = $_GET["station_name"];
     $station_id = $_GET["station_id"];
     
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {
        exit(1);
     }

     $sql = "select * from estacoes where user_id = $idUser and nome_estacao = \"$station_name\" and id_estacao = \"$station_id\";";  
     $query = mysqli_query($conexao,$sql);

    if (!$query) {
        echo setEchoMessage("Falha na consulta da estação $station_id");
        mysqli_close($conexao);
        exit(1);
    }

     $numLinhas = $query->num_rows;
     if( $numLinhas > 0) {
        echo setEchoMessage("Já existe a estação $station_id cadastrada para o usuário logado.");
        mysqli_close($conexao);
        exit(1);
     }
    
     $sqlInsert = "insert into estacoes (user_id, nome_estacao, id_estacao) values ($idUser, \"$station_name\", \"$station_id\");";

     $query = mysqli_query($conexao, $sqlInsert);

     if(!$query){
        echo setEchoMessage("Cadastro não realizado.");
     }
     else {
        echo setEchoMessage("Estação $station_id cadastrada com sucesso.").GetEstacoes($idUser);
     }

     function setEchoMessage($message){
      return "<message>$message</message>";
     }

     mysqli_close($conexao);
?>

