<?php
$idCidade = $_GET["id"];
$dia = $_GET["dia"]; // Novo
$mes = $_GET["mes"]; // Novo
$estacao = $_GET["estacao"]; // Novo


include 'pathConfig.php';
$arquivoPath = configPath;
include($arquivoPath);

$conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal);
if (!$conexao) {

   echo "<buscaDados>1</buscaDados>";
   exit(1);
}

$numLinhas = 0;

$sql = "select dia, mes, ano, temMedia, eto, radSol, velVento, ur, ifnull(m.cidade, '') nomeCidade
              from evapoTranspiracaoTomateEstacao 
              left join (select e.id_cidade, e.id_estacao 
                        from estacoes e	
                        group by e.id_cidade, e.id_estacao) e
               on e.id_estacao = evapoTranspiracaoTomateEstacao.codEstacao  
               left join municipios m on m.id = e.id_cidade
              where idCidade = $idCidade 
              and ano = (select MAX(ano) from evapoTranspiracaoTomateEstacao where idCidade = $idCidade and ((mes = $mes and dia >= $dia ) or mes > $mes)) 
              and ((dia >= $dia and mes = $mes) or mes > $mes)";

if ($estacao != "") {
   $query = mysqli_query($conexao, $sql . " and codEstacao = \"$estacao\" ");
   $numLinhas = $query->num_rows;
}

if( $numLinhas == 0) {
   $query = mysqli_query($conexao, $sql);
} 

if (!$query) {
   mysqli_close($conexao);
   echo "<buscaDados>3</buscaDados>";
   echo $sql;
   exit(1);
}

$numLinhas = $query->num_rows;

if ($numLinhas == 0) {
   $registro2 = '<buscaDados>0</buscaDados>';
   echo $registro2;
   mysqli_close($conexao);
   exit(1);
} else {
   $registro2 = "<buscaDados>";
   $i = 1;

   while ($linha = $query->fetch_row()) {

      if ($i == 1)
         $registro2 = $registro2 . $linha[0] . "|" . $linha[1] . "|" . $linha[2] . "|" . $linha[3] . "|" . $linha[4] . "|" . $linha[5] . "|" . $linha[6] . "|" . $linha[7] . "|" . $linha[8];
      else
         $registro2 = $registro2 . "#" . $linha[0] . "|" . $linha[1] . "|" . $linha[2] . "|" . $linha[3] . "|" . $linha[4] . "|" . $linha[5] . "|" . $linha[6] . "|" . $linha[7] . "|" . $linha[8];
      $i = 2;
   } // fim do while

   echo $registro2 . "</buscaDados>";
   mysqli_close($conexao);
}
