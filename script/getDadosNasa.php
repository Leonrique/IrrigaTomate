<?php

function curl_get_contents_nasa($url)
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  //
  // Verificar se a opcao abaixo eh para o chrome ou o firefox. Esta setado para chrome
  //
  //curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
  curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36\r\n');
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
 
function getvarNasa($dia, $lat, $lon) {

 
          $nasa = "https://power.larc.nasa.gov/cgi-bin/v1/DataAccess.py?&request=execute&identifier=SinglePoint&parameters=T2M,PRECTOT,RH2M,WS2M,ALLSKY_SFC_SW_DWN&startDate=$dia&endDate=$dia&userCommunity=AG&tempAverage=DAILY&outputList=ASCII&lat=$lat&lon=$lon";
//                 https://power.larc.nasa.gov/cgi-bin/v1/DataAccess.py?&request=execute&identifier=SinglePoint&parameters=T2M,PRECTOT,RH2M,WS2M,ALLSKY_SFC_SW_DWN&startDate=20200120&endDate=20200120&userCommunity=AG&tempAverage=DAILY&outputList=ASCII&lat=-14.1305&lon=-47.51
          $a = curl_get_contents_nasa($nasa);
          if( is_null($a) ) {

            //echo "O valor de a eh nulo ";           
            $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0";
            return($resultado);
          }
          else
             if( strlen($a) == 0 ) {

               echo "O valor de a eh vazio ";          
               $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0"; 
               return($resultado);

             }
 
          $obj = json_decode($a);
          if( is_null($obj)) {

            echo "O valor de obj eh nulo";
            $resultado = "-99.0,-99.0,-99.0,-99.0,-99.0";
            return($resultado);
          }

 
 
          $data1 = $dia;

          if(  isset ($obj->features[0]->properties->parameter->ALLSKY_SFC_SW_DWN->$data1 ) ) {

               $radSol = $obj->features[0]->properties->parameter->ALLSKY_SFC_SW_DWN->$data1; // Em MJ/m2
               if(is_null($radSol) )
                   $radSol = -99.0;
               else
                  if(  $radSol < -50 || strlen($radSol) == 0 || ! is_numeric($radSol) ) {
                        $radSol = -99.0;
                  }
                  else
                     if( $radSol > -50 ) {
                         $radSol = $radSol * 1000000.0 / (24.0*60.0*60.0); // MJ/m2 para w/m2
                     }

          }
          else
              $radSol = -99.0;
 
          if(  isset ($obj->features[0]->properties->parameter->WS2M->$data1 ) ){

               $velVento = $obj->features[0]->properties->parameter->WS2M->$data1;
               if(is_null($velVento) )
                   $velVento = -99.0;
               else
                  if(  $velVento < -50 || strlen($velVento) == 0 || ! is_numeric($velVento) ) {
                        $velVento = -99.0;
                  }
 
          }
          else
              $velVento = -99.0;

          if(  isset ($obj->features[0]->properties->parameter->T2M->$data1 ) ) {

               $tempAr = $obj->features[0]->properties->parameter->T2M->$data1;
               if(is_null($tempAr) )
                   $tempAr = -99.0;
               else
                  if(  $tempAr < -50 || strlen($tempAr) == 0 || ! is_numeric($tempAr) ) {
                        $tempAr = -99.0;
                  }
 
          }
          else
              $tempAr = -99.0;

          if(  isset ($obj->features[0]->properties->parameter->RH2M->$data1 ) ) {

               $umidRel = $obj->features[0]->properties->parameter->RH2M->$data1;
               if(is_null($umidRel) )
                   $umidRel = -99.0;
               else
                  if(  $umidRel < -50 || strlen($umidRel) == 0 ||   ! is_numeric($umidRel) ) {
                        $umidRel = -99.0;
                  }
 
          }
          else
              $umidRel = -99.0;   
       
          if( $tempAr >= -40 && $radSol >= 0 && $velVento >= 0 && $umidRel >= 0 ) {

                 $eto1 = pow(10, ((7.5*$tempAr)/(237.3+$tempAr)) );
                 $eto2 = pow( ($tempAr +237.3), 2);
                 $etoX=((0.408*((4098*(0.6108*$eto1))/$eto2)*$radSol*0.55*0.0864)+(0.063*900*$velVento*((0.6108*$eto1)-(((0.6108*$eto1)*$umidRel)/100)))/($tempAr+275))/(((4098*(0.6108*$eto1))/$eto2)+0.063*(1+0.34*$velVento));

          } 
          else
             $etoX = -99.0;

          $resultado = "$tempAr,$etoX,$velVento,$umidRel,$radSol";
          //echo " <br> $resultado <br>";

          return($resultado);

} //function getvarNasa($dia, $lat, $lon) {

?>
