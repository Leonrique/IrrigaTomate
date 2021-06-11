<?php

     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);

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

       $sql= "select id, cidadeHTML from  municipios  where idEstado = 9 and irrigacao = 1 order by cidade";
       $query = mysqli_query($conexao, $sql) ;
       if(!$query)
        {
          mysqli_close($conexao);
          echo "Erro em consulta a base de dados, a qual est&aacute; descrita a seguir.<br>";
          echo $sql;
          exit(1);
        }

      $numLinhas = $query->num_rows;
      if( $numLinhas == 0) {

             echo '
               <center>
               <table border=0>
                     <tr><td><br><h2  style="font-size:24px; color:green; font-weight:bold; margin-left:0px; margin-right: 0px"><center> Problemas de acesso &agrave; base de dados do sistema. Tente mais tarde. </center></h2></td></tr>
               </table>
               </center>
             ';
              mysqli_close($conexao);
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
                    <script type="text/javascript" src="getCidades.js"></script>
                    <script type="text/javascript" src="getTabela.js"></script>
                    <script type="text/javascript" src="getTabela2.js"></script>

                    <script src="https://code.jquery.com/jquery-1.8.2.js"></script>
                    <script src="https://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>


                     <script>

 
                      var mesInicial = 1; //Alterar, caso a data de inicio nao seja janeiro
                      var mesFinal = 12; //Alterar, caso a data de termino nao seja dezembro
                      var optEscolhido = 0;


                      function selectOptions30anos() {
            ';

              while( $linha=$query->fetch_row() ) {

                  $cidade = $linha[1];
                  $id = $linha[0];
                  echo '$("#cidade").append(`<option value="'.$id.' ">'.$cidade.'</option>`);';

              }// fim do while
                           
       echo '
                      } // final da funcao selectOptions30anos() {

                      function selectOptionsIntervalo() {
            ';
       $sql2 = "select distinct e.idCidade, m.cidadeHTML, m.cidade from municipios as m, evapoTranspiracaoTomateEstacao as e where m.idEstado = 9 and e.codEstacao is not null  and e.idCidade = m.id order by m.cidade";
       $query2 = mysqli_query($conexao, $sql2) ;
       while( $linha2=$query2->fetch_row() ) {

                  $cidade2 = $linha2[1];
                  $id2 = $linha2[0];
                  echo '$("#cidade").append(`<option value="'.$id2.' ">'.$cidade2.'</option>`);';

       }// fim do while
                         
       echo '
                      }  // final da funcao selectOptionsIntervalo()


                      function buscar() {


                          var cidade;
                          var flag = 1;
                        
                          var id = document.getElementById("cidade").selectedIndex;
                          var opcao = document.getElementById("cidade").options[id].value;
                          var cidade = id;
                          var idCidade = parseInt(opcao);
                          var radios = document.getElementsByName("optradio1");
                          var place =  document.getElementsByName("calendario");

                          for (var i = 0; i < radios.length; i++) {
                                                  if (radios[i].checked) {
                                                     optEscolhido = radios[i].value;
                                                  }
                          }
                          if( optEscolhido == 1) {

                                              document.getElementsByName("calendario")[0].placeholder = "dd/mm"
                                              document.getElementsByName("calendario2")[0].placeholder = "dd/mm"
                          }
                          else
                            if( optEscolhido == 2) {

                                                   document.getElementsByName("calendario")[0].placeholder = "dd/mm/aaaa"
                                                   document.getElementsByName("calendario2")[0].placeholder = "dd/mm/aaaa"

                            }
                            else {
                                                    alert("Escolha entre a m\u00e9dia de 30 anos ou os dados de alguma esta\u00e7\u00e3o do INMET, SIMEHGO ou UFG!");
                                                    flag = 0;
                                 }

                          if( flag == 1) {

                                     
                                if( document.getElementById("calendario") === undefined  || document.getElementById("calendario") === null ) {

                                    alert("Escolha uma data inicial.");
                                    flag = 0;
                                }
                                else
                                  if( document.getElementById("calendario").value == "" ) {

                                     alert("Escolha uma data inicial.");
                                     flag = 0;
                                 }
                                 else
                                   if( document.getElementById("calendario2") === undefined  || document.getElementById("calendario2") === null ) {

                                       alert("Escolha uma data final.");
                                       flag = 0;
                                  }
                                  else
                                    if( document.getElementById("calendario2").value == "" ) {
 
                                        alert("Escolha uma data final.");
                                         flag = 0;
                                    }
                                    else
                                      if( parseInt(opcao) == 0 ) {

                                          alert("Escolha o munic\u00EDpio.");
                                          flag = 0;

                                      }

                          }

 
                         if( flag == 1) { // caso tudo esteja certo, esta opcao vai chamar a funcao q constroi a tabela

                           var data1 = document.getElementById("calendario").value;
                           var data2 = document.getElementById("calendario2").value;
                           dia = data1.substring(0,2);
                           mes = data1.substring(3,5);
                           ano = data1.substring(6,10);
 
                           dia2 = data2.substring(0,2);
                           mes2 = data2.substring(3,5);
                           ano2 = data2.substring(6,10);
                             
                           if( ( parseInt(mes2) == parseInt(mes)  && parseInt(dia2) < parseInt(dia) &&  parseInt(ano2) == parseInt(ano) ) || (parseInt(ano2) < parseInt(ano) ) ) {

                               alert("A data inicial deve ser anterior a data final!");

                           }                               
                           else
                             if( ( parseInt(ano2) == parseInt(ano) && parseInt(mes2) < parseInt(mes)  )  ) {
                               alert("A data inicial deve ser anterior a data final!");
                             }
                             else                             
                               if( optEscolhido == 1)
                                  getTabela(idCidade, dia, mes, dia2, mes2)
                               else
                                 if( optEscolhido == 2)
                                    getTabela2(idCidade, dia, mes, ano, dia2, mes2, ano2);

                         }
 
                      }

                      function opcaoRadio() {


                          var radios = document.getElementsByName("optradio1");
                          var place =  document.getElementsByName("calendario");
                          var i;
                          var x = document.getElementById("cidade");
                          tam = x.length - 1;
 
                          for (i = 0; i < radios.length; i++) {
                                         if (radios[i].checked) {
                                             optEscolhido = radios[i].value;
                                         }
                          }
                          if( optEscolhido == 1) {

                                 document.getElementsByName("calendario")[0].placeholder = "dd/mm";
                                 document.getElementsByName("calendario2")[0].placeholder = "dd/mm";
                                 
                                 for (i = tam; i > 0;  i--) {
                                    x.remove(i);
                                 }
                                 selectOptions30anos();
                          }
                          else
                            if( optEscolhido == 2){
 
                                 document.getElementsByName("calendario")[0].placeholder = "dd/mm/aaaa";
                                 document.getElementsByName("calendario2")[0].placeholder = "dd/mm/aaaa";
                                 
                                 for (i = tam; i > 0;  i--) {
                                    x.remove(i);
                                 }
                                 selectOptionsIntervalo();

                            }
                      }
                      function verificaData() {

                         dataInicial = document.getElementById("calendario").value
                         dataInicial = dataInicial.trim();
                         var dia= parseInt(dataInicial.substring(0,2) );
                         var mes = parseInt(dataInicial.substring(3,5) );


                          if ( mes < mesInicial || mes > mesFinal)
                            alert("Escolha um m\u00EAs entre janeiro e dezembro.\nOs dias podem variar de 01/01 a 31/12,\nque s\u00E3o os dias recomendados. ");
                          else
                            if (mes == mesFinal && dia > 31)
                                alert("Para o m\u00EAs de dezembro, dia 31 seria o \u00FAltimo dia recomendado.");
                            else
                              if (mes == mesInicial && dia < 1)
                                alert("Para o m\u00EAs de janeiro, dia 01 seria o primeiro dia recomendado.");

                      }



                     $(function() {
                        // Ver em
                        // https://api.jqueryui.com/datepicker/
                        // para mais informacoes
                        //      
   
                       var d = new Date();
                       var mes = d.getMonth() + 1;
                       var dia = d.getDate() ;
                       var year = d.getYear() + 1900;
 
                       $("#calendario").datepicker({
                          dateFormat: "dd/mm/yy",
                          dayNames: ["Domingo","Segunda","Terca","Quarta","Quinta","Sexta","Sabado","Domingo"],
                          dayNamesMin: ["D","S","T","Q","Q","S","S","D"],
                          dayNamesShort: ["Dom","Seg","Ter","Qua","Qui","Sex","S&aacute;b","Dom"],
                          monthNames: ["Janeiro","Fevereiro","Mar&ccedil;o","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
                          monthNamesShort: ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
                          minDate: new Date(year, mesInicial - 1, 01),
                          //maxDate: new Date(year, mesFinal - 1, 20),
                          maxDate: new Date(year, mesFinal - 1, 31),
                          defaultDate: new Date(year, mesInicial - 1, 01),
                          altFormat: "dd/mm/yy",
                          duration: "fast", // fast, normal, slow
                          beforeShow: function(){    
                               $(".ui-datepicker").css("font-size", 12)
                          }

                       });

                       $("#calendario2").datepicker({
                          dateFormat: "dd/mm/yy",
                          dayNames: ["Domingo","Segunda","Terca","Quarta","Quinta","Sexta","Sabado","Domingo"],
                          dayNamesMin: ["D","S","T","Q","Q","S","S","D"],
                          dayNamesShort: ["Dom","Seg","Ter","Qua","Qui","Sex","S&aacute;b","Dom"],
                          monthNames: ["Janeiro","Fevereiro","Mar&ccedil;o","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro"],
                          monthNamesShort: ["Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez"],
                          minDate: new Date(year, mesInicial - 1, 01),
                          maxDate: new Date(year, mesFinal - 1, 20),
                          defaultDate: new Date(year, mesInicial - 1, 01),
                          altFormat: "dd/mm/yy",
                          duration: "fast", // fast, normal, slow
                          beforeShow: function(){    
                               $(".ui-datepicker").css("font-size", 12)
                          }

                       });

                     });
                     </script>

                     <style>

                      input[type=text] {
                                height: 30px;
                                width: 195;
                                border: 1px thin #000000;
                      }

 

                      .ui-datepicker {
                           width: 200px;
                           height: 170px;
                           //font-size:12px;
                           font-weight:bold;
                           font-family: Trebuchet MS, Tahoma, Verdana, Arial, sans-serif;
                      }

                    </style>

                 </head>
                 <!--body onload="setTimeout(  function() { getCidades(9) } , 200)"-->
                 <body>
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

                     
                      <table border=0 style="width:600px; margin-left:200px"><tr><td>
                        <p><span>Escolha uma das op&ccedil;&otilde;es abaixo:</span></p>
                        <p>

                             <input type="radio" name="optradio1" value = 1 id="media30anos" onClick="opcaoRadio()" > M&eacute;dia de 30 anos
                             <br>
                             <input type="radio" name="optradio1" value = 2 id="anual" onClick="opcaoRadio()" > Dados a partir de 2020 (esta&ccedil;&otilde;es do INMET, SIMEHGO e UFG)
                        </p>
                        <br>
                        <p><span>Escolha um intervalo de tempo</span></p>
                        <div id=tempo1>
                        <p>

                           <table border = 0 style="margin-left:45px;">
                            <tr><td>Data inicial:</td><td>
                                 <input type="text" id="calendario" name = "calendario" style="color:#669900; font-Weight:bold; padding-left:50px; height: 30px;"
                                              placeHolder=""  onChange="verificaData()" />
                            </td></tr>
                            <tr><td>Data final:</td><td>
                                 <input type="text" id="calendario2" name = "calendario2" style="color:#669900; font-Weight:bold; padding-left:50px; height: 30px;"
                                              placeHolder=""  />

                            </td></tr>
                           </table>
 
                        </p>
                        </div>
                            <br>
                            <p><span> Escolha um munic&iacute;pio: </span></p><select name="cidade" id="cidade"  style="HEIGHT: 32px; WIDTH: 193px; color:#669900; font-size: 12px; margin-left:40px" >
                              <option value =0 style="color:#000000 ; font-size: 14px;  height: 30px;" ><center> --escolha o munic&iacute;pio--</center></option>

                            </select>
                            <p style="margin: 80px 40px;margin-left:210px"><a href="javascript:buscar()" class="botao"> buscar</a></p>
                     </td></tr></table>

                     <div id="tabela">

                     </div>
                  </div>
            ';
             mysqli_close($conexao);

?>
