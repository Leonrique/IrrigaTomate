<?php

     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);
     include 'funcoesInmet.php';
     include 'getDadosNasa.php';
     include 'simple_html_dom.php';
     include 'funcoesUfg.php';
     include 'getDadosSimehgo.php';
     
     getDadosInmet();
     getDadosUfg();
     //===Sobre o Simego
      insertDadosSimehgo();
      updateDadosSimehgo();

?>
