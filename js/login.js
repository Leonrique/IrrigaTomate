var xmlHttpObject = false;
//**********************************************************************************
function GetXmlHttpObject(user,senha) {


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
    xmlHttpObject.open('GET','login.php?user='+user+'&senha='+senha, true); //executa a chamada no servidor
    xmlHttpObject.send(null); // envia parametros ao servidor se for pelo metodo POST
    return false;
}
//**********************************************************************************
function mostraSaida() {
	if (xmlHttpObject.readyState == 4) {
	        if (xmlHttpObject.status == 200) {

                    texto = xmlHttpObject.responseText;
                    j = texto.indexOf("<login>");
                    s = "<login>".length;
                    i = texto.length;
                    texto2 = "";
                    for(k= j + s; k < i; k++)
                      texto2 = texto2 + texto.charAt(k);
                    texto2 = texto2 + "";
                    texto3 = "";
                    j = texto2.indexOf("</login>");
                    for(k= 0; k < j; k++)
                      texto3 = texto3 + texto2.charAt(k);
                    texto3 = texto3 + "";
                    texto3 = texto3.trim();
 
                    
        	    if(!isNaN(texto3)) {

                           sessionStorage.clear(); // limpa todos os dados armazenados com setItem/sessionStorage
                           sessionStorage.setItem("idUser", parseInt(texto3) );
                           idZ = parseInt(texto3); 
                           setTimeout(  function() { getDadosUser2(idZ);  setTimeout( function() {  window.location="planilhaIrrigacao.html"; } , 1000 ); }, 2);
 

             	   } else 
                        {

                          if(texto3 == 'B') {

                                  alert('codigo -> '+ texto3 +'\n\nO servidor de base de dados n\u00E3o est\u00E1 acess\u00EDvel!');

                          }
                          else 
                            if(texto3 == 'A') {

               		          alert('codigo -> '+ texto3 +'\n\nUsername ou senha n\u00E3o confere!');
                            }
                            else
                                if(texto3 == 'D') {

               		          alert('codigo -> '+ texto3 +'\n\nUsername ou senha com caracter inv\u00E1lido ou nulo!');

                                }
                                else
                                  if(texto3 == 'F') {

                                  alert('codigo -> '+ texto3 +'\n\nNenhum registro foi encontrado na base de dados!');

                                }

                        
             		} 
        	} else {
            		alert('Problema na requisi\u00E7\u00E3o!');
        	}
    } 
     return true;
}

