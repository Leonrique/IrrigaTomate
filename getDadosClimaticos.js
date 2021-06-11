function getDadosClimaticos(id, dia, mes, estacao) {
   if (window.XMLHttpRequest) {
      // FireFox, Mozilla, Safari,...
      xmlHttpObject = new XMLHttpRequest();
      if (xmlHttpObject.overrideMimeType) {
         xmlHttpObject.overrideMimeType("text/xml");
      }
   } else if (window.ActiveXObject) {
      // Internet Explorer
      try {
         xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP"); // Internet Explorer 5.5+
      } catch (e) {
         try {
            xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTP"); //Internet Explorer 5.5-
         } catch (e) {}
      }
   }
   if (!xmlHttpObject) {
      alert("Imposs\u00EDvel criar inst\u00E2ncia do objeto XMLHttpResquest.");
      return false;
   }

   xmlHttpObject.onreadystatechange = busca30anos; //especifica a fun..o JAVASCRIPT que recebera o resultado
   xmlHttpObject.open("GET", "getDadosClimaticos.php?id=" + id + "&dia=" + dia + "&mes=" + mes + "&estacao=" + estacao); // insercao dos parametros dia e mes
   xmlHttpObject.send(); // envia par.metros ao servidor se for pelo metodo POST
   return false;
}

function busca30anos() {
   if (xmlHttpObject.readyState == 4) {
      if (xmlHttpObject.status == 200) {
         newTexto = xmlHttpObject.responseText;
         j = newTexto.indexOf("<buscaDados>");
         s = "<buscaDados>".length;
         i = newTexto.length;
         newTexto2 = "";
         for (k = j + s; k < i; k++) newTexto2 = newTexto2 + newTexto.charAt(k);
         newTexto2 = newTexto2 + "";
         newTexto3 = "";
         j = newTexto2.indexOf("</buscaDados>");
         for (k = 0; k < j; k++) newTexto3 = newTexto3 + newTexto2.charAt(k);
         newTexto3 = newTexto3 + "";
         newTexto = newTexto3;

         if (newTexto.length > 3) {
            tabela =
               '<center><table class="resultado" border="0" cellpadding="0" cellspacing="0" align="center"><tr style=" background-color:#009900; color:white"><th>Dia</th><th>M&ecirc;s</th><th>Ano</th><th>Temp M&eacute;dia <br>(&#8451;)</th><th>Soma T&eacute;rmica  (&#8451;)</th><th>ETo (mm/dia)</th><th>Radia&ccedil;&atilde;o Solar (W/m<sup>2</sup>/dia)</th><th>Vel.vento (m/s)</th><th>Umidade Rel. do ar (%)</th><th>Estação</th></tr>';
            tabela = tabela + "<tbody>";
            tabela = tabela + "<colgroup>";
            tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
            tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
            tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
            tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
            tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
            tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
            tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
            tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
            tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
            tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
            tabela = tabela + "</colgroup>";
            results = newTexto.split("#");
            somaTermica = 0;
            for (i = 0; i < results.length; i++) {
               string = results[i].split("|");
               somaTermica = somaTermica + parseFloat(string[3]) - 10;
               tempMedia = parseFloat(string[3]);
               etoMedia = parseFloat(string[4]);
               tabela =
                  tabela +
                  "<tr><td>" +
                  string[0] +
                  "</td><td>" +
                  string[1] +
                  "</td><td>" +
                  string[2] +
                  '</td><td style=" padding-left:15px">' +
                  tempMedia.toFixed(2) +
                  "</td><td>" +
                  somaTermica.toFixed(2) +
                  "</td><td>" +
                  etoMedia.toFixed(2) +
                  "</td><td>" +
                  string[5] +
                  "</td><td>" +
                  string[6] +
                  "</td><td>" +
                  string[7] +
                  "</td><td>" +
                  string[8] +
                  "</td></tr>";
            }

            tabela = tabela + "</tbody></table></center>";
            document.getElementById("tabela").innerHTML = tabela;
         } else {
            if (newTexto.charAt(0) == "0") {
               tabela = '<center><table class="resultado"  border="0" cellpadding="0" cellspacing="0" align="center"><tr style=" background-color:#009900; color:white"><th>Dia</th><th>M&ecirc;s</th><th>Ano</th><th>Temp M&eacute;dia <br>(&#8451;)</th><th>Soma T&eacute;rmica  (&#8451;)</th><th>ETo (mm/dia)</th><th>Radia&ccedil;&atilde;o Solar (W/m<sup>2</sup>/dia)</th><th>Vel.vento (m/s)</th><th>Umidade Rel. do ar (%)</th><th>Estação</th></tr>';

               tabela = tabela + "<tbody>";
               tabela = tabela + "<colgroup>";
               tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
               tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
               tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
               tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
               tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
               tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
               tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
               tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
               tabela = tabela + '<col style="width: 10%; background-color:#e8f0d9;">';
               tabela = tabela + '<col style="width: 10%; background-color:#fff;">';
               tabela = tabela + "</colgroup>";
               tabela =
                  tabela +
                  "<tr><td><center>--</center></td><td><center>--</center></td><td><center>--</center></td><td><center>--</center></td><td><center>--</center></td><td><center>--</center></td><td><center>--</center></td><td><center>--</center></td><td><center>--</center></td></tr>";
               tabela = tabela + "</tbody></table></center>";
               document.getElementById("tabela").innerHTML = tabela;

               alert("A consulta retornou vazia. Tente dados de fevereiro a outubro!");
            } else if (newTexto.charAt(0) == "1") {
               alert("Houve algum problema ao acessar o servidor de base de dados do sistema. \nAcesso negado. ");
            } else if (newTexto.charAt(0) == "2") {
               alert("Houve algum problema ao acessar a base de dados do sistema. \nAcesso negado. ");
            } else if (newTexto.charAt(0) == "3") {
               alert("Houve algum problema ao acessar os dados para consulta na base de dados do sistema. ");
            }
         }
      } else {
         alert("Problema na requisi\u00E7\u00E3o!");
      }
   }
   return true;
}
