<?php

     include 'pathConfig.php';
     $arquivoPath = configPath;
     include($arquivoPath);
     include 'funcoesInmet.php';
     include 'getDadosNasa.php';
     include 'simple_html_dom.php';
     include 'funcoesUfg.php';

     include 'getDadosSimehgo.php';

     include 'base30anos.php';
     include 'removeDadosRepetidosEstacoes.php';
     include 'cidadesSemEstacoes.php';
     include 'removeDadosRepetidos.php';

     //
     // iNSERIR DADOS DO inmet, ufg E Simehgo
     //
     getDadosInmet();
     getDadosUfg();

     //===Sobre o Simego
      insertDadosSimehgo();
      updateDadosSimehgo();

      //
      // Verificar dados faltantes de 
      // temperatura media e temperatura
      // de evapotranspiracao (ETC)

       base30anos();

       // remover dados duplicados das estacoes UFG, INMET e SIMEHGO

       removeDadosEstacoesFisicas();  //Remove todos os dados replicados das estacoes existentes


     //Inserir dados nas demais cidades
      // a partir dos dados do Inmet, UFG e Simehgo
      //
       cidadesSemEstacoes();

       // 
       // remover dados duplicados das demais estacoes

       removeDadosDuplicados();  //Remove todos os dados replicados das demais estacoes
?>
