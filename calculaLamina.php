<?php
    session_start();

    if(isset($_POST) && count($_POST)) {
       $_SESSION['post'] = $_POST; 
    }
    
    if(isset($_SESSION['post']) && count($_SESSION['post'])) {
       $_POST = $_SESSION['post']; 
    }
    
    if (!isset($_POST["cidade"]) && !isset($_SESSION['post']["cidade"])) {
      header('Location:' . 'manejo.html');
      exit(1);
    }

    $message = "";
    $idCidade = $_POST["cidade"]; // municipio escolhido
    $identificacao = (isset($_POST["newident"]) && $_POST["newident"] != "")? $_POST["newident"] : $_POST["ident"];
    $sistemaDePlantio = $_POST["optradio2"]; // 2 - convencional;  1- direto;
    $tipoSolo = $_POST["optradio1"]; // 1 - arenoso, 2 - medio, 3 - argiloso
    $data = $_POST["calendario"]; // data de plantio
    $efic = $_POST["optradio"]; // eficiencia de distribuicao de agua do pivo
    $laminaAplicada = $_POST["norma"];
    $areaPivo = $_POST["area"]; // Area total do Pivot (ha)
    $regula = $_POST["norma"];
    $estacao = (isset($_POST["estacao_select"]))? $_POST["estacao_select"] : "";

    $pieces = explode("/", $data);
    $mesInicial = (int)$pieces[1];
    $diaInicial = (int)$pieces[0];

    if(strlen($mesInicial) == 1 )
        $mes =  '0'.$mesInicial;
    else
        $mes =  $mesInicial;

    if(strlen($diaInicial) == 1 )
        $dia =  '0'.$diaInicial;
    else
        $dia = $diaInicial;
    
    date_default_timezone_set('America/Sao_Paulo');
    $hojeDia = date('d');
    $hojeMes = date('m');
    $hojeAno = date('Y');

    $diaInicial2 = $hojeDia.'-'.$hojeMes.'-'.$hojeAno;
    $data2 = new DateTime($diaInicial2);

    $data2->modify('last day of this month');
    $ontemDia = $data2->format('d');
    $ontemMes = $data2->format('m');
    $ontemAno = $data2->format('Y');

    if($efic == 1) {
      $eficiencia = 0.85;
      $eficiencia2 = 85;
    }
    else {
      $eficiencia = 0.80;
      $eficiencia2 = 80;
    }

    $mesNovo[1] = "janeiro";
    $mesNovo[2] = "fevereiro";
    $mesNovo[3] = "mar&ccedil;o";
    $mesNovo[4] = "abril";
    $mesNovo[5] = "maio";
    $mesNovo[6] = "junho";
    $mesNovo[7] = "julho";
    $mesNovo[8] = "agosto";
    $mesNovo[9] = "setembro";
    $mesNovo[10] = "outubro";
    $mesNovo[11] = "novembro";
    $mesNovo[12] = "dezembro";

    include 'pathConfig.php';
    $arquivoPath = configPath;
    include($arquivoPath);

    $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
    
    if (!$conexao) {
        $message = "Problemas de acesso &agrave; base de dados do sistema. Tente mais tarde.";
        include 'Validacao.php';
        exit(1);
  }

    // Verificar se este usuario jah cadastrou o pivot

    if (!isset($_COOKIE["idUser"])) {
      $message = "Seu username e senha expiraram ou n&atilde;o existem. Fa&ccedil;a o login novamente ou o seu cadastro.";
      include 'Validacao.php';

      mysqli_close($conexao);
      exit(1);
    }

  $user = $_COOKIE["userTomate"];
  $idUser = $_COOKIE["idUser"];
  $sql = "select * from dadosPivotUserTomate where idUser = $idUser";

  $query = mysqli_query($conexao, $sql) ;

  if(!$query) {
    $message = "Dados do pivot relacionados ao id do usu&aacute;rio ' . $user . ' n&atilde;o foram encontrados ou cont&eacute;m erros.";
    include 'Validacao.php';

    mysqli_close($conexao);
    exit(1);
  }
  else {
    $numLinhas = $query->num_rows;

    if( $numLinhas == 0 ) {
        $sql = "insert into dadosPivotUserTomate(idUser, idCidade, identificacao, idPivot,  dataPlantio,  eficiencia, laminaAplicada, tipoPlantio, tipoSolo, AreaPivot) ";
        $sql = $sql . "values($idUser, $idCidade, \"$identificacao\", 1, \"$data\", $efic, $laminaAplicada, $sistemaDePlantio, $tipoSolo, $areaPivo );";
      //  echo $sql;
        $query = mysqli_query($conexao, $sql) ;

        if(!$query) {
          $message = "Dados do pivot relacionados ao id do usu&aacute;rio ' . $user . ' n&atilde;o puderam ser inseridos na base de dados.";
          include 'Validacao.php';

          mysqli_close($conexao);
          exit(1);
        }
    }
    else {
      // Ver se este pivot nao existe, isto eh, se a identificacao
      // eh diferente
      $identificacao = trim($identificacao);
      $identificacao2 = strtoupper($identificacao);
      $sql = "select idPivot from dadosPivotUserTomate where idUser = $idUser and UPPER(identificacao) = \"$identificacao2\";";

      $query = mysqli_query($conexao, $sql) ;
      if(!$query) {
        $message = "A consulta <br>$sql<br>retornou um erro!";
        include 'Validacao.php';

        mysqli_close($conexao);
        exit(1);
      }
      else {
        $num = $query->num_rows;
        if($num == 0) {
          $sql = "select max(idPivot) from dadosPivotUserTomate where idUser = $idUser";
          $query = mysqli_query($conexao, $sql) ;
          
          if(!$query) {
            $message = "A consulta <br>$sql <br>retornou um erro!";
            include 'Validacao.php';

            mysqli_close($conexao);
            exit(1);
          }
          
          $linha=$query->fetch_row() ;
          $idPivotNew = (int)$linha[0] + 1;
          $sql = "insert into dadosPivotUserTomate(idUser, idCidade, identificacao, idPivot,  dataPlantio,  eficiencia, laminaAplicada, tipoPlantio, tipoSolo, AreaPivot) ";
          $sql = $sql . "values($idUser, $idCidade, \"$identificacao\", $idPivotNew, \"$data\", $efic, $laminaAplicada, $sistemaDePlantio, $tipoSolo, $areaPivo );";
          
          $query = mysqli_query($conexao, $sql) ;
          if(!$query) {
            $message = "Dados do pivot relacionados ao id do usu&aacute;rio $user n&atilde;o puderam ser inseridos na base de dados.";
            include 'Validacao.php';
            mysqli_close($conexao);
            exit(1);
          }
        }
        else {
          $linha=$query->fetch_row() ;
          $idPivotNew = (int)$linha[0];
          
          $sql = "update dadosPivotUserTomate 
                  set idCidade = $idCidade, identificacao = \"$identificacao\", dataPlantio = \"$data\", eficiencia = $efic, laminaAplicada = $laminaAplicada, tipoPlantio =  $sistemaDePlantio, tipoSolo = $tipoSolo, AreaPivot = $areaPivo
                  where idUser = $idUser
                  and UPPER(identificacao) = \"$identificacao2\"
                  and idPivot = $idPivotNew";

          $query = mysqli_query($conexao, $sql);
          if(!$query) {
            $message = "Dados do pivot relacionados ao id do usu&aacute;rio $user n&atilde;o puderam ser atualizados na base de dados.";
            include 'Validacao.php';
            mysqli_close($conexao);
            exit(1);
          }
        }
      }
    }
  }
           
  //
  // A partir daqui, obter os dados a partir da data de plantio ateh
  // a data de hoje e fazer os calculos.
  //

  $sql = "select cidadeHTML from municipios where id = $idCidade and idEstado = 9;"; //Estado de Goias

  $query = mysqli_query($conexao, $sql) ;
  if(!$query){
    $nomeCidade = "";
    $sigla = "";
  } else {
    $numLinhas = $query->num_rows;

    if( $numLinhas > 0 ) {
      $linha=$query->fetch_row() ;
      $nomeCidade = $linha[0];
      $sigla = "GO";
    }
    else {
      $nomeCidade = "";
      $sigla = "";
    }
  }

