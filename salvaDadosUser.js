var xmlHttpObject = false;
//**********************************************************************************
function salvaDadosUser(chuva,irrigacao ) {


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
        alert('Impossivel criar inst\u00E2ncia do objeto XMLHttpResquest.');
        return false;
    }

    xmlHttpObject.onreadystatechange = salvaDados; //especifica a funcao JAVASCRIPT que recebera o resultado
    xmlHttpObject.open('GET','salvaDadosUser.php?chuva='+chuva+'&irrigacao='+irrigacao, true); //executa a chamada no servidor
    xmlHttpObject.send(null); // envia parametros ao servidor se for pelo metodo POST
    return false;
}
//**********************************************************************************
function salvaDados() {
	if (xmlHttpObject.readyState == 4) {
	        if (xmlHttpObject.status == 200) {


                    texto = xmlHttpObject.responseText;

                    j = texto.indexOf("<dadosIrriga>");
                    s = "<dadosIrriga>".length;
                    i = texto.length;
                    texto2 = "";
                    for(k= j + s; k < i; k++)
                      texto2 = texto2 + texto.charAt(k);
                    texto2 = texto2 + "";
                    texto3 = "";
                    j = texto2.indexOf("</dadosIrriga>");
                    for(k= 0; k < j; k++)
                      texto3 = texto3 + texto2.charAt(k);
                    texto3 = texto3 + "";
                    texto3 = texto3.trim(); 
                 
        	    if(texto3.length  > 1) {

                      alert('codigo -> '+ texto3 +'\n\nErro inesperado ocorreu!');

             	   } else 
                        {

                          if(texto3 == '0') {

               		          alert('codigo -> '+ texto3 +'\n\nO servidor de base de dados n\u00E3o est\u00E1 acess\u00EDvel!');

                          }
                          else 
                            if(texto3 == '2') {

               		          alert('codigo -> '+ texto3 +'\n\nErro ao inserir dados de chuva!');
                            }
                            else
                                if(texto3 == '3') {

               		          alert('codigo -> '+ texto3 +'\n\nErro ao atualizar dados de chuva!');

                                }
                                else
                                  if(texto3 == '4') {

                                  alert('codigo -> '+ texto3 +'\n\nErro ao inserir dados de irriga\u00E7\u00E3o!');

                                }
                                else
                                  if(texto3 == '5') {

                                  alert('codigo -> '+ texto3 +'\n\nErro ao atualizar dados de irriga\u00E7\u00E3o!');

                                }
                                else 
                                  if(texto3 == '6') {

                                  alert('codigo -> '+ texto3 +'\n\n Usu\u00E1rio com tempo de acesso expirado ou inexistente.\nAcesse o sistema novamente!');

                                }

                        
             		} 
        	} else {
            		alert('Problema na requisi\u00E7\u00E3o!');
        	}
    } 
     return true;
}

