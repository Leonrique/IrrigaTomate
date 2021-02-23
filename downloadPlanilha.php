<?php

/*
* Criando e exportando planilhas do Excel
* /
*/
// Definimos o nome do arquivo que seráxportado
session_start();
$arquivo = 'planilha.xls';

// Configuraçs header para forç o download
header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header ("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");
header ("Content-type: application/x-msexcel");
//header ("Content-type: text/plain");
header ("Content-Disposition: attachment; filename=\"{$arquivo}\"" );
header ("Content-Description: PHP Generated Data" );

// Envia o conteúo arquivo
echo $_SESSION['textoExcel'];
session_destroy();
exit;

?>