//
//  A partir deste trecho comecam os calculos e a montagem da planilha
//
// Existem 40 estacoes automaticas, INMET, UFG, SIMEHGO e tambéestacoes virtuais/automaticas do Agritempo e o Nasapower.
// Os dados estarao na tabela evapoTranspiracaoTomateEstacao,
// Um escript vai inserir dados nas cidades que nao tem estacoes.
// Serah atraves do Agritempo, Nasapower ou os dados com medias de
// 30 anos
//
     $numLinhas  = 0;

     $sql= "select dia, mes, temMedia, eto 
            from evapoTranspiracaoTomateEstacao  
            where idCidade = $idCidade and  (mes = $mes and dia = $dia )";
     
    if($estacao != ""){
      $query = mysqli_query($conexao, $sql." and codEstacao = \"$estacao\" ");
      $numLinhas = $query->num_rows;
    }
    
     if( $numLinhas == 0) {
      $query = mysqli_query($conexao, $sql);
     } 

     if(!$query) {
             $t = (int)$mes;
             $mesZ = $mesNovo[$t];
             $necessidadeInicial = 0;
             $eto = 0;
             $lamina = 0.00;
             $registroInicial =  '<tr><td>'.$dia.'/'.$mesNovo[(int)$mes].'</td><td>0</td><td>0</td><td>0</td><td>';
             $registroInicial = $registroInicial . '<input type = text value = 15.00 size = 10 style = "color:"green"; border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td>';
             $registroInicial = $registroInicial . '<input type = text value = 0 size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td>';
             $registroInicial = $registroInicial . '<input type = text value = "" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';            
             //$registroInicial = $registroInicial . '<input type = text value = 100.00 size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';            

             //
             // Inserir mensagem de erro, isto eh, nao tem dados
             // e encerrar o programa
             //
             // TODO
             //
     }
     else {
          $numLinhas = $query->num_rows;
          if( $numLinhas > 0) {
       
             $linha = $query->fetch_row();
             if( $sistemaDePlantio == 1)
              $eto = $linha[3]*0.45;
             else
              $eto = $linha[3]*0.90;

             $eto2 = number_format($eto, 2, '.', '');
             $lamina = $eto / $eficiencia;
             $lamina = number_format($lamina, 2, '.', '');
             $t = (int)$mes;
             $mesZ = $mesNovo[$t];
             $necessidadeInicial = $eto;
 
             $registroInicial =  '<tr><td>'.$dia.'/'.$mesNovo[(int)$mes].'</td><td>0</td><td>'.$eto2.'</td><td>--</td><td>';
             $registroInicial = $registroInicial . '<input type = text value = 15.00 size = 10 style = " color:green; border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td>';
             $registroInicial = $registroInicial . '<input type = text value = '.$lamina.' size = 10 style = " color:green; border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td>';
             $registroInicial = $registroInicial . '<input type = text value = "" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';            
             //$registroInicial = $registroInicial . '<input type = text value = 100.00 size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';            
          }
     }

     date_default_timezone_set('America/Sao_Paulo');
     $ano = date("Y");
     $novaData =  date("m-j-Y", mktime(0, 0, 0, $mes, $dia + 1, $ano));
     list ($mes, $dia, $ano) = explode("-",$novaData);
     $SemDadosParaEstacao = false;

     $numLinhas = 0;
     $orderBy = "order by mes, dia;";

     $sql= "select dia, mes, temMedia, eto 
            from evapoTranspiracaoTomateEstacao
            where idCidade = $idCidade 
            and ((mes = $mes and dia >= $dia ) or mes > $mes)
            and ano = (select MAX(ano) from evapoTranspiracaoTomateEstacao where idCidade = $idCidade and ((mes = $mes and dia >= $dia ) or mes > $mes))";
    
     if($estacao != ""){
       $query = mysqli_query($conexao, $sql." and codEstacao = \"$estacao\" ".$orderBy);
       $numLinhas = $query->num_rows;
       $SemDadosParaEstacao = $numLinhas == 0;
     } 
     
     if($numLinhas == 0){
      $query = mysqli_query($conexao, $sql." ".$orderBy) ;
     }

     if(!$query)
      {
        $message = "N&atilde;o existem dados para esta cidade!";
        include 'Validacao.php'; 

        mysqli_close($conexao);
        exit(1);
      }
 
     $numLinhas = $query->num_rows;
     
     if ($numLinhas == 0)
      {
        $message = "N&atilde;o foram encontrados dados sobre esta cidade!";
        include 'Validacao.php'; 

        mysqli_close($conexao);
        exit(1);
      }
     else {

//  Inicio do trecho do programa que faz os caldulos diarios sobre a lamina de agua
//
               if($sistemaDePlantio == 2)
                  $tipoPlantio = "Convencional";
               else
                  $tipoPlantio = "Plantio direto";

               if( $tipoSolo == 1)
                 $solo = "Arenoso";
               else
                 if( $tipoSolo == 2 )
                    $solo = "M&eacute;dio";
                 else
                    $solo = "Argiloso";  

               $mes1 = (int)$mes;
               $dia = (int)$dia;

               $percentimetro = (100.0*$regula)/7.0;
               if($percentimetro > 100.0 )
                  $percentimetro = 100.00;
               else
                  $percentimetro = number_format($percentimetro + 0.5, 0, '.', ''); // faz com que o numero tenha apenas uma casa decimal
               if($percentimetro > 100.0 )
                 $percentimetro = 100.0;

               echo '
                 <!doctype html>
                 <html>
                 <head>
                 <meta charset="utf-8">
                 <title>Manejo</title>
                 <link href="css/singlePage.css" rel="stylesheet" type="text/css">
                 <link rel="shortcut icon" type="image/x-icon" href="imagens/favicon.ico">
                 <script src="js/Chart.min.js"></script>
                 <script src="js/utils.js"></script>

                 <link rel="stylesheet" href="js/package/dist/sweetalert2.min.css">
                 <script src="js/package/dist/sweetalert2.all.min.js"></script>
                 <script src="js/package/dist/sweetalert2.min.js"></script>


                 <script>

                   var myLine;
                   var arenosoTI = [];
                   var medioTI = [];
                   var argilosoTI = [];
                   var linhas = 12;
                   var colunas = 28;
                   var m_20 = new Array(colunas);
                   var m_19 = new Array(colunas);
                   var m_18 = new Array(colunas);
                   var m_17 = new Array(colunas);
                   var m_16 = new Array(colunas);
                   var m_15 = new Array(colunas);
                   var m_14 = new Array(colunas);
                   var m_13 = new Array(colunas);
                   for (var i2 = 0; i2 < colunas; i2++) {

                    m_20[i2] = new Array(linhas);
                    m_19[i2] = new Array(linhas);
                    m_18[i2] = new Array(linhas);
                    m_17[i2] = new Array(linhas);
                    m_16[i2] = new Array(linhas);
                    m_15[i2] = new Array(linhas);
                    m_14[i2] = new Array(linhas);
                    m_13[i2] = new Array(linhas);


                   }

                   function clima(nomeCidade) {
                    var ua = navigator.userAgent.toLowerCase();
                    var isAndroid = ua.indexOf("android") > -1;
                    
                    window.document.form1.enctype = "multipart/form-data";
                    window.document.form1.method = "post";
                    
                    if(!isAndroid) {
                      window.document.form1.target = "_blank";
                    }

                    argClima = "clima2.php?idCidade='.$idCidade.'&nome=" + nomeCidade + "&dia='.$diaInicial.'&mes='.$mesInicial.'&estacao='.$estacao.'"; // mostra variaveis climaticas do ano ou dos ultimos 30 anos                    
                    window.document.form1.action = argClima;
                    //window.document.form1.action = "clima2.php?idCidade='. $idCidade.'&nome=" + nomeCidade; // mostra variaveis climaticas do ano ou dos ultimos 30 anos
                    window.document.form1.submit();
                   }

                   function formEscoamentoOnClick() {

                      document.getElementById("hrId").style.visibility = "visible";
                      document.getElementById("rotulo").style.visibility = "visible";
                      document.getElementById("idField").style.visibility = "visible";
                      document.getElementById("idLegend").style.visibility = "visible";
                      document.getElementById("idSelect").style.visibility = "visible";

                      window.location.href="#C5";
                   }

                   function formEscoamento(e) {

                       if (e.which == 13 || e.keyCode == 13){
                            window.location.href="#C5";
                       }
                   }

                   function inputKeyUp(e) {

                       if (e.which == 13 || e.keyCode == 13){
                            atualizaDados(0);
                       }
                   }
                 </script>
                 <script language="javascript" src="salvaDadosUser.js" ></script>
                 <script language="javascript" src="js/dados.js" ></script>
                      <style>

                          canvas {
                                  -moz-user-select: none;
                                  -webkit-user-select: none;
                                  -ms-user-select: none;
                                 }
                      </style>


                 </head>
                 <body onload="atualizaDados(10)">
                 <!-- Barra dos logotipos -->
                 <div id="barraLogotipos">
                   <!-- Logo irrigaFeijao  -->
                   <img src="imagens/logo-irrigaTomate.svg" class="logoIrrigaTomate" />
                 </div>
                 <div id="barraMenu">
                   <!-- barra do menu de navegacao->
                   <div id="Navegador">
                     <nav>
                       <ul>
                           <li><a href="index.html" >PLANEJAMENTO</a></li>
                           <li><a href="#" class="selecionado">MANEJO</a></li>
                           <li><a href="sobre.html">SOBRE</a></li>
                           <li><a href="orientacoes.html">ORIENTA&Ccedil;&Otilde;ES</a></li>
                           <li><a href="cadastro.html">CADASTRO</a></li>
                           <li><a href="contato.html">CONTATO</a></li>
                           <li><a href="apoio.html">APOIO</a></li>
                       </ul>
                     </nav>
                   </div>
                 </div>
                 <!-- barra da imagem de fundo com slogam -->
                 <div id="barraDestaque" class="reduzida">
                   <!-- quadro com informaçque -->
                 <!--
                   <div id="conteudoDestaque">
                     <h1> MANEJO</h1>
                     <h2>DA IRRIGA&Ccedil;&Atilde;O>    
                     <div id="apresentacao" >
                       <p>Acesso f&aacute;cil a informa&ccedil;&otilde;es para manejo pr&aacute;tico da irriga&ccedil;&atilde;o do tomateiro na sua propriedade</p>
                     </div>
                   </div>
                 -->
                 </div>
                 <!-- box do conteudo-->

                  <div id="conteudoPrincipal">
                  <!--a class="botaoPlanilha" href="javascript:atualizaDados(1)"><img src="imagens/icoPlanilha.png" /></a-->
                  <a class="botaoPlanilha" onclick="atualizaDados(1);" href="#"><img src="imagens/salvar-2.jpg" /></a>
                  <!--p style="margin: 80px 40px"><a href="javascript:history.back()" class="botao">« voltar</a></p-->
                  <p style="margin: 80px 40px"><a href="javascript:paginaManejo()" class="botao">« voltar</a></p>
                  <p>Munic&iacutepio: <span>'.$nomeCidade.' - '.$sigla.'</span></p>
                  <p>Identifica&ccedil;&atilde;o: <span>'.$identificacao.'</span> </p>
                  <p>Efici&ecirc;ncia: <span>'.$eficiencia2.' %</span></p>
                  <p>L&acirc;mina aplicada (mm) : <span>'.$laminaAplicada.'</span></p>
                  <p>Sistema de plantio : <span>'.$tipoPlantio.'</span></p>
                  <p>Tipo de Solo : <span>'.$solo.'</span></p>
                  <table class="resultado" border="0" cellpadding="0" cellspacing="0" align="center">
                   <tbody>
                   <colgroup>
                  <col style="width: 16%; background-color:#e8f0d9;">
                  <col style="width: 12%; background-color:#fff;">
                  <col style="width: 12%; background-color:#e8f0d9;">
                  <col style="width: 12%; background-color:#fff;">
                  <col style="width: 12%; background-color:#e8f0d9;">
                  <col style="width: 12%; background-color:#fff;">
                  <col style="width: 12%; background-color:#e8f0d9;">
                   </colgroup>
                     <tr>
                       <th >Data</th>
                       <th>Dias ap&oacute;s o transplantio</th>
                       <th>ETc (mm/dia)</th>
                       <th>Chuva (mm/dia)</th>
                       <th>L&acirc;mina irrigada (mm/dia)</th>
                       <th>L&acirc;mina recomendada (mm)</th>
                       <th>Regulagem do piv&ocirc; (%) </th>
                     </tr>
                     <!--tr><td>'.$dia.'/'.$mesNovo[$mes1].'</td><td>0</td><td>--</td><td>--</td><td>--</td><td>15.0</td><td>100</td></tr-->
 
               ';

 
               $total = 0;
 
               $sql = "select chuva, dia, mes, ano from dadosChuvaUserTomate where idUser = $idUser and idDadosPivotUserTomate = $idPivotNew;";
//echo "<br> chuva => $sql <br>";
               $queryChuva = mysqli_query($conexao, $sql) ;
               $quantidadeDiasChuva = 0;
               $numRegistros =  $queryChuva->num_rows;
               if($numRegistros > 0)
                 while( $linhaChuva = $queryChuva->fetch_row() ) {

                   $quantidadeDiasChuva = $quantidadeDiasChuva + 1;

                   $diaChuva[$quantidadeDiasChuva] = (int)$linhaChuva[1];
                   $mesChuva[$quantidadeDiasChuva] = (int)$linhaChuva[2];
                   $valorChuva[$quantidadeDiasChuva] = (float)$linhaChuva[0];

                 }
 
               $sql = "select irrigacao, dia, mes, ano from dadosIrrigacaoUserTomate where idUser = $idUser and  idDadosPivotUserTomate = $idPivotNew;";
//echo "<br> irriga => $sql <br>";
               $queryIrrigacao = mysqli_query($conexao, $sql) ;
               $quantidadeDiasIrrigacao = 0;
               $numRegistros =  $queryIrrigacao->num_rows;
               if($numRegistros > 0)
                 while( $linhaIrrigacao = $queryIrrigacao->fetch_row() ) {

                   $quantidadeDiasIrrigacao = $quantidadeDiasIrrigacao + 1;
                   $diaIrrigacao[$quantidadeDiasIrrigacao] = (int)$linhaIrrigacao[1];
                   $mesIrrigacao[$quantidadeDiasIrrigacao] = (int)$linhaIrrigacao[2];
                   $valorIrrigacao[$quantidadeDiasIrrigacao] = (float)$linhaIrrigacao[0];

                 }



 
               if($tipoSolo == 1) { // arenoso

                   $fase1 = 5;
                   $fase2 = 10;
                   $fase3 = 15;
                   $fase4 = 15;
               }
               else
                 if($tipoSolo == 2) { // medio

                   $fase1 = 8;
                   $fase2 = 16;
                   $fase3 = 24;
                   $fase4 = 24;
                 }
                 else
                   if($tipoSolo == 3) { // argiloso

                     $fase1 = 10;
                     $fase2 = 20;
                     $fase3 = 30;
                     $fase4 = 30;
                   }

//
//             Verificar se o dado foi postado hoje e assim imprimir apenas
//             A data, o dia, a chuva e irrigacao
//
               if( (int)$mesInicial == (int)$hojeMes && (int)$diaInicial == (int)$hojeDia ) {

                   $dataNova = "$dia/$mes";
                   echo '<tr><td>'.$dataNova.'</td><td>1</td><td>--</td><td>--</td><td>--</td><td>--</td><td>--</td></tr>';
 
               }
 
//
//             Este while faz o preenchimento da tabela
//      
//

               $i = 0;
               $totalElementos = 0;
               $necessidadeIrrigacao = 0;
               $necessidadeIrrigacaoOntem =  $necessidadeInicial;
               $total = 0;
               $flag = 1;
               $flag2 = 1;
               $acumuloTermico = 0;
               $valorFase1 = 0;
               $valorFase2 = 0;
               $valorFase3 = 0;
               $valorFase4 = 0;
               $flagValorFase1 = 0;
               $flagValorFase3 = 0;

               $dataFase1 = "";
               $dataFase2 = "";
               $dataFase3 = "";
               $dataFase4 = "";
               $k1 = (1.10 - 0.65)/(423.9 - 91.0);
               $k2 = (1.05 - 0.50)/(423.9 - 91.0);                  
               $k3 = (1.10 - 0.35)/(1256.5 - 965.4);
               $k4 = (1.05 - 0.35)/(1256.5 - 965.4);

               echo $registroInicial;
               while($linha=$query->fetch_row() ) {


                   $dataDia = $linha[0];
                   $dataMes = $linha[1];
                   $i = $i + 1;

                   if(strlen($dataMes) == 1 )
                     $dataMes2 =  '0'.$dataMes;
                   else
                     $dataMes2 =  $dataMes;
 
                   if(strlen($dataDia) == 1 )
                     $dataDia2 =  '0'.$dataDia;
                   else
                     $dataDia2 =  $dataDia;
 
                   $tempAr =  (float)$linha[2];
                   $eto = (float)$linha[3];

/*--------------------inicialmente era assim o calculo de kc ----------------

acumulo termico <= 91 -> fase/estákc = 0.9 (PC) ou 0.45 (PD)
91 < acumulo té 423,9  -> fase 2, kc varia de 0,65-1,1 (PC) ou v (PD)
423,9 < acumulo té 965,4  -> fase 3, kc = 1.1 (PC) ou 1,05 (PD)
965,4 < acumulo té 1256,5  -> fase 4a, kc varia de 1,1-0,35 (PC) ou 1,05-0,35 (PD)
1256,5 < acumulo té 1441,5  -> fase 4b, kc  0,35 (PD) ou (PC)

                   if( $acumuloTermico <= 55.0 ){
                       
                        $kc = 0.90;
                        $fase = $fase1;
                   }
                   else
                     if( $acumuloTermico <= 110.0 ){

                        $kc = 0.80;
                        $fase = $fase1;
                     }
                     else
                       if( $acumuloTermico <= 380.0 ) {

                             if($flag == 1 ) {
                                $kc = 0.65;
                                $flag = 0;
                             }
                             else
                               $kc = $kc + $grauDia*$k1;

                             $fase = $fase2;
                       }
                       else
                         if( $acumuloTermico <= 1020.0 ) {

                             $kc = 1.10;
                             $fase = $fase3;
                         }    
                         else {

                              $fase = $fase4;
                              if( $acumuloTermico <= 1400.0 )
                                 $kc = $kc - $grauDia*$k2;
                         }                    


=---------------------------------------------------------------------------*/
/*--------------------Agora, o calculo de kc  ficou assim----------------

acumulo termico <= 91 -> fase/estákc = 0.9 (PC) ou 0.45 (PD)
91 < acumulo té 423,9  -> fase 2, kc varia de 0,65-1,1 (PC) ou 0,5-1,05 (PD)
423,9 < acumulo té 965,4  -> fase 3, kc = 1.1 (PC) ou 1,05 (PD)
965,4 < acumulo té 1256,5  -> fase 4a, kc varia de 1,1-0,35 (PC) ou 1,05-0,35 (PD)
1256,5 < acumulo té 1385,3  -> fase 4b, kc  0,35 (PD) ou (PC)
 
$sistemaDePlantio ==>1- direto;  2 - convencional;  
-------------------------------------------------------------------------*/


                   $grauDia = (float)$tempAr - 10.0;
                   $acumuloTermico = $acumuloTermico + $grauDia;
                   if( $acumuloTermico <= 91.0 ){
                       
                        if( $sistemaDePlantio == 1)
                            $kc = 0.45;
                        else
                            $kc = 0.90;
                        $fase = $fase1;
                        if($flagValorFase1 == 0 ) {
                           $valorFase1  = $i;
                           $flagValorFase1 = 1;
                        }
                   }
                   else
                       if( $acumuloTermico <= 423.9 && $sistemaDePlantio == 1) {

                             if($flag == 1 ) {
                                $kc = 0.50;
                                $flag = 0;
                                $valorFase2 = $i;
                             }
                             else
                               $kc = $kc + $grauDia*$k2;

                             $fase = $fase2;
                       }
                       else
                         if( $acumuloTermico <= 423.9 && $sistemaDePlantio == 2) {

                             if($flag == 1 ) {
                                $kc = 0.65;
                                $flag = 0;
                                $valorFase2 = $i;
                             }
                             else
                               $kc = $kc + $grauDia*$k1;

                             $fase = $fase2;
                         }
                         else
                           if( $acumuloTermico <= 965.4 ) {

                               if($sistemaDePlantio == 1) {

                                  $kc = 1.05;
 
                               }
                               else {

                                  $kc = 1.10;

                               }                                
                               $fase = $fase3;
                               if( $flagValorFase3 == 0 ) {
             
                                   $flagValorFase3 = 1;
                                   $valorFase3  = $i;
                               }

                           }    
                         else
                           if( $acumuloTermico <= 1256.5 ) {


                             
                              if( $sistemaDePlantio == 1) {

                                    if($flag2 == 1 ) {

                                      $kc = 1.05;
                                      $flag2 = 0;
                                      $valorFase4  = $i;

                                    }
                                    else
                                      $kc = $kc - $grauDia*$k4;

                                    $fase = $fase4;

                              }
                              else {

                                    if($flag2 == 1 ) {

                                      $kc = 1.10;
                                      $flag2 = 0;
                                      $valorFase4  = $i;

                                    }
                                    else
                                      $kc = $kc - $grauDia*$k3;

                                    $fase = $fase4;
                              }



                           }
                           else {

                               if( $acumuloTermico <= 1379.3 ) {
 
                                        $kc = 0.35; //Nao importa o sistema de plantio
                                   
                               }
                              $fase = $fase4;
 
                           }                    

                   $diasAposTransp[$i] = $i;
                   $diaTabela[$i] = $dataDia2;
                   $mesTabela[$i] = $dataMes;

                   //if($kc < 0.35  || $acumuloTermico > 1379.3) { // Criterio de parada do while
                   if($acumuloTermico > 1379.3) { // Criterio de parada do while

                     $totalElementos = $i - 1;
                     break;

                   }

                   $etc = $kc * $eto;
                   $etc2 = number_format($etc, 2, '.', ''); // faz com que o numero tenha apenas uma casa decimal
                   $etcTabela[$i] = $etc2;
                   $faseTabela[$i] = $fase;
                   $dataNova = $dataDia2.'/'.$mesNovo[$dataMes];            
       
                   $qtdeChuva = 0;

                   for($k = 1; $k <= $quantidadeDiasChuva; $k++) {

                      if( (int)$dataDia2 == (int)$diaChuva[$k] && (int)$dataMes == (int)$mesChuva[$k]) {

                         $qtdeChuva = $valorChuva[$k];
                         break;
                      }

                   }

                   $qtdeIrrigacao = 0;
                   for($k = 1; $k <= $quantidadeDiasIrrigacao; $k++) {

                      if( (int)$dataDia2 == (int)$diaIrrigacao[$k] && (int)$dataMes == (int)$mesIrrigacao[$k]) {

                         $qtdeIrrigacao = $valorIrrigacao[$k];
                         break;
                      }

                   }
                   $total = $total + $qtdeIrrigacao;
                   if($qtdeChuva >= 4.99) // Se a chuva for maior ou igual a 5
                       $necessidadeIrrigacao = $etc + $necessidadeIrrigacaoOntem - $qtdeChuva - $qtdeIrrigacao * $eficiencia;
                   else
                       $necessidadeIrrigacao = $etc + $necessidadeIrrigacaoOntem - $qtdeIrrigacao * $eficiencia; // desprezar a quantidade de chuva


                   $laminaRecomendada = $necessidadeIrrigacao / $eficiencia;
               //    if ( $laminaAplicada > $laminaRecomendada )
                //      $laminaRecomendada = $laminaAplicada;
                   if($necessidadeIrrigacao < 0)
                     $necessidadeIrrigacao = 0;

                   if($laminaRecomendada > 0)
                       $regulagemPivot = 100.0 * $laminaAplicada / $laminaRecomendada;
                   else
                       $regulagemPivot = -1;

                   if( $regulagemPivot > 100 )
                       $regulagemPivot = 100.0;
                   //if($regulagemPivot > 99.9)
                    //   $regulagemPivot = -1;

                   $regulagemPivot2 = number_format($regulagemPivot, 1, '.', '');
                   $laminaRecomendada2 = number_format($laminaRecomendada, 1, '.', '');

                   if( $laminaRecomendada < $fase )
                       $color = "green";
                   else
                       $color = "red";

                   $idChuva = "chuva".$i;
                   $idIrrigacao = "irriga".$i;
                   $idLamina = "lamina".$i;
                   $idRegulagem = "regula".$i;

                   $contaDiasAposTransplantio = $i ;

                   if($valorFase1  != $i && $valorFase2  != $i && $valorFase3 != $i && $valorFase4 != $i ) {
 
                     if($regulagemPivot2 >= 0)
                         echo '<tr><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$dataNova.'</td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)">'.$contaDiasAposTransplantio.'</td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = '.$regulagemPivot2.' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';
                     else
                         echo '<tr><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$dataNova.'</td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)">'.$contaDiasAposTransplantio.'</td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = "" id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';


                   }
                   else
                     if( $valorFase1 == $i ) {

                         $valorFase1 = 0;
                         $dataFase1 = $dataNova;

                         if($regulagemPivot2 >= 0)
                              echo  '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C1">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="primeiraFase()" style="background-color:lightgreen" ><a href="#C2">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = '.$regulagemPivot2.' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';
                         else
                              echo  '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C1">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="primeiraFase()" style="background-color:lightgreen" ><a href="#C2">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = ""  id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';

/*
                         $msg =  '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C1">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="primeiraFase()" style="background-color:lightgreen" ><a href="#C2">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = ';


                         if( $regulagemPivot2 >= 100.0)
                          $msg = $msg. " ";
                         else
                          $msg = $msg. $regulagemPivot2;
 
                         $msg = $msg. ' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';

                         echo $msg;
*/
                     }
                     else
                       if( $valorFase2 == $i ) {

                         $valorFase2 = 0;
                         $dataFase2 = $dataNova;
                         if($regulagemPivot2 >= 0)
                             echo '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C2">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="segundaFase()" style="background-color:lightgreen" ><a href="#C3">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = '.$regulagemPivot2.' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';
                         else
                             echo '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C2">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="segundaFase()" style="background-color:lightgreen" ><a href="#C3">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = "" id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';

/*
                         $msg =  '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C2">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="segundaFase()" style="background-color:lightgreen" ><a href="#C3">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = ';

                         if( $regulagemPivot2 >= 100.0)
                          $msg = $msg. " ";
                         else
                          $msg = $msg. $regulagemPivot2;

                         $msg = $msg. ' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';

                         echo $msg;
*/

                       }
                       else
                         if($valorFase3 == $i ) {

                            $valorFase3 = 0;
                            $dataFase3 = $dataNova;
                            if($regulagemPivot2 >= 0)
                                echo '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C3">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="terceiraFase()" style="background-color:lightgreen" ><a href="#C4">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = '.$regulagemPivot2.' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';
                            else
                                echo '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C3">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="terceiraFase()" style="background-color:lightgreen" ><a href="#C4">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = "" id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';

/*
                            $msg =  '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C3">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="terceiraFase()" style="background-color:lightgreen" ><a href="#C4">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = ';


                                 if( $regulagemPivot2 >= 100.0)
                                   $msg = $msg. " ";
                                 else
                                 $msg = $msg. $regulagemPivot2;

                                 $msg = $msg. ' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';

                                 echo $msg;
*/
                         }
                         else
                           if( $valorFase4 == $i ) {


                                $valorFase4 = 0;
                                $dataFase4 = $dataNova;
                                if($regulagemPivot2 >= 0)
                                   echo '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C4">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="quartaFase()" style="background-color:lightgreen" ><a href="#C4">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = '.$regulagemPivot2.' id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';
                                else
                                   echo '<tr><td   style="background-color:lightgreen" onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ><a href="#C4">'.$dataNova.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  onmouseover="quartaFase()" style="background-color:lightgreen" ><a href="#C4">'.$contaDiasAposTransplantio.'</a></td><td   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" >'.$etc2.'</td><td><input type = text value ='.$qtdeChuva.' style="font-size: 16px;"  maxlength="8" size = 8 id = "'.$idChuva.'"   onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)" ></td><td><input type = text value ='.$qtdeIrrigacao.' maxlength="8" style="font-size: 16px;" size = 8 id = "'.$idIrrigacao.'"  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"   ></td><td style="color:'.$color.'; " ><input type = text value = '.$laminaRecomendada2.' id = '.$idLamina.'   size = 10 style = "color:'.$color.'; border: none transparent; background: transparent; outline: none;  margin-left:5px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td><td><input type = text value = "" id = "'.$idRegulagem.'" size = 10 style = "border: none transparent; background: transparent; outline: none;  padding-left:9px; font-size: 16px;" readonly  onclick="atualizaDados(0)" onblur="atualizaDados(5)"   onkeyup="inputKeyUp(event)"  ></td></tr>';


                           }

 
 
                   $totalElementos = $i;
                   
                   if($ontemDia == $dataDia &&  $ontemMes == $dataMes) {
                     break;
                   }

                   $necessidadeIrrigacaoOntem = $necessidadeIrrigacao;
                   

 
               } // final do while ($linha....






               echo '

                       <tr><td colspan="3"><th>Total</th><th><input type=text id="resultadoFinal" style="width: 100px; font-weight: bold; border: none transparent; background: transparent; outline: none;   font-size: 16px; color:white"  ></th><th style="background-color:\'#669900\'; color:\'white\'; border: 3px solid white;" onblur="this.style.backgroundColor=\'#669900\'; this.style.color=\'white\'; this.style.border=\'2px solid white\'"  onMouseOver="this.style.backgroundColor=\'white\' ; this.style.color=\'#669900\'; this.style.border=\' 2px solid #669900\'; msgEscoamento();"  onclick="formEscoamentoOnClick()"  onkeyup="formEscoamento(event); " >Escoamento de &aacute;gua no solo</th><td colspan="1" style="background-color:#e8f0d9;"></td></tr>
                    </tbody>
                    </table>
                    <center>
                    <div id="msgEscoamento"><br></div>
                    <div style="width:820px;">
                       <br>
                       <canvas id="canvas" style = "background-color:white; "></canvas>
                    </div>
                    <p>
                      *Profundidade efetiva de ra&iacute;zes: fase 1 (12,5), fase 2 (25) e fases 3 e 4 (37,5 cm).<br>
                      **Fator de disponibilidade de &aacute;gua no solo de 40% (f=0,4).<br>
                      ***ARENOSO (Areia Franca, 10 a 15% de argila, 1,6 g/cm3, Ucc=18,4%, Upmp=8,1%, Ucr&iacute;t.=14,3%).<br>
                         &nbsp;&nbsp;&nbsp;&nbsp;M&Eacute;DIO (Franco, 15 a 30% de argila, 1,4 g/cm3, Ucc=31,1%, Upmp=14,2%, Ucr&iacute;t.=24,4%).<br>
                         &nbsp;&nbsp;&nbsp;&nbsp;ARGILOSO (Argilosa, 40 a 60% de argila, 1,3 g/cm3, Ucc=40,2%, Upmp=19,2%, Ucr&iacute;t.=31,9%).<br>
                    </p>
                    </center>

                      <!--p style="margin: 80px 40px"><a href="javascript:paginaManejo()" class="botao">« voltar</a></p--->
                      <p style="margin: 80px 40px"><form name = form1 id = form1 style="margin-left:40px" ><a href="javascript:paginaManejo()" class="botao"  >« voltar</a><a href="javascript:clima(\''.$nomeCidade.'\')" class="botao"  style="margin-left:20px">« clima</a></form></p>
                      <br>

                      <p> <span> IMPORTANTE!! </span> </p>
                      <p style="color:purple"><a name="C1"> <b><i><span>'.$dataFase1.'.</span> Falta de &aacute;gua, principalmente nos 3 primeiros dias ap&oacute;s o transplantio, causa baixo pegamento das mudas (morte de plantas), e excesso de &aacute;gua pode causar maior incid&ecirc;ncia de doen&ccedil;as.</i></b></a></p>
                      <p><a name="C2"> <b><i><span>'.$dataFase2.'.</span> Falta moderada de &aacute;gua, na fase de crescimento das plantas, pode favorecer o enraizamento, e o excesso de &aacute;gua favorece a incid&ecirc;ncia de doen&ccedil;as e desperd&iacute;cio de parte do adubo aplicado (principalmente nitrog&ecirc;nio e pot&aacute;ssio).</i></b></a></p>
                      <p style="color:purple"><a name="C3"> <b><i><span>'.$dataFase3.'.</span> Falta de &aacute;gua na fase de flores e frutos causa queda de flores, deixa os frutos pequenos, e pode causar fundo preto nos frutos. O excesso de &aacute;gua favorece doen&ccedil;as e desperd&iacute;cio de adubo.</i></b></a></p>
                      <p><a name="C4"> <b><i><span>'.$dataFase4.'.</span>  Excesso de &aacute;gua na matura&ccedil;&atilde;o dos frutos prejudica a qualidade destes para a ind&uacute;stria, reduzindo colora&ccedil;&atilde;o, acidez, e brix, al&eacute;m de aumentar o n&uacute;mero de frutos podres na colheita.<br> Nas condi&ccedil;&otilde;es goianas, maior rendimento de polpa pode ser obtido paralisando as irriga&ccedil;&otilde;es quando a cultura
                       apresentar entre 40 e 50% de frutos maduros (10 a 15 dias da colheita). J&aacute; a m&aacute;xima produtividade de frutos
                       somente &eacute; atingida irrigando-se at&eacute; mais pr&oacute;ximo &agrave; colheita, com 60 a 90% de frutos maduros (5 a 10 dias antes
                       da colheita).</i></b></a>
                      </p>
                      <br>

                      <hr style="visibility:hidden" id="hrId">
                       <p id="rotulo"  ><a name="C5"><center>
                           <fieldset id="idField" style=" width:80%; height:100%; visibility:hidden"  >        
                              <legend id = "idLegend" style="font-size:18px;color:green; font-weight:bold; visibility:hidden" ><span onMouseOver="msgDiametroMolhado()" >Escolha o valor do di&acirc;metro molhado (m) </span></legend>
                                 <div id="dmtMolhado"> </div>
                                 <br>
                                 <p><select onchange = "calculaEscoamentoAgua(this )" id="idSelect" style=" font-size:16px; padding-left:2px; margin-left:2px; visibility:hidden" ><option value="0" selected>  Valores para o di&acirc;metro (m) </option><option value="1">13</option><option value="2">14</option><option value="3">15</option><option value="4">16</option><option value="5">17</option><option value="6">18</option><option value="7">19</option><option value="9">20</option> </select><p>
                                 <p id="rotulo2" style="visibility:hidden"><span style="font-size:16px; color:black" >A l&acirc;mina recomendada m&aacute;xima permitida &eacute;  </span> <b id="lamMax" style="color:blue" ></b></p>
                                 <p id="rotulo3" style="visibility:hidden"><span style="font-size:16px; color:black" >A regulagem do percentimetro, correspondente <br>&agrave; l&acirc;mina recomendada m&aacute;xima permitida, &eacute;  </span> <b id="regLamMax" style="color:blue" ></b></p>

                           </fieldset>    
                      </center></a></p>                

 
                   </div>

                   <script>

                     var diaAposTransp = [];
                     var diaTabela = [];
                     var mesTabela = [];
                     var etcTabela = [];
                     var chuvaTabela = [];
                     var irrigacaoTabela = [];
                     var laminaTabela = [];
                     var regulagemTabela = [];
                     var fase = [];
                     var mesNovo = [];
                     var chuvaZ = "";
                     var irrigacaoZ = "";
                     var chuvaW;
                     var irrigacaoW;
                     var laminaAplicada = '.$laminaAplicada.';

                   function  msgDiametroMolhado() {

                     msg = "<b>Escolha aqui o di&acirc;metro molhado dos aspersores do &uacute;ltimo v&atilde;o do seu piv&ocirc;. </b>";
                     document.getElementById("dmtMolhado").style.display = "inline";
                     document.getElementById("dmtMolhado").style.color = "black";
                     document.getElementById("dmtMolhado").style.backgroundColor = "#dff0d8";
                     document.getElementById("dmtMolhado").style.borderColor = "#d6e9c6";
                     document.getElementById("dmtMolhado").innerHTML = msg;
                     setTimeout( function() { document.getElementById("dmtMolhado").style.display="none" } , 5000 );

                   }

                   function  msgEscoamento() {

                     msg = "<b>Clique aqui para calcular a l&acirc;mina m&aacute;xima sem risco de escoamento de &aacute;gua no solo.</b>";
                     document.getElementById("msgEscoamento").style.display = "inline";
                     document.getElementById("msgEscoamento").style.color = "black";
                     document.getElementById("msgEscoamento").style.backgroundColor = "#dff0d8";
                     document.getElementById("msgEscoamento").style.borderColor = "#d6e9c6";
                     document.getElementById("msgEscoamento").innerHTML = msg;
                     setTimeout( function() { document.getElementById("msgEscoamento").style.display="none" } , 5000 );

                   }
                   function verificaMudanca(numElem, opt) {
   
                          chuvaW = "";
                          irrigacaoW = "";
 
                          //
                          // Se os primeiros valores de chuva, como amostra, nao estao ainda disponiveis, entao retornar zero
                          //
                          if (sessionStorage.getItem("chuva1") === null && sessionStorage.getItem("chuva2") === null && sessionStorage.getItem("chuva3") === null  )
                             return(0);

                          contaChuva = 0;
                          contaIrrigacao = 0;
                          for(i=1; i<= numElem; i++) {
                           
                             if( opt == 1) {
                                 j = "chuva" + i;
                                 k = "irriga" + i;
                             }
                             else {
                                 j = "chuvaInicial" + i;
                                 k = "irrigaInicial" + i;

                             }

                             if( !(chuvaTabela[i] == sessionStorage.getItem(j)) ) {

                                    if( contaChuva == 0) {
                                      chuvaW = idPivot + ";" + diaTabela[i] + ";" + mesTabela[i] + ";" + chuvaTabela[i];
                                      contaChuva = 1;
                                    }
                                    else
                                      chuvaW = chuvaW + "|" + idPivot + ";" + diaTabela[i] + ";" + mesTabela[i] + ";" + chuvaTabela[i];
                                   

                             }

                             if( !(irrigacaoTabela[i] == sessionStorage.getItem(k)) ) {


                                    if( contaIrrigacao == 0) {
                                      irrigacaoW = idPivot + ";" + diaTabela[i] + ";" + mesTabela[i] + ";" + irrigacaoTabela[i];
                                      contaIrrigacao = 1;
                                    }
                                    else
                                      irrigacaoW = irrigacaoW + "|" + idPivot + ";" + diaTabela[i] + ";" + mesTabela[i] + ";" + irrigacaoTabela[i];

                             }

                                   
                          } // for(i=1; i<= numElem; i++) {
       
                          if( contaIrrigacao == 1 || contaChuva == 1)
                               return(1);
                          else
                               return(0);
                               

                   }// final da funcao verificaMudanca()

                   function atualizaDados(idBaixarPlanilha) {
   
                       totalElementos = 0;
                       necessidadeIrrigacao = 0;
                       necessidadeIrrigacaoOntem = '.$necessidadeInicial.';
                       eficiencia = '.$eficiencia.';
                       idPivot = '.$idPivotNew.';
                       total = 15.00;
                       mesNovo[1] = "janeiro";
                       mesNovo[2] = "fevereiro";
                       mesNovo[3] = "mar&ccedil;o";
                       mesNovo[4] = "abril";
                       mesNovo[5] = "maio";
                       mesNovo[6] = "junho";
                       mesNovo[7] = "julho";
                       mesNovo[8] = "agosto";
                       mesNovo[9] = "setembro";
                       mesNovo[10] = "outubro";
                       mesNovo[11] = "novembro";
                       mesNovo[12] = "dezembro";
                       flagCor = 1;
                       contaValoresChuva = 0;
                       contaValoresIrrigacao = 0;
                       esgotamentoAguaSolo = "";
                       esgotamentoAguaSolo2 = "";
                       fasesPlantio = "";
                       menorValor = 1000;
                       valorTerra = "";
                       
                       

                   ';

                for($j = 1; $j <= $totalElementos; $j++) {
               
                     echo "\n                               diaAposTransp[".$j."] = ".$diasAposTransp[$j].";  diaTabela[".$j."] = ".$diaTabela[$j].";  mesTabela[".$j."] = ".$mesTabela[$j].";  etcTabela[".$j."] = ". $etcTabela[$j].";";
                }


                for($j = 1; $j <= $totalElementos; $j++) {
               
                     $idChuva = "chuva".$j;
                     $idIrrigacao = "irriga".$j;
                     $idLamina = "lamina".$j;
                     $idRegulagem = "regula".$j;
                     echo "\n                               chuvaTabela[".$j."] =  parseFloat( document.getElementById(\"".$idChuva."\").value.replace(/[,]/ig, \".\") ) ;  irrigacaoTabela[".$j."] = parseFloat( document.getElementById(\"".$idIrrigacao."\").value.replace(/[,]/ig, \".\") ) ;   fase[".$j."] =  $faseTabela[$j];";  
                 }


               echo '
 
                          //
                          // Verificar se existem dados nao numericos para chuva ou irrigacao
                          //
                          for(i = 1; i <='.$totalElementos.'; i++) {

                             if(isNaN(chuvaTabela[i]) ||  isNaN(irrigacaoTabela[i]) ) {

                               if( idBaixarPlanilha == 0 ||  idBaixarPlanilha == 1 ) { // Quando o user clicar em alguma celula ou teclar enter, ou salvar geral,
                                                                                       // o sistema verifica se o dado eh numerico
                                   saidaHtmlX = "<p style=\"color:black; text-align: justify;\">Existe dado n&atilde;o num&eacute;rico passado para o c&aacute;lculo. Pode ser c&eacute;lula vazia. Corrija e tente novamente.</p>";
                                   Swal.fire({ html:  saidaHtmlX });
                               }
                               return(0);
                             }

                          }

                          //
                          // Fazer os calculos da planilha
                          //
                          for(i = 1; i <='.$totalElementos.'; i++) {
                                                               
                             idLamina = "lamina" + i;
                             idRegula = "regula" + i;
                             idChuva = "chuva" + i;
                             idIrriga = "irriga" + i;
 
                             if(chuvaTabela[i] >= 4.99) // se chuva for maior ou igual a 5. Coloquei 4.99 por causa de arredondamento
                                necessidadeIrrigacao = etcTabela[i] + necessidadeIrrigacaoOntem - chuvaTabela[i] - irrigacaoTabela[i] * eficiencia;
                             else
                                necessidadeIrrigacao = etcTabela[i] + necessidadeIrrigacaoOntem - irrigacaoTabela[i] * eficiencia;
       
                             temp =  fase[i] - necessidadeIrrigacao;
                             if( temp > fase[i] )
                                temp = fase[i];

/*=================
                             if(irrigacaoTabela[i] > 0 || chuvaTabela[i] > 0) {
                                 temp =  fase[i] - necessidadeIrrigacao;
                                 if( temp > fase[i] )
                                     temp = fase[i];
                             }
                             else
                                temp =  fase[i] ;
=================*/

                             if(menorValor > temp)
                                menorValor  = temp;
 
                             if( i == 1) {

                                //if(temp >= 0)
                                  esgotamentoAguaSolo = esgotamentoAguaSolo + "[" + temp;

                                //else
                                  //esgotamentoAguaSolo = esgotamentoAguaSolo + "[0";

                                if(temp >= 0)
                                  esgotamentoAguaSolo2 = esgotamentoAguaSolo2 + "[" + temp;
                                else
                                  esgotamentoAguaSolo2 = esgotamentoAguaSolo2 + "[0";

                                fasesPlantio = fasesPlantio + "[" + fase[i];
                             }
                             else {

                                //if(temp >= 0)
                                  esgotamentoAguaSolo = esgotamentoAguaSolo + "," + temp;
                                //else
                                 // esgotamentoAguaSolo = esgotamentoAguaSolo + ",0";

                                if(temp >= 0)
                                  esgotamentoAguaSolo2 = esgotamentoAguaSolo2 + "," + temp;
                                else
                                  esgotamentoAguaSolo2 = esgotamentoAguaSolo2 + ",0";

                                fasesPlantio = fasesPlantio + "," +  fase[i] ;
                             }


                             if( necessidadeIrrigacao < 0) {
                             
                                necessidadeIrrigacao = 0.00;
                                laminaRecomendada = 0.00;

                             }
                             else {

                                   laminaRecomendada = necessidadeIrrigacao / eficiencia;
/*-----------------------------------
                                   //
                                   //Caso queria manter o valor da fase, descomente
                                   // os comentarios deste trecho e remova o comando anterior
                                   //  laminaRecomendada = necessidadeIrrigacao / eficiencia;
                                   //

                                   if( necessidadeIrrigacao < fase[i] ) {

                                     laminaRecomendada = necessidadeIrrigacao / eficiencia;

                                   }
                                   else  {

                                    laminaRecomendada = fase[i]  / eficiencia;
                                    necessidadeIrrigacao = fase[i];

                                   }
------------------------------------*/

                             }

                             //if ( laminaAplicada > laminaRecomendada )
                              //  laminaRecomendada = laminaAplicada;
                             if(laminaRecomendada > 0)
                                 regulagemPivot = 100.0 * laminaAplicada / laminaRecomendada;
                             else
                                 regulagemPivot = -1;

                             if( regulagemPivot > 100 )
                               regulagemPivot = 100.0;

                             //
                             // Ajustar os valores novos e as cores
                             //

                             //if( necessidadeIrrigacao < fase[i] )
                             if( laminaRecomendada < fase[i] )
                                  color = "green";
                             else
                                  color = "red";

                             regulagemPivot2 = regulagemPivot.toFixed(2);
                             laminaRecomendada2 = laminaRecomendada.toFixed(2);
                             document.getElementById(idLamina).value =  laminaRecomendada2;
                             document.getElementById(idLamina).style.color = color;

 
                             //if(  regulagemPivot2 >=0 && regulagemPivot2 < 100)
                             if(  regulagemPivot2 >=0)
                                document.getElementById(idRegula).value =  regulagemPivot2;
                             else
                                 document.getElementById(idRegula).value =  "";
                             necessidadeIrrigacaoOntem = necessidadeIrrigacao;
                             if(!isNaN(irrigacaoTabela[i]) )
                                total = total + irrigacaoTabela[i];


                          } // final do for (i=....

                          document.getElementById("resultadoFinal").value =  total.toFixed(2) ;
                          atualizacao = verificaMudanca('.$totalElementos.', 1);
 
                             //
                             // Obter dados de chuva e irrigacao para insercao ou
                             // atualizacao na base de dados
                             //
                            if(atualizacao == 1)
                               setTimeout( function() { salvaDadosUser(chuvaW, irrigacaoW); } , 10 );

                          //
                          // guardar os valores de chuva e irrigacao para serem
                          // comparados com a proxima alteracao
                          //
                          for(i = 1; i <='.$totalElementos.'; i++) {

                             idChuva = "chuva" + i;
                             idIrriga = "irriga" + i;
                             sessionStorage.setItem(idChuva, chuvaTabela[i]);
                             sessionStorage.setItem(idIrriga, irrigacaoTabela[i]);                            
                             //
                             // reter os dados iniciais, quando o sistema entro no ar, para
                             // entao salvar todas as atualizacoes
                             //
                             if( idBaixarPlanilha == 10) {

                                idChuva10 = "chuvaInicial" + i;
                                idIrriga10 = "irrigaInicial" + i;
                                sessionStorage.setItem(idChuva10, chuvaTabela[i]);
                                sessionStorage.setItem(idIrriga10, irrigacaoTabela[i]);                            
 
                             }

                          }

                       if(idBaixarPlanilha == 1) {
                          atualizacao = verificaMudanca('.$totalElementos.', 10);
                          setTimeout(  function() { salvaDadosUser(chuvaW, irrigacaoW);  setTimeout( function() { saidaHtmlX = "Dados salvos com sucesso!" ;  Swal.fire({ html:  saidaHtmlX }); } , 700 ); }, 100);
                       }

                       if(menorValor >= 0)
                          menorValor = -5;
                       else
                          menorValor = menorValor - 2;
 
                       valorTerra = "[";
                       for(i = 1; i <='.$totalElementos.'; i++) {

                          if( i > 1)
                             valorTerra = valorTerra + ", " + menorValor;
                          else
                             valorTerra = valorTerra + menorValor;
                             
                       }
                       valorTerra = valorTerra + "]";

                       esgotamentoAguaSolo = esgotamentoAguaSolo + "]";
                       esgotamentoAguaSolo2 = esgotamentoAguaSolo2 + "]";
                       fasesPlantio = fasesPlantio + "]";

                       if(idBaixarPlanilha <= 1 || idBaixarPlanilha == 10) //valores de idBaixarPlanilha = 0, 1, 10 podem gerar o grafico
                           grafico (esgotamentoAguaSolo, fasesPlantio, esgotamentoAguaSolo2, valorTerra, '.$totalElementos.');


                    }// final da funcao atualizaDados(idBaixarPlanilha) {

                   
                    function paginaManejo() {

                      setTimeout(  function() { atualizaDados(10);  setTimeout( function() { window.location="planilhaIrrigacao.html";  } , 700 ); }, 10);
                    }

                    function primeiraFase() {

                       mensagem = "<b style=\"color:green; \">IMPORTANTE!!</b><br><br><p style=\"color:black; text-align: justify;\">Falta de &aacute;gua, principalmente nos 3 primeiros dias, causa baixo pegamento das mudas (morte de plantas), e excesso de &aacute;gua pode causar maior incid&ecirc;ncia de doen&ccedil;as.</p>";
                       Swal.fire({ html:  mensagem });
                    }

                    function segundaFase() {

                       mensagem = "<b style=\"color:green\";>IMPORTANTE!!</b><br><br><p style=\"color:black; text-align: justify;\">Falta moderada de &aacute;gua, na fase de crescimento das plantas, pode favorecer o enraizamento, e o excesso de &aacute;gua favorece a incid&ecirc;ncia de doen&ccedil;as e desperd&iacute;cio de parte do adubo aplicado (principalmente nitrog&ecirc;nio e pot&aacute;ssio).</p>";
                       Swal.fire({ html:  mensagem });
                    }

                    function terceiraFase() {

                       mensagem = "<b style=\"color:green\";>IMPORTANTE!!</b><br><br><p style=\"color:black; text-align: justify;\">Falta de &aacute;gua na fase de flores e frutos causa queda de flores, deixa os frutos pequenos, e pode causar fundo preto nos frutos. O excesso de &aacute;gua favorece doen&ccedil;as e desperd&iacute;cio de adubo.</p>";
                       Swal.fire({ html:  mensagem });
                    }

        function quartaFase() {
                     
                       mensagem = "<b style=\"color:green\";>IMPORTANTE!!</b><br><br><p style=\"color:black; text-align: justify;\">Excesso de &aacute;gua, na matura&ccedil;&atilde;o dos frutos, prejudica a qualidade dos frutos para a ind&uacute;stria, reduzindo colora&ccedil;&atilde;o, acidez, e Brix, al&eacute;m de aumentar o n&uacute;mero de frutos podres na colheita <br><br>";
                       mensagem = mensagem + "Nas condi&ccedil;&otilde;es goianas, maior rendimento de polpa pode ser obtido paralisando as irriga&ccedil;&otilde;es quando a cultura apresentar entre ";
                       mensagem = mensagem + "40 e 50% de frutos maduros (10 a 15 dias da colheita). <br><br>J&aacute; a m&aacute;xima produtividade de frutos ";
                       mensagem = mensagem + "somente &eacute; atingida irrigando-se at&eacute; mais pr&oacute;ximo &agrave; colheita, com 60 a 90% de frutos maduros (5 a 10 dias antes  da colheita).</p>";
                       Swal.fire({ html:  mensagem });
        }

        function grafico (esgotamentoAguaSolo, fases, esgotamentoAguaSolo2, valorTerra, total) {
                 
                     //
                     // ver os exemplos das paginas
                     // https://www.chartjs.org/samples/latest/
                     // https://www.chartjs.org/samples/latest/charts/area/line-boundaries.html
                     // ver sobre a opcao fill, para preencher o grafico abaixo da linha
                     // Se fazer fill = true taem da certo
                     //
                     // Veja tambem a documentacao em
                     // https://www.chartjs.org/docs/latest/configuration
                     // https://help.docraptor.com/en/articles/1197314-code-example-chartjs
                     //
                     // Para ver como fazer os acentos das palavras no grafico, veja o sitio
                     // https://rotinadigital.net/como-exibir-corretamente-caracteres-acentuados-nas-mensagens-javascript-e-codigo-html/
                     //

                     var config = {
                        type: "line",
                        data: {
                ';

                $labels = "                                labels: [";
                for($j = 1; $j < $totalElementos; $j++) {

                   $labels = $labels ."\"$j\", ";
                     
                }
                $labels = $labels ."\"$totalElementos\"],  ";

                echo $labels."\n";
                echo '
                                
                                datasets: [{
                                        label: "",
                                        borderColor: window.chartColors.green,
                                        backgroundColor: "rgb(255, 255, 255)",
                                        data: eval(esgotamentoAguaSolo),
                                        fill: \'false\'
                                }, {
                                        label: "Esgotamento de \u00E1gua no solo",
                                        borderColor: window.chartColors.green,
                                        backgroundColor: window.chartColors.green,
                                        data: eval(fases),
                                        fill: \'-1\'
 
                                }, {
                                         label: "\u00C1gua dispon\u00EDvel no solo",
                                         borderColor: window.chartColors.blue,
                                         backgroundColor: window.chartColors.blue,
                                         data: eval(esgotamentoAguaSolo2),
                                         fill: \'origin\'

                                }, {
                                         label: "",
                                         borderColor: "rgb(255, 255, 255)",
                                         backgroundColor: "rgb(249, 234, 195)",
                                         data: eval(valorTerra),
                                         fill: \'origin\'

                                }]
                        },
                        options: {
                                responsive: true,
                                legend: {
                                           labels: {
                                                     filter: function(legendItem, chartData) {
                                                                    if (legendItem.datasetIndex === 0 || legendItem.datasetIndex === 3 ) {
                                                                          return false;
                                                                    }
                                                                    return true;
                                                             }
                                           }
                                },
                                title: {
                                        display: true,
                                        text: "\u00C1gua no solo",
                                        fontSize: 20

                                },
                                tooltips: {
                                        mode: "index",
                                        filter: function (tooltipItem) {
                                                     
                                                         return tooltipItem.datasetIndex === 1 || tooltipItem.datasetIndex === 2;
                                                     
                                                },
                                        intersect: false
                                },
                                hover: {
                                        mode: "index",
                                        intersect: true
                                },
                                elements: {
                                        line: {
                                                tension: 0.000001,
                                                borderWidth: 0
                                        },
                                        point: {
                                                radius: 0
                                        }
                                },
                                scales: {
                                        xAxes: [{
                                                display: true,
                                                scaleLabel: {
                                                        display: true,
                                                        labelString: "Ciclo"
                                                }
                                        }],
                                        yAxes: [{
                                                display: true,
                                                scaleLabel: {
                                                        display: true,
                                                        labelString: "mm"
                                                }
                                        }]
                                }
                        }
                   
                     }; // final da definicao de config

                        if (myLine) {    myLine.destroy();  }
                        var ctx = document.getElementById("canvas").getContext("2d");
                        myLine = new Chart(ctx, config);                          
        }

        function calculaEscoamentoAgua( obj) {

           var area, solo, id;

           area2 = '.$areaPivo.';
           //area2 = area2.replace(/[,]/ig, ".");
           area = parseFloat(area2);

           //
           // Obter qual a linha que contem a area do pivot (eixo x da tabela)
           //
           if( area < 0.90 ) {

             id = 1;
           }
           else
            if( area >= 0.90 && area <= 3.60 ) {

              id = 2;
            }
            else
              if( area > 3.60 && area <= 8.090 ) {

                id = 3;
              }
              else
                if( area > 8.090 && area <= 14.38 ) {

                  id = 4;
                }
                else
                  if( area > 14.38 && area <= 22.46) {
 
                    id = 5;
                  }
                  else
                    if( area > 22.46  && area <= 32.35) {

                      id = 6;
                    }
                    else
                      if( area > 32.35  && area <= 44.03) {

                        id = 7;
                      }
                      else
                        if ( area > 44.03 && area <= 57.51) {

                          id = 8;
                        }
                        else
                          if( area > 57.51 && area <= 72.79 ) {

                            id = 9;
                          }
                          else
                            if( area > 72.79 && area <= 89.86) {

                               id = 10;
                            }
                            else {

                               id = 11;
                            }
                             


           //
           // Obter o tipo de solo (obter qual dado a ser buscado)
           //
           var solo = '.$tipoSolo.'
           if( solo == 1  ) {
             
               valorTI = arenosoTI[id];
           }
           else
             if (solo == 2) {
                 
                 valorTI = medioTI[id];
             }
             else
               if (solo == 3) {
                   
                  valorTI = argilosoTI[id];
               }
               else {
                 
                 return(0);
               }
               

           //
           // Fazer a busca na tabela, conforme diametro molhado (qual tabela usar, de 5 a 18 m, de 1/2 em 1/2 m
           //

           id1 = parseInt(obj.value);
           if( id1 == 1)
              diametroMolhado = 13;
           else
             if( id1 == 2)
              diametroMolhado = 14;
             else
               if(id1 == 3)
                 diametroMolhado = 15;
               else
                 if(id1 == 4)
                    diametroMolhado = 16;
                 else
                   if(id1 == 5)
                     diametroMolhado = 17;
                   else
                     if(id1 == 6)
                        diametroMolhado = 18;
                     else
                       if(id1 == 7)
                          diametroMolhado = 19;
                       else
                           diametroMolhado = 20;
 
           idY = 0;          
           if( diametroMolhado == 13) {

                  for(k = 1; k <= 27; k++) {

                     if( m_13[id][k] > valorTI ){

                         if(k > 1)
                          idY = k - 1;
                         else
                          idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                         
                         k = 29;
                         break;
                     }
                  }

           }
           else
             if( diametroMolhado == 14) {


                  for(k = 1; k <= 27; k++) {

                     if( m_14[id][k] > valorTI ){

                         if(k > 1)
                          idY = k - 1;
                         else
                          idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                         k = 29;
                         break;
                     }
                  }


             }
             else
               if( diametroMolhado == 15) {


                  for(k = 1; k <= 27; k++) {

                     if( m_15[id][k] > valorTI ){

                         if(k > 1)
                          idY = k - 1;
                         else
                          idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                         k = 29;
                         break;
                     }
                  }


               }
               else
                 if( diametroMolhado == 16) {

                    for(k = 1; k <= 27; k++) {

                       if( m_16[id][k] > valorTI ){

                         if(k > 1)
                          idY = k - 1;
                         else
                          idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                         k = 29;
                         break;
                       }
                    }

                 }
                 else
                   if(  diametroMolhado == 17) {

                                 for(k = 1; k <= 27; k++) {

                                     if( m_17[id][k] > valorTI ){

                                         if(k > 1)
                                            idY = k - 1;
                                         else
                                            idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                                         k = 29;
                                         break;
                                     }
                                 }

                   }
                   else
                     if( diametroMolhado == 18 ) {

                                 for(k = 1; k <= 27; k++) {

                                     if( m_18[id][k] > valorTI ){

                                         if(k > 1)
                                            idY = k - 1;
                                         else
                                            idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                                         k = 29;
                                         break;
                                     }

                                 }


                     }
                     else
                       if( diametroMolhado == 19 ) {

                                 for(k = 1; k <= 27; k++) {

                                     if( m_19[id][k] > valorTI ){

                                         if(k > 1)
                                            idY = k - 1;
                                         else
                                            idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                                         k = 29;
                                         break;
                                     }
                                 }


                       }
                       else
                         if( diametroMolhado == 20) {

                                 for(k = 1; k <= 27; k++) {

                                     if( m_20[id][k] > valorTI ){

                                         if(k > 1)
                                            idY = k - 1;
                                         else
                                            idY = k; // se logo na primeira coluna o valor eh maior que o permitido
                                         k = 29;
                                         break;
                                     }
                                 }


                         }

           pos = 0;
           if( idY > 0)
              pos = 0.50*idY + 4.50;
 
            document.getElementById("rotulo2").style.visibility = "visible";

 
            if(pos > 0){

               regLaminaMax = 100*laminaAplicada/pos;
               if(regLaminaMax > 100)
                 regLaminaMax = 100.00;
               regLaminaMax = regLaminaMax.toFixed(2);                
               document.getElementById("rotulo3").style.visibility = "visible";
               document.getElementById("lamMax").innerHTML = pos + " mm.";
               document.getElementById("regLamMax").innerHTML = regLaminaMax+ " %.";

            }
            else {

               document.getElementById("rotulo3").style.visibility = "hidden";
               document.getElementById("lamMax").innerHTML = "nenhum (valor n&atilde;o encontrado). ";
            }

        } // final de function calculaEscoamentoAgua() {

                   </script>

                   <p class="creditos">© 2020 Embrapa Arroz e  Feij&atilde;o / UFG. Todos os direitos reservados.</p>
                  </body>
                  </html>';
//
// Final  do trecho do programa que faz os calculos diarios sobre a lamina de agua

     }
     
     mysqli_close($conexao);
     

?>
