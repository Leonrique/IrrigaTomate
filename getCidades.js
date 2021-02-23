function htmlEntities(str) {
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

        function getCidades(id) {

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

              xmlHttpObject.onreadystatechange = municipiosNew; //especifica a fun..o JAVASCRIPT que recebera o resultado
              xmlHttpObject.open('GET','municipios2.php?id=' + id); 
              xmlHttpObject.send(); // envia par.metros ao servidor se for pelo metodo POST
              return false;



        }
        function municipiosNew() {
        
           if (xmlHttpObject.readyState == 4) {
           
                if (xmlHttpObject.status == 200) {

                    newTexto = xmlHttpObject.responseText;
                    j = newTexto.indexOf("<municipio>");
                    s = "<municipio>".length;
                    i = newTexto.length;
                    newTexto2 = "";
                    for(k= j + s; k < i; k++)
                      newTexto2 = newTexto2 + newTexto.charAt(k);
                    newTexto2 = newTexto2 + "";
                    newTexto3 = "";
                    j = newTexto2.indexOf("</municipio>");
                    for(k= 0; k < j; k++)
                      newTexto3 = newTexto3 + newTexto2.charAt(k);
                    newTexto3 = newTexto3 + "";
                    newTexto = newTexto3;

                    if(newTexto.length > 3 ) {

 
                             results = newTexto.split("#");
                             for( i = 0; i < results.length; i++ ) {

                                  string = results[i].split( "|" );
 
 
                                  if( string[1] !== undefined ) {
                                    string[1] = string[1].trim();
                                    n = string[1].search(/;/);
                                    if(n >= 0) {
 
                                       string[1] = string[1].replace(/[;]/ig, "\;");
                                       string[1] = string[1].replace(/[&]/ig, "\&");
                                    }
                                  }
 
                                  //document.getElementById("cidade").options[i + 1] = new Option( string[1], string[0] );
                                  if(string[1].length > 0)
                                     $('#cidade').append(`<option value='${string[0]}'>${string[1]}</option>`);
        
                             }
                             document.getElementById("cidade").options.length = results.length + 1;

                    }
                    else {
                           if( newTexto.charAt(0) == '0' ) {

                                alert('A consulta retornou nenhum munic\u00EDpio.');
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
