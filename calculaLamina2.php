<?php

     $idCidade = $_POST["cidade"];
     $data = $_POST["calendario"];
     $efic = $_POST["optradio"];
     $laminaAplicada = $_POST["norma"];
     $identificacao = $_POST["ident"];
     $regula = $_POST["norma"];
     $sistemaDePlantio = $_POST["optradio2"]; // 1 - convencional;  2- direto;

     $pieces = explode("/", $data);
     $mes = (int)$pieces[1];
     $dia = (int)$pieces[0];

     if($efic == 1) {
       $eficiencia = 0.85;
       $eficiencia2 = 85;
     }
     else {
       $eficiencia = 0.80;
       $eficiencia2 = 80;
     }

     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

     
     $conexao = mysqli_connect(hostBancoPantanal, userDonoPantanal, senhaDonoPantanal, nomeBancoPantanal) ;
     if (!$conexao) {

             echo '
               <center>
               <table border=0>
                     <tr><td><img SRC="imagens/embrapaArrozFeijao.png" ALT="Embrapa Arroz e Feij&atilde;o" BORDER=0></td></tr>
                     <tr><td><br><h3  style="background-color: green; font-size:24px; color:white; font-weight:bold; margin-left:0px; margin-right: 0px"><center> Planilha de irriga&ccedil;&atilde;o </center></h3></td></tr>
                     <tr><td><br><h2  style="font-size:24px; color:green; font-weight:bold; margin-left:0px; margin-right: 0px"><center> Problemas de acesso &agrave; base de dados do sistema. Tente mais tarde. </center></h2></td></tr>
                     <tr><td><center><table border = 0>
                             <tr style="color:blue; font-weight: bold; font-size:20px;"><td><center><a href="javascript:history.go(-1)" >Voltar</a><br></center></td><td><center><br></center></td><td><center></center></td><td><center></center></td><td><center></center></td></tr></table></center>

               </table>
               </center>
             ';
              mysql_close($conexao);
              exit(1);

       }


     $sql = "select m.cidadeHTML, e.siglaEstado from municipios as m, estados as e  where e.id=m.idEstado and m.id = $idCidade;";
       

     //$query = mysql_query($sql) ;
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
                    $nomeCidade = "";
                    $sigla = "";
           }
     else {

            //$numLinhas = mysql_num_rows($query);
            $numLinhas = $query->num_rows;

            if( $numLinhas > 0 ) {

                   //$linha=mysql_fetch_row($query);
                   $linha=$query->fetch_row() ;
                   $nomeCidade = $linha[0];
                   $sigla = $linha[1];
            }
            else {

                    $nomeCidade = "";
                    $sigla = "";
            }

     }


     $sql= "select dia, mes, eto, tempMax, tempMin from  evapoTrasnpiracaoTomate  where idCidade = $idCidade and ( (mes = $mes and dia > $dia ) or mes > $mes );";

     //$query = mysql_query($sql) ;
     $query = mysqli_query($conexao, $sql) ;
     if(!$query)
           {
             echo '
               <center>
               <table border=0>
                     <tr><td><img SRC="imagens/embrapaArrozFeijao.png" ALT="Embrapa Arroz e Feij&atilde;o" BORDER=0></td></tr>
                     <tr><td><br><h3  style="background-color: green; font-size:24px; color:white; font-weight:bold; margin-left:0px; margin-right: 0px"><center> Planilha de irriga&ccedil;&atilde;o </center></h3></td></tr>
                     <tr><td><br><h2  style="font-size:24px; color:green; font-weight:bold; margin-left:0px; margin-right: 0px"><center> N&atilde;o existem dados para esta cidade!  </center></h2></td></tr>
                     <tr><td><center><table border = 0>
                             <tr style="color:blue; font-weight: bold; font-size:20px;"><td><center><a href="javascript:history.go(-1)" >Voltar</a><br></center></td><td><center><br></center></td><td><center></center></td><td><center></center></td><td><center></center></td></tr></table></center>

               </table>
               </center>
             ';
              mysqli_close($conexao);
              exit(1);
           }

     //$numLinhas = mysql_num_rows($query);
     $numLinhas = $query->num_rows;

     if ($numLinhas == 0)
      {
          echo '
               <center>
               <table border=0>
                     <tr><td><img SRC="imagens/embrapaArrozFeijao.png" ALT="Embrapa Arroz e Feij&atilde;o" BORDER=0></td></tr>
                     <tr><td><br><h3  style="background-color: green; font-size:24px; color:white; font-weight:bold; margin-left:0px; margin-right: 0px"><center> Planilha de irriga&ccedil;&atilde;o </center></h3></td></tr>
                     <tr><td><br><h2  style="font-size:24px; color:green; font-weight:bold; margin-left:0px; margin-right: 0px"><center> N&atilde;o foram encontrados dados sobre esta cidade! </center></h2></td></tr>
                     <tr><td><center><table border = 0>
                             <tr style="color:blue; font-weight: bold; font-size:20px;"><td><center><a href="javascript:history.go(-1)" >Voltar</a><br></center></td><td><center><br></center></td><td><center></center></td><td><center></center></td><td><center></center></td></tr></table> </center>

               </table>
               </center>
          ';
               mysql_close($conexao);
               exit(1);
      }

