@php


if (isset($_GET['action'])  &&  $_GET['action']== 'export'){

  $excelfilename = "factures" . date('YmdHis') . '.xls';

header("Content-type: application/vnd.ms-excel");
header("Content-disposition: attachment; filename=$excelfilename");


  $columnName=['Periode', 'Achat',' HT',' TVA', 'AIB','AchatTTC'];
  $data =implode (glue:"\t", array_values ( $columnName)). "\n";
  
  
  if( $db->countBills() >0){
      $bills= $db ->read();
      foreach( $bills as $bill){
          $exceldata = [ $bill->id , $bill->Periode , $bill->Achat , $bill->HT , $bill->TVA , $bill->AIB , $bill->AchatTTC];
          $data * = implode(glue:"\t", $exceldata)."\n";
      }
}else{
          $data="aucune factures trouvees...."."\n"; 


      }

      echo $data;
      die();
}
@endphp