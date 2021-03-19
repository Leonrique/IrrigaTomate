var xmlHttpObject = false;
//**********************************************************************************
function getDadosUser2(id) {

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
            } catch (e) { }
        }
    }
    if (!xmlHttpObject) {
        alert('Imposs\u00EDvel criar inst\u00E2ncia do objeto XMLHttpResquest.');
        return false;
    }

    xmlHttpObject.onreadystatechange = mostraSaidaId2; //especifica a funcao JAVASCRIPT que recebera o resultado
    xmlHttpObject.open('GET', 'getDadosUser.php?id=' + id, true); //executa a chamada no servidor
    xmlHttpObject.send(null); // envia parametros ao servidor se for pelo metodo POST
    return false;
}
//**********************************************************************************
function mostraSaidaId2() {
    if (xmlHttpObject.readyState == 4) {
        if (xmlHttpObject.status == 200) {
            
            var idCidadeZ = [];
            var identificacaoZ = [];
            var idPivotZ = [];
            var dataPlantioZ = [];
            var eficienciaZ = [];
            var laminaAplicadaZ = [];
            var tipoPlantioZ = [];
            var tipoSoloZ = [];
            var areaPivotZ = [];
            
            texto = xmlHttpObject.responseText;
            j = texto.indexOf("<id>");
            s = "<id>".length;
            i = texto.length;

            texto2 = "";
            for (k = j + s; k < i; k++)
                texto2 = texto2 + texto.charAt(k);
            texto2 = texto2 + "";
            texto3 = "";
            j = texto2.indexOf("</id>");
            for (k = 0; k < j; k++)
                texto3 = texto3 + texto2.charAt(k);
            texto3 = texto3 + "";
            texto3 = texto3.trim();

            sessionStorage.setItem("selectBoxOptions2", "");
            sessionStorage.setItem("colecaoId", "");
            colecaoId = "";
            if (texto3.length > 10) {

                results = texto3.split("#%&@1743@");
                numRegistrosZ = 0;
                opcoesId = "";

                for (i = 0; i < results.length; i++) {
                    
                    numRegistrosZ = numRegistrosZ + 1;
                    registro = results[i].split(";");
                    
                    if (numRegistrosZ > 1) {
                        opcoesId = opcoesId + ";" + registro[1];
                        colecaoId = colecaoId + ";" + registro[9];
                    }
                    else {
                        opcoesId = registro[1];
                        colecaoId = registro[9];
                    }
                    
                    t = numRegistrosZ;
                    registro = results[i].split( ";" );
                    idCidadeZ[t] = registro[0];
                    identificacaoZ[t] = registro[1];
                    idPivotZ[t] = registro[2];
                    dataPlantioZ[t] = registro[3];
                    eficienciaZ[t] = registro[4];
                    laminaAplicadaZ[t] = registro[5];
                    tipoPlantioZ[t] = registro[6];
                    tipoSoloZ[t] = registro[7];
                    areaPivotZ[t] = registro[8];
                }

                sessionStorage.setItem("selectBoxOptions2", opcoesId);
                sessionStorage.setItem("colecaoId", colecaoId);

                sessionStorage.setItem("numRegistrosZ", numRegistrosZ);
                sessionStorage.setItem("idCidadeZ", idCidadeZ);
                sessionStorage.setItem("identificacaoZ", identificacaoZ);
                sessionStorage.setItem("idPivotZ", idPivotZ);
                sessionStorage.setItem("dataPlantioZ", dataPlantioZ);
                sessionStorage.setItem("eficienciaZ", eficienciaZ);
                sessionStorage.setItem("laminaAplicadaZ", laminaAplicadaZ);
                sessionStorage.setItem("tipoPlantioZ", tipoPlantioZ);
                sessionStorage.setItem("tipoSoloZ", tipoSoloZ);
                sessionStorage.setItem("areaPivotZ", areaPivotZ);

            } 
            // else 
            // {
            //     if (texto3 == 'B') {
            //         alert('codigo -> ' + texto3 + '\n\nO servidor de base de dados n\u00E3o est\u00E1 acess\u00EDvel!');
            //     }
            //     else
            //         if (texto3 == 'A') {
            //             alert('codigo -> ' + texto3 + '\n\nUsername ou senha n\u00E3o confere!');
            //         }
            //         else
            //             if (texto3 == 'D') {
            //                 alert('codigo -> ' + texto3 + '\n\nUsername ou senha com caracter invalido ou nulo!');
            //             }
            //             else
            //                 if (texto3 == 'F') {
            //                     //alert('codigo -> '+ texto3 +'\n\nNenhum registro foi encontrado na base de dados!');
            //                 }
            // }
        } else {
            alert('Problema na requisi\u00E7\u00E3o!');
        }
    }
    return true;
}

