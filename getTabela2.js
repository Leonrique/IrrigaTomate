
        function getTabela2(id, dia, mes, ano, dia1, mes1, ano1) {

              if (window.XMLHttpRequest) { // FireFox, Mozilla, Safari,...
                  xmlHttpObject = new XMLHttpRequest();
                  if (xmlHttpObject.overrideMimeType) {
                     xmlHttpObject.overrideMimeType('text/xml');
                  }
              } else if (window.ActiveXObject) { // Internet Explorer
                    try {
                             xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP"); // Internet Explorer 5.5+
                        } catch (e) {
                                      try {
                                             xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTP"); //Internet Explorer 5.5-
                                          } catch (e) {}
                                    }
               }
              if (!xmlHttpObject) {
                        alert('Imposs\u00EDvel criar inst\u00E2ncia do objeto XMLHttpResquest.');
                        return false;
              }

              xmlHttpObject.onreadystatechange = buscaEstacoes; //especifica a fun..o JAVASCRIPT que recebera o resultado
              xmlHttpObject.open('GET','getTabela2.php?id=' + id + '&dia=' + dia + '&mes=' + mes + '&ano=' + ano + '&dia1=' + dia1 + '&mes1=' + mes1 + '&ano1=' + ano1); 
              xmlHttpObject.send(); // envia par.metros ao servidor se for pelo metodo POST
              return false;



        }
        function buscaEstacoes() {
        
           if (xmlHttpObject.readyState == 4) {
           
                if (xmlHttpObject.status == 200) {

                    newTexto = xmlHttpObject.responseText;
                    j = newTexto.indexOf("<buscaEstacoes>");
                    s = "<buscaEstacoes>".length;
                    i = newTexto.length;
                    newTexto2 = "";
                    for(k= j + s; k < i; k++)
                      newTexto2 = newTexto2 + newTexto.charAt(k);
                    newTexto2 = newTexto2 + "";
                    newTexto3 = "";
                    j = newTexto2.indexOf("</buscaEstacoes>");
                    for(k= 0; k < j; k++)
                      newTexto3 = newTexto3 + newTexto2.charAt(k);
                    newTexto3 = newTexto3 + "";
                    newTexto = newTexto3;
 
                    if(newTexto.length > 3 ) {


                             tabela = "<center><table class=\"resultado\"  border=\"0\" cellpadding=\"0\" cellspacing=\"0\" align=\"center\"><tr style=\" background-color:#009900; color:white\"><th>Dia</th><th>M&ecirc;s</th><th>Ano</th><th>ETC</th><th>Temp. M&eacute;dia</th><tr>";
                             tabela = tabela + "<tbody>";
                             tabela = tabela + "<colgroup>";
                             tabela = tabela + "<col style=\"width: 20%; background-color:#e8f0d9;\">";
                             tabela = tabela + "<col style=\"width: 20%; background-color:#fff;\">";
                             tabela = tabela + "<col style=\"width: 20%; background-color:#e8f0d9;\">";
                             tabela = tabela + "<col style=\"width: 20%; background-color:#fff;\">";
                             tabela = tabela + "<col style=\"width: 20%; background-color:#e8f0d9;\">";
                             tabela = tabela + "</colgroup>";
                             results = newTexto.split("#");
                             for( i = 0; i < results.length; i++ ) {

                                  string = results[i].split( "|" );
                                  tabela = tabela + "<tr><td>" + string[0] + "</td><td>" + string[1] + "</td><td>" + string[2] + "</td><td style=\" padding-left:15px\">" + string[3] + "</td><td>" + string[4] + "</td></tr>" ;
        
                             }
                             tabela = tabela + "</tbody></table></center>";
                             document.getElementById("tabela").innerHTML = tabela;

                    }
                    else {
                           if( newTexto.charAt(0) == '0' ) {

                                alert('A consulta retornou vazia. Tente dados de fevereiro a outubro!');
                           }
                           else
                             if( newTexto.charAt(0) == '1' ) {
                              alert('Houve algum problema ao acessar o servidor de base de dados do sistema. \nAcesso negado. ');
                             }
                             else
                               if( newTexto.charAt(0) == '2' ) {
                                  alert('Houve algum problema ao acessar a base de dados do sistema. \nAcesso negado. ');
                               }
                               else
                                 if( newTexto.charAt(0) == '3' ) {
                                    alert('Houve algum problema ao acessar os dados para consulta na base de dados do sistema. ');
                                 }

                        }
                } else {
                        alert('Problema na requisi\u00E7\u00E3o!');
                  }

           }
           return true;
        }
