var xmlHttpObject = false;
//**********************************************************************************
function cadastro(nome, email, user, senha) {
 
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

    xmlHttpObject.onreadystatechange = mostraSaida; //especifica a funcao JAVASCRIPT que recebera o resultado
    xmlHttpObject.open('GET','cadastro.php?user='+user+'&senha='+senha+'&email='+email+'&nome='+nome, true); //executa a chamada no servidor
    xmlHttpObject.send(null); // envia parametros ao servidor se for pelo metodo POST
    return false;
}
//**********************************************************************************
function mostraSaida() {
	if (xmlHttpObject.readyState == 4) {
	        if (xmlHttpObject.status == 200) {

                    texto = xmlHttpObject.responseText;

                    j = texto.indexOf("<cadastro>");
                    s = "<cadastro>".length;
                    i = texto.length;
                    texto2 = "";
                    for(k= j + s; k < i; k++)
                      texto2 = texto2 + texto.charAt(k);
                    texto2 = texto2 + "";
                    texto3 = "";
                    j = texto2.indexOf("</cadastro>");
                    for(k= 0; k < j; k++)
                      texto3 = texto3 + texto2.charAt(k);
                    texto3 = texto3 + "";
                    texto3 = texto3.trim();

        	    if(texto3 == '4') {
			          alert("Cadastro realizado com sucesso!");
             	   } else 
                        {

                          if(texto3 == '1') {

                                alert('codigo -> '+ texto3 +'\n\nO servidor ou a base de dados n\u00E3o est\u00E1 acess\u00EDvel! \nTente mais tarde.');

                          }
                          else 
                            if(texto3 == '0') {

                                  alert('codigo -> '+ texto3 +'\n\nVerifica\u00E7\u00E3o dos dados falhou! \nPode ser erro de comando select ou dados estranhos foram passados!');

                            }
                            else
                                if(texto3 == '3') {

                                  alert('codigo -> '+ texto3 +'\n\nUsername, senha, e-mail ou nome com caracter inv\u00E1lido ou nulo!');

                                }
                                else
                                  if(texto3 == '5') {

                                      alert('codigo -> '+ texto3 +'\n\nEste username ou e-mail foi cadastrado anteriormente!');

                                  }
                                  else
                                    if(texto3 == '2') {

                                      alert('codigo -> '+ texto3 +'\n\nComando para inser\u00E7\u00E3o de dados falhou ou algum dado estranho foi passado!');

                                    }

                        
             		} 
        	} else {
            		alert('Problema na requisi\u00E7\u00E3o!');
        	}
    } 
     return true;
}

