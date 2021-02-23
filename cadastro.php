<?php

     // O dado relativo a nome estah em utf8 na base de dados.
     // este dado pode conter acento. Para recuperar o dado com acento,
     // basta usar o comando
     // utf8_decode(htmlspecialchars_decode($nome)).
     // $nome contem o dado com acento e que estava na base de dados.
     //
     $user = $_GET["user"];
     $senha = $_GET["senha"];
     $nome = $_GET["nome"];
     $email = $_GET["email"];
 
 
     $pos1 = strpos($user, "=");
     $pos2 = strpos($senha, "=");
     $pos3 = strpos($user, "like");
     $pos4 = strpos($senha, "like");
     if (strlen($user) < 1 || strlen($senha) < 1 || $pos1 !== false || $pos2 !== false || $pos3 !== false || $pos4 !== false)
      {
        echo "<cadastro>3</cadastro>";// dados nao validos foram inseridos. Tente outros valores. 
         exit(1);
      }

     $pos1 = strpos($nome, "=");
     $pos2 = strpos($email, "=");
     $pos3 = strpos($nome, "like");
     $pos4 = strpos($email, "like");
     if (strlen($nome) < 1 || strlen($email) < 1 || $pos1 !== false || $pos2 !== false || $pos3 !== false || $pos4 !== false)
      {
        echo "<cadastro>3</cadastro>"; // dados nao validos foram inseridos. Tente outros valores. 
         exit(1);
      }
 
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

              $registro2 = '<cadastro>1</cadastro>'; // Nao foi possivel fazer a conexao com o servidor ou a base de dados
              echo $registro2;
              mysqli_close($conexao);
              exit(1);

     }

     $sql = "select username, senha from loginTomate where  username = \"$user\" or email = \"$email\";";  
     $query = mysqli_query($conexao,$sql);

     if(!$query)
           {
              $registro2 = '<cadastro>0</cadastro>'; // ou o select ou algum dado estranho foi passado
              echo $registro2;
              mysqli_close($conexao);
              exit(1);
           }
     $numLinhas = $query->num_rows;
     if( $numLinhas > 0) {

              $registro2 = '<cadastro>5</cadastro>'; // usuario foi cadastrado anteriormente
              echo $registro2;
              mysqli_close($conexao);
              exit(1);

     }
     $sql = "insert into loginTomate( username, senha, email, nome) values(\"$user\", \"$senha\", \"$email\", \"$nome\");";  

     $query = mysqli_query($conexao,$sql);

     if(!$query){
            
              $registro2 = '<cadastro>2</cadastro>';// ou o insert ou algum dado estranho foi passado
              echo $registro2;
 
     }
     else {
              $registro2 = "<cadastro>4";   
              echo $registro2."</cadastro>";

     }
     mysqli_close($conexao);

?>