//  Inicio do trecho do programa que faz os caldulos diarios sobre a lamina de agua
//
                include('dados.php');

                if( $sistemaDePlantio == 2 ) { // 1 - convencional; 1

                       $tipoPlantio = "Convencional";
                }
                else
                  if( $sistemaDePlantio == 1 ) { // 2 - plantio direto

                              $tipoPlantio = "Plantio direto";
                  }

                $j = -1;


                for ($i = 1; $i <= 335; $i++) // 335 eh o mumero de dias, nos meses de fevereiro ateh julho + dias de plantio ( + - 120)
                  {

                     if ( $dataDia[$i] == $dia && $dataMes[$i] == $mes)
                      {
                        $j = $i;
                        $i = 700;
                      }
                  }

               if( $j < 0)
                {

                  echo '
                        <center>
                        <table border=0>
                              <tr><td><img SRC="imagens/embrapaArrozFeijao.png" ALT="Embrapa Arroz e Feij&atilde;o" BORDER=0></td></tr>
                              <tr><td><br><h3  style="background-color: green; font-size:24px; color:white; font-weight:bold; margin-left:0px; margin-right: 0px"><center> Planilha de irriga&ccedil;&atilde;o </center></h3></td></tr>
                              <tr><td><br><h2  style="font-size:24px; color:green; font-weight:bold; margin-left:0px; margin-right: 0px"><center> N&atilde;o tem, na base de dados, o valor da temperatura de evapotranspira&ccedil;&atilde;o </center></h2></td></tr>
                              <tr><td><center><table border = 0>
                              <tr style="color:blue; font-weight: bold; font-size:20px;"><td><center><a href="javascript:history.go(-1)" >Voltar</a><br></center></td><td><center><br></center></td><td><center></center></td><td><center></center></td><td><center></center></td></tr></table> </center>

                        </table>
                        </center>
                   ';
                   mysql_close($conexao);
                   exit(1);
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
               $mes1 = (int)$mes;
               $dia = (int)$dia;
               $percentimetro2 = (100.0*$regula)/7.0;
               if($percentimetro2 > 100.0 )
                  $percentimetro2 = 100.00;
               else
                  $percentimetro2 = number_format($percentimetro2 + 0.5, 0, '.', ''); // faz com que o numero tenha apenas uma casa decimal
               if($percentimetro2 > 100.0 )
                 $percentimetro2 = 100.0;

               session_start();
               echo '
                 <!doctype html>
                 <html>
                 <head>
                 <meta charset="utf-8">
                 <title>Planejamento / Planilha</title>
                 <link href="css/singlePage.css" rel="stylesheet" type="text/css">
                 <link rel="shortcut icon" type="image/x-icon" href="imagens/favicon.ico">
                 </head>
                 <body>
                 <!-- Barra dos logotipos -->
                 <div id="barraLogotipos">
                   <!-- Logo irrigaFeijao  -->
                   <img src="imagens/logo-irrigaTomate.svg" class="logoIrrigaTomate" />
                 </div>
                 <div id="barraMenu">
                   <!-- barra do menu de navegaç->
                   <div id="Navegador">
                     <nav>
                       <ul>
                          <li><a href="sobre.html">SOBRE</a></li>
                          <li><a href="index.html" class="selecionado" >PLANEJAMENTO</a></li>
                          <li><a href="login.html">MANEJO</a></li>
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
                   <!-- quadro com informaçem destaque -->
                 <!--
                   <div id="conteudoDestaque">
                     <h1> MANEJO</h1>
                     <h2>DA IRRIGAÃ</h2>    
                     <div id="apresentacao" >
                       <p>Acesso f&aacute;cil a informa&ccedil;&otilde;es para manejo pr&aacute;tico da irriga&ccedil;&atilde;o do tomateiro na sua propriedade</p>
                     </div>
                   </div>
                 -->
                 </div>
                 <!-- box do conteúmulá-->
                 <div id="conteudoPrincipal">
                  <a class="botaoPlanilha" href="downloadPlanilha.php"><img src="imagens/icoPlanilhaBaixar.png" /></a>
                  <p style="margin: 80px 40px"><a href="javascript:history.back()" class="botao">« voltar</a></p>
                  <p>Munic&iacutepio: <span>'.$nomeCidade.' - '.$sigla.'</span></p>
                  <p>Identifica&ccedil;&atilde;o: <span>'.$identificacao.'</span> </p>
                  <p>Efici&ecirc;ncia: <span>'.$eficiencia2.' %</span></p>
                  <p>L&acirc;mina aplicada (mm) : <span>'.$laminaAplicada.'</span></p>
                  <p>Sistema de plantio : <span>'.$tipoPlantio.'</span></p>
                  <table class="resultado" border="0" cellpadding="0" cellspacing="0" align="center">
                   <tbody>
                   <colgroup>
                  <col style="width: 20%; background-color:#e8f0d9;">
                  <col style="width: 15%; background-color:#fff;">
                  <col style="width: 15%; background-color:#e8f0d9;">
                  <col style="width: 15%; background-color:#fff;">
                  <col style="width: 15%; background-color:#e8f0d9;">
                  <col style="width: 20%; background-color:#fff;">
                   </colgroup>
                     <tr>
                       <th >Dias</th>
                       <th>Dias ap&oacute;s o transplantio</th>
                       <th>Est&aacute;dio</th>
                       <th>L&acirc;mina di&aacute;ria (mm)</th>
                       <th>L&acirc;mina de irriga&ccedil;&atilde;o (mm)</th>
                       <th> Percent&iacute;metro do piv&ocirc;-central (%) </th>
                     </tr>
                     <tr><td>'.$dia.'/'.$mesNovo[$mes1].'</td><td>0</td><td>--</td><td>--</td><td>15.00</td><td></td></tr>
                     <!--tr><td>'.$dia.'/'.$mesNovo[$mes1].'</td><td>0</td><td>--</td><td>--</td><td>5.0</td><td>'.$percentimetro2.'</td></tr-->

               ';

               $textoExcel =  "";
               $textoExcel = $textoExcel . '<center>';
               $textoExcel = $textoExcel . '         <table border = 0 style="color: green; font-weight: bold; font-size:18px; width: 2700px"  id="tabelaCabecalho"  colspan=4>';
               $textoExcel = $textoExcel . '            <tr><td style="color: brown; padding-left: 0px" colspan=6 > <center> IrrigaTomate - Planilha de irriga&ccedil;&atilde;o de tomate</center></td></tr>';
               $textoExcel = $textoExcel . '            <tr><td  colspan=6 ">Munic&iacute;pio: <span style="color:blue">'.$nomeCidade.' - '.$sigla.'</span></td><td></td><td></td></tr>';
               $textoExcel = $textoExcel . '            <tr><td style="color:green;" colspan=6 >Identifica&ccedil;&atilde;o  piv&ocirc;-central / quadrante / propriet&aacute;rio: <span style="color:blue">'.$identificacao.'</span></td><td></td><td></td></tr>';
               $textoExcel = $textoExcel . '            <tr><td style="color: green" colspan=6 >Efici&ecirc;ncia de distribui&ccedil;&atilde;o de &aacute;gua do piv&ocirc;-central: <span style="color:blue">'.$eficiencia2.' %</span></td><td></td><td></td></tr>';
               $textoExcel = $textoExcel . '            <tr><td  style="color:green;" colspan=6>L&acirc;mina aplicada a 100% do percent&iacute;metro (mm): <span style="color:blue">'.$laminaAplicada.'</span></td><td></td><td></td></tr>';
               $textoExcel = $textoExcel . '            <tr><td  style="color:green;" colspan=6>Sistema de plantio : <span style="color:blue">'.$tipoPlantio.'</span></td><td></td><td></td></tr>';
               $textoExcel = $textoExcel . '         </table></center>';

               $textoExcel = $textoExcel . '<table border = 0>';
               $textoExcel = $textoExcel . '      <tr style="color:blue; font-weight: bold; font-size:20px;"><td><center><br></center></td><td><center><br></center></td><td><center></center></td><td><center></center></td><td><center></center></td><td><center></center></td></tr>';
               $textoExcel = $textoExcel . '      <tr style="background-color: lightblue; font-size:17px"><th>Dias</th><th>Dias ap&oacute;s <br>a semeadura</th><th>Est&aacute;dio</th><th>L&acirc;mina <br>di&aacute;ria (mm)</th><th>L&acirc;mina de <br>irriga&ccedil;&atilde;o (mm)</th><th><center>Percent&iacute;metro do <br>piv&ocirc;-central (%)</center></th></tr>';
               $textoExcel = $textoExcel . '      <tr style="color:green; font-weight: bold; background-color: lightgray;"><td><center>'.$dia.'/'.$mesNovo[$mes1].'</center></td><td><center>0</center></td><td><center>--</center></td><td><center>--</center></td><td><center>7.0</center></td><td><center>'.$percentimetro2.'</center></td></tr>';


               //----------------------------------------------------------
               // Obter os valores de cada linha do relatorio de saida

                   
               $i = 0;
               $fim = 0;
               $contaFase1= 0;
               $contaFase4= 0;
               $flag4 = 1;
               $flag3 = 1;
               $flag2 = 1;
               $flag = 1;
               $flagFase2 = 1;
               $flagFase4 = 1;

               $k1 = (1.10 - 0.65)/(423.9 - 91.0);
               $k2 = (1.05 - 0.50)/(423.9 - 91.0);
               $k3 = (1.10 - 0.35)/(1256.5 - 965.4);
               $k4 = (1.05 - 0.35)/(1256.5 - 965.4);

               $grausDia = 0;
               while( $linha=$query->fetch_row() ) {

                  $i = $i + 1;
                  $evptp[$i] = $linha[2];
                  $mes1 = (int)$dataMes[$j];
                  $semeadura[$i] = $dataDia[$j].'/'.$mesNovo[$mes1];
 
                  $tempMedia = ($linha[3] + $linha[4])/2.0;
                  $diasAposSemeadura[$i] = $i - 1;


                  $grausDia = $grausDia + $tempMedia - 10;
                  $grausDia2 = $tempMedia - 10;

                  //      $sistemaDePlantio = $_POST["optradio2"]; // 1 - convencional;  2- direto;

                  if ( $grausDia <= 91.0 ) {

                          $estadio[$i] = "I";
                          if( $sistemaDePlantio == 1 )
                             $kcNew[$i] =  0.90;
                          else
                             $kcNew[$i] =  0.45;

                          $contaFase1 = $contaFase1 + 1;
                      }
                      else
                        if( $grausDia <=  423.9 ) {

                          $estadio[$i] = "II";
                          if( $sistemaDePlantio == 1 ) {

                             if($flagFase2 == 1) {

                                 $flagFase2 = 0;
                                 $kcNew[$i] = 0.65;

                             }
                             else {

                                   $kcNew[$i] = $kcNew[$i - 1] + $grausDia2*$k1;

                             }  
                          }
                          else {


                             if($flagFase2 == 1) {

                                 $flagFase2 = 0;
                                 $kcNew[$i] = 0.50;

                             }
                             else {

                                   $kcNew[$i] = $kcNew[$i - 1] + $grausDia2*$k2;

                             }  
                          }
                          if ( $flag2 == 1 ) {

                             $flag2 = 0;
                             //$pos2 = $i;
                          }

                        }
                        else
                          if(  $grausDia <= 965.4 ) {
                         
                               $estadio[$i] = "III";
                               if ( $flag3 == 1 ) {

                                    $flag3 = 0;
                                    $pos3 = $i;
                                    $pos2 = $i;
                               }
                               if( $sistemaDePlantio == 1 ) {

                                  $kcNew[$i] = 1.10;

                               }
                               else {

                                  $kcNew[$i] = 1.05;
                               }
                               

                          }
                          else
                           if( $grausDia <=  1256.5  ) {

                               $estadio[$i] = "IV (a)";
                               $contaFase4 = $contaFase4 + 1;
                               if ( $flag4 == 1 ) {

                                      $flag4 = 0;
                                      $pos4 = $i;
                               }
                               if( $sistemaDePlantio == 1 ) {

                                   if($flagFase4 == 1) {

                                      $kcNew[$i] = 1.10;
                                      $flagFase4 = 0;
                                   }
                                   else {

                                      $kcNew[$i] = $kcNew[$i - 1] - $grausDia2*$k3;

                                  }
                               }
                               else {


                                   if($flagFase4 == 1) {

                                      $kcNew[$i] = 1.05;
                                      $flagFase4 = 0;
                                   }
                                   else {

                                      $kcNew[$i] = $kcNew[$i - 1] - $grausDia2*$k4;

                                  }
                               }

                           }
                           else
                            if( $grausDia <=  1379.3   ) {

                               $estadio[$i] = "IV (b)";
                               $kcNew[$i] = 0.35; //Nao importa o sistema de plantio

                           }
                           else {

                                 $fim = 1;
                           }

                   
                  $j = $j + 1;// os calculos comecam para o dia posterior ao que foi passado.
                 
                  if( $fim == 1 )
                     break;


               } // while($linha=mysql_fetch_row($query) ) {


               mysqli_close($conexao);

 
               //
               // Execucao dos calculos da planilha e impressao da mesma
               //

                   $flag2 = 0;
                   $flag = 1;
                   $numDiasTotal = $i - 1; // Descontar a ultima vez q significa o fim
                   $j = $j - 1; // descontar a ultima passagem do loop na qual achou o fim do mesmo
                   $total = 0;
                   $numFaseA = $contaFase1 ;

 

                 for ( $i = 1; $i <= $numDiasTotal; $i++ ) {

                     $kc[$i] = $kcNew[$i];
                     $lamina = $kc[$i] * $evptp[$i] / $eficiencia;;
                     $lamina = number_format($lamina, 2, '.', ''); // faz com que o numero tenha apenas uma casa decimal
                     $laminaDiaria[$i] = $lamina;
                     $percentimetro[$i] = 100.0*$regula/$lamina;
                     $total = $total + $lamina ;
                 
                     if($percentimetro[$i] > 100.00 )
                        $percentimetro[$i] = 100.0;
                     else
                        $percentimetro[$i] = number_format($percentimetro[$i] + 0.5, 0, '.', ''); // faz com que o numero tenha apenas uma casa decimal

                 }


                   
               //
               // Impressao do resultado final
               //

                 $j = 0;
                 for ( $i = 1; $i <= $numDiasTotal; $i++ ) {
 
                       if( $i <= $numFaseA ) {

                            echo '<tr><td>'.$semeadura[$i].'</td><td>'.$i.'</td><td>'.$estadio[$i].'</td><td>'.$laminaDiaria[$i].'</td><td>'.$laminaDiaria[$i].'</td><td>'.$percentimetro[$i].'</td></tr>';
                            $textoExcel = $textoExcel .  '<tr style="color:green; font-weight: bold; background-color: lightgray;"><td><center>'.$semeadura[$i].'</center></td><td><center>'.$i.'</center></td><td><center>'.$estadio[$i].'</center></td><td><center>'.$laminaDiaria[$i].'</center></td><td><center>'.$laminaDiaria[$i].'</center></td><td><center>'.$percentimetro[$i].'</center></td></tr>';
                       }
                       else
                         if( $i > $numFaseA && $i < $pos2) {
 
                            $j = $j + 1;
                            if ($j % 2 == 0) {

                                   $ultimaLamina = $i;
                                   $lamina = $laminaDiaria[ $i ] + $laminaDiaria[ $i - 1 ];
                                   $percentimetro[$i] =  100.0*$regula/$lamina;
                                   if($percentimetro[$i] > 100.00 )
                                      $percentimetro[$i] = 100.0;
                                   else
                                      $percentimetro[$i] = number_format($percentimetro[$i] + 0.5, 0, '.', ''); // faz com que o numero tenha apenas uma casa decimal
                                   echo '<tr><td>'.$semeadura[$i].'</td><td>'.$i.'</td><td>'.$estadio[$i].'</td><td>'.$laminaDiaria[$i].'</td><td>'.$lamina.'</td><td>'.$percentimetro[$i].'</td></tr>';
                                   $textoExcel = $textoExcel .  '<tr style="color:green; font-weight: bold; background-color: lightgray;"><td><center>'.$semeadura[$i].'</center></td><td><center>'.$i.'</center></td><td><center>'.$estadio[$i].'</center></td><td><center>'.$laminaDiaria[$i].'</center></td><td><center>'.$lamina.'</center></td><td><center>'.$percentimetro[$i].'</center></td></tr>';

                            }
                            else {

                                   $lamina2 = "";
                                   echo '<tr><td>'.$semeadura[$i].'</td><td>'.$i.'</td><td>'.$estadio[$i].'</td><td>'.$laminaDiaria[$i].'</td><td>'.$lamina2.'</td><td></td></tr>';
                                   $textoExcel = $textoExcel .  '<tr style="color:green; font-weight: bold; background-color: lightgray;"><td><center>'.$semeadura[$i].'</center></td><td><center>'.$i.'</center></td><td><center>'.$estadio[$i].'</center></td><td><center>'.$laminaDiaria[$i].'</center></td><td><center>'.$lamina2.'</center></td><td></td></tr>';

                            }

                         }
                         else
                           if( $i  >=  $pos2 ) {

                               if( ($i - $ultimaLamina) % 3 == 0 ) {
 
                                   $ultimaLamina = $i;
                                   $lamina = $laminaDiaria[ $i ] + $laminaDiaria[ $i - 1 ] + $laminaDiaria[ $i - 2 ];
                                   $percentimetro[$i] =  100.0*$regula/$lamina;
                                   if($percentimetro[$i] > 100.00 )
                                      $percentimetro[$i] = 100.0;
                                   else
                                      $percentimetro[$i] = number_format($percentimetro[$i] + 0.5, 0, '.', ''); // faz com que o numero tenha apenas uma casa decimal
                                   echo '<tr><td>'.$semeadura[$i].'</td><td>'.$i.'</td><td>'.$estadio[$i].'</td><td>'.$laminaDiaria[$i].'</td><td>'.$lamina.'</td><td>'.$percentimetro[$i].'</td></tr>';
                                   $textoExcel = $textoExcel .  '<tr style="color:green; font-weight: bold; background-color: lightgray;"><td><center>'.$semeadura[$i].'</center></td><td><center>'.$i.'</center></td><td><center>'.$estadio[$i].'</center></td><td><center>'.$laminaDiaria[$i].'</center></td><td><center>'.$lamina.'</center></td><td><center>'.$percentimetro[$i].'</center></td></tr>';
                               }
                               else {

                                   $lamina2 = "";
                                   echo '<tr><td>'.$semeadura[$i].'</td><td>'.$i.'</td><td>'.$estadio[$i].'</td><td>'.$laminaDiaria[$i].'</td><td>'.$lamina2.'</td><td></td></tr>';
                                   $textoExcel = $textoExcel .  '<tr style="color:green; font-weight: bold; background-color: lightgray;"><td><center>'.$semeadura[$i].'</center></td><td><center>'.$i.'</center></td><td><center>'.$estadio[$i].'</center></td><td><center>'.$laminaDiaria[$i].'</center></td><td><center>'.$lamina2.'</center></td><td></td></tr>';
                                   //$textoExcel = $textoExcel .  '<tr style="color:green; font-weight: bold; background-color: lightgray;"><td><center>'.$semeadura[$i].'</center></td><td><center>'.$i.'</center></td><td><center>'.$estadio[$i].'</center></td><td><center>'.$laminaDiaria[$i].'</center></td><td><center>'.$lamina2.'</center></td><td><center>'.$percentimetro[$i].'</center></td></tr>';
                               }


                           }



                 } //  for ( $i = 1; $i <= $numDiasTotal; $i++ )

               echo '
                       <tr><td colspan="3"><th>Total</th><th>'.$total.'</th><td></td></tr>
                    </tbody>
                    </table>
                      <p style="margin: 80px 40px"><a href="javascript:history.back()" class="botao">« voltar</a></p>
                   </div>
                   <p class="creditos">© 2019 Embrapa Arroz e  Feij&atilde;o Todos os direitos reservados</p>
                  </body>
                  </html>';


                     $textoExcel = $textoExcel . '<tr style="color:green; font-weight: bold; font-size:20px; background-color: lightgray"><td><center></center></td><td><center></center></td><td><center></center></td><td><center>Total</center></td><td><center>'.$total.'</center></td><td><center></center></td></tr>';
                     $textoExcel = $textoExcel . '</table></center></td></tr>';
                     $_SESSION['textoExcel']   = $textoExcel;


?>

