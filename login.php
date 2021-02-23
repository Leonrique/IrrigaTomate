<?php

     $user = $_GET["user"];
     $senha = $_GET["senha"];
     $pos1 = strpos($user, "=");
     $pos2 = strpos($senha, "=");
     $pos3 = strpos($user, "like");
     $pos4 = strpos($senha, "like");
     if (strlen($user) < 1 || strlen($senha) < 1 || $pos1 !== false || $pos2 !== false || $pos3 !== false || $pos4 !== false)
      {
        echo "<login>D</login>";
         exit(1);
      }

 
     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

              $registro2 = '<login>B</login>';
              echo $registro2;
              mysqli_close($conexao);
              exit(1);

     }

     $sql = "select  id, username, senha from loginTomate where  senha = \"$senha\" and username = \"$user\";";
     $query = mysqli_query($conexao,$sql);

     if(!$query)
           {
              $registro2 = '<login>A</login>';
              echo $registro2;
              mysqli_close($conexao);
              exit(1);
           }

     $numLinhas = $query->num_rows;
 

     if ($numLinhas == 0)
      {
        $registro2 = '<login>F</login>';
        echo $registro2;

      }
     else {

              $result = $query->fetch_row();
              $id = $result[0];
              setcookie("userTomate",$user, time()+21600);
              setcookie("senhaTomate",$senha, time()+21600);
              setcookie("idUser",$id, time()+21600);
              $registro2 = "<login>$id";   
              echo $registro2."</login>";	              

     }
     mysqli_close($conexao);

?>

