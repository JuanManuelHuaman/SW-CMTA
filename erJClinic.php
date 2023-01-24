<?php
/**
 * @author Developer Erick Aguayo Romo
 * @copyright HAR INDUSTRIES 4/ 7/2020 16:2 eamerick66@gmail.com cel:963941338
 */
class erJClinic extends eri {	
  public function _defecto(){
    $this->erAddPhp('erJClinicRepo_');
   //$this->eriGridRed();
  }    
  public function erGUApro(){
    $iGru=$_POST[iGru];
    $this->erJE($this->toarray('select "" i,"TODOS" d'.($iGru>0?' union 
    select u.id i,concat(u.nombres," ",u.ape_pat) d from usuario_usuario u where u.id_gru="'.$iGru.'"':'')));
  }
  public function erGGru(){
    $this->erJE($this->toarray('select "" i,"TODOS" d union select g.id i,g.nom_grupo d from n3exis_cla_3grupo g where g.id_sede="'.ID_SEDE.'" and g.id_cate="'.$_POST[iCat].'" and !g.er_anulado'));
  }
  public function erRepRece(){
    $this->erAddPhp('erRepRece');
  }
  public function erRepHisto(){
  $this->erAddPhp('erRepHisto');
  }      
  public function erRepParMed(){
    $this->erAddPhp('erRepParMed');
  }
  public function erRepTarifa(){
    $this->erAddPhp('erRepTarifa');
  }
  public function erRepProdTer(){
    $this->erAddPhp('erRepProdTer');
  }
  public function erRepPacBolEsp(){
   $this->erAddPhp('erRepPacBolEsp');  
  }
  public function erRepDiaXEsp(){
    $this->erAddPhp('erRepDiaXEsp');
  }
  public function erRepDiaXCaj(){
    $this->erAddPhp('erRepDiaXCaj');
  }
  public function erFrMov(){
    $this->erAddPhp('erJClinicFr');    
  }
   public function erPdfCab($pdf){
   
   if($pdf->noCab>0)return;
    
   $pdf->Image(SIS_BAS.'../erV3Img/'.DB_NOMBD.'/erLogo.png',5,5,60,20);
    
    $pdf->erPdfText($pdf->w-45,3,'%15',$this->cambiarFecha($this->erFhHoy(),'-','/'),'C','y',$bor,6,0,0,8,'');
   $y=$pdf->erPdfText(10,23,'%90',$pdf->erTit,'C','y',$bor,6,0,0,12,'B');
   if($pdf->erTit1)$y=$pdf->erPdfText(10,$y,'%90',$pdf->erTit1,'C','y',$bor,6,0,0,11,'');
   if($pdf->erTit1Der)$y=$pdf->erPdfText(10,$y,'%90',$pdf->erTit1Der,'L','y',$bor,6,0,0,9,'');
   $y=$pdf->yIni;
   $erFonCo=$pdf->erFonCo;$erFon=$pdf->erFon;$erFonN=$pdf->erFonN;
   switch($pdf->mo){
    case 1:
   $x=5;   
   $x=$pdf->erPdfText($x,$y,'%10','FECHA','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%25','CAJERO','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%15','MED.COBRO','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%20','COMPROBANTE','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%5','VA','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%5','AN','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%5','AT','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $y=$pdf->erPdfText($x,$y,'%10','TOTAL','C','y',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');  
    break;
    case 2:
   $x=5;   
   $x=$pdf->erPdfText($x,$y,'%10','FECHA','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%25','ESPECIALIDAD','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $y=$pdf->erPdfText($x,$y,'%15','CANTIDAD','C','y',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');    
    break;
    case 3:
   $x=5;   
   $x=$pdf->erPdfText($x,$y,'%8','FECHA','C','x',$bor,6,$erFonCo,$erFon,$erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%7','DOC','C','x',$bor,6,$erFonCo,$erFon,$erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%15','NOMBRE','C','x',$bor,6,$erFonCo,$erFon,$erFonN,'B');    
   $x=$pdf->erPdfText($x,$y,'%13','A.PATERNO','C','x',$bor,6,$erFonCo,$erFon,$erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%13','A.MATERNO','C','x',$bor,6,$erFonCo,$erFon,$erFonN,'B'); 
   $x=$pdf->erPdfText($x,$y,'%28','PRODUCTO','C','x',$bor,6,$erFonCo,$erFon,$erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%5','CAN','C','x',$bor,6,$erFonCo,$erFon,$erFonN,'B');
   $y=$pdf->erPdfText($x,$y,'%7','TOT S/','C','y',$bor,6,$erFonCo,$erFon,$erFonN,'B');   

    $pdf->SetFont('Arial','B',50);
    $pdf->SetTextColor(192,192,192);
     $pdf->erPdfRotar(implode('  ',array('C','O','P','I','A',' ','S','I','N',' ','V','A','L','O','R')),10,$pdf->h-30,50);                
    break;
    case 4:
   $x=5;   
   $x=$pdf->erPdfText($x,$y,'%5','ITEM','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%25','ESPECIALIDAD','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%10','ALU','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');    
   $x=$pdf->erPdfText($x,$y,'%35','DESCRIPCION','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%5','MON','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $y=$pdf->erPdfText($x,$y,'%10','PRECIO','C','y',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
    break;
    case 5:
   $x=5;   
   $x=$pdf->erPdfText($x,$y,'%3','N','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%2','T','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%3','SER','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');    
   $x=$pdf->erPdfText($x,$y,'%4','NRO','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%6','FATE','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%6','FPAG','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%10','MEDICO','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%2','TU','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%20','APE NOM','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');      
   $x=$pdf->erPdfText($x,$y,'%5','EDAD','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%19','DIAGNOSTICO','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');
   $x=$pdf->erPdfText($x,$y,'%15','PRODUCTO','C','x',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');   
   $y=$pdf->erPdfText($x,$y,'%3','CN','C','y',$bor,6,$pdf->erFonCo,$pdf->erFon,$pdf->erFonN,'B');    
    break;    
   }
       
  }
  public function bus(){
    $this->erGrRB();    
  }    
  public function edit(){      
     $this->erGridRedSav();        
  }
}
?>