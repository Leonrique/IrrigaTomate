  <?php

       include 'pathConfig.php';
       $arquivoPath = configPath;
       include($arquivoPath);

       $idCidade = $_GET["idCidade"];
       $nomeCidade = $_GET["nome"];
       $diaPlantio = $_GET["dia"]; // Novo
       $mesPlantio = $_GET["mes"]; // Novo

       $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
       if (!$conexao) {

             echo '
               <center>
               <table border=0>
                     <tr><td><br><h2  style="font-size:24px; color:green; font-weight:bold; margin-left:0px; margin-right: 0px"><center> Problemas de acesso &agrave; base de dados do sistema. Tente mais tarde. </center></h2></td></tr>
               </table>
               </center>
             ';
              exit(1);
       }


       echo '
                 <!doctype html>
                 <html>
                 <head>
                    <meta charset="utf-8">
                    <title>Vari&aacute;veis clim&aacute;ticas</title>
                    <link href="css/singlePage.css" rel="stylesheet" type="text/css">
                    <link rel="shortcut icon" type="image/x-icon" href="imagens/favicon.ico">
                    <link rel="stylesheet" href="https://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" / >
                    <script src="js/Chart.min.js"></script>
                    <script src="js/utils.js"></script>

                    <link rel="stylesheet" href="js/package/dist/sweetalert2.min.css">
                    <script src="js/package/dist/sweetalert2.all.min.js"></script>
                    <script src="js/package/dist/sweetalert2.min.js"></script>
                    <script type="text/javascript" src="getDadosClimaticos.js"></script>

                    <script src="https://code.jquery.com/jquery-1.8.2.js"></script>
                    <script src="https://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
                    <script>
                      function paginaPlanilha() {
                        setTimeout(  function() { setTimeout( function() { window.location.href = document.referrer;  } , 700 ); }, 10);
                      }
                    </script>
                 </head>
                 <body onload="getDadosClimaticos('.$idCidade.', '.$diaPlantio.', '.$mesPlantio.')">
                 <!-- Barra dos logotipos -->
                 <div id="barraLogotipos">
                   <!-- Logo irrigaFeijao  -->
                   <img src="imagens/logo-irrigaTomate.svg" class="logoIrrigaTomate" />
                 </div>

                 <!-- barra da imagem de fundo com slogam -->
                 <div id="barraDestaque">
                   <!-- quadro com informaçtaque -->>
                   <div id="conteudoDestaque">
                     <h1> DADOS</h1>
                     <h2>CLIM&Aacute;TICOS</h2>
                     <div id="apresentacao" >
                        <p>Acesso f&aacute;cil &agrave; v&aacute;riaveis clim&aacute;ticas do Estado de Goi&aacute;s</p>
                     </div>
                   </div>

                 </div>
                 <br><br>
                 <br><br>

       ';
     
       echo '
                 <!-- box do conteudo-->

                  <div id="conteudoPrincipal">
                     <p style="margin: 40px 20px"><a href="javascript:paginaPlanilha()" class="botao">« voltar</a></p>
                     <p style="font-size:25px">Dados Clim&aacute;ticos de <span>'.$nomeCidade.' </span></p>                     
                     <div id="tabela">

                     </div>
                  </div>
            ';
             mysqli_close($conexao);

?>
