<?php
/**
 * @author Developer Erick Aguayo Romo
 * @copyright HAR INDUSTRIES 12/ 7/2020 14:2 eamerick66@gmail.com cel:963941338
 */
class erHisto extends eri {	
  public function _defecto(){
   $this->erAddPhp('erHisto_');
   //$this->eriGridRed();
  }
  public function erImpOrdMed(){
    $this->erAddPhp('erImpOrdMed');
  }
  public function erGEspe(){
  $iES=$_POST[iES];
  
  $this->erJE($this->toarray('select o.id,concat(o.nro_serie,"-",o.nro_comprob," ",ca.nom_cate) d from n3erseller_ord o,n3exis_cla_2cate ca where o.id_entsal_anex="'.$iES.'" and o.id_cate=ca.id'));
  return;
    $this->erJE( $this->toarray('select ca.id,ca.nom_cate from n3exis_entsal_prod d,n3exis_estilo_prod p,n3exis_estilo e,n3exis_cla_2cate ca  
    where d.id_entsal="'.$iES.'" and d.id_prod=p.id and p.id_estilo=e.id and e.id_cate=ca.id group by e.id_cate') );
  }
  public function erAutCate(){
   $this->erComplete('select nom_cate,id from n3exis_cla_2cate where nom_cate like "%'.$_REQUEST[q].'%" ORDER BY nom_cate');
  }
  public function erGFrm(){
  $iES=$_POST[iES];
  $erCab=$this->getRow('select * from n3exis_entsal c where c.id="'.$iES.'"');
  $d=explode(' ',$erCab[fecha_apro]);
  if(!strlen($d[0])||$d[0]=='0000-00-00')$d[0]=$this->erFhHoy('Y-m-d');
  if(!strlen($d[1]))$d[1]=$this->erFhHoy('H:m:s'); 
  $erCab[fecha_apro]=$this->cambiarFecha($d[0],'-','/');
  $erCab[hora]=$d[1];
  $erCab[fecha_emi]=$this->cambiarFecha($erCab[fecha_emi],'-','/');
  $d=$this->getRow('select c.nombres,c.ape_pat,c.ape_mat,c.nro_docum nro_doc,date_format(c.fecha_nac,"%d/%m/%Y") fecha_nac,
  concat(TIMESTAMPDIFF(YEAR,c.fecha_nac,"'.$this->erFhHoy('Y-m-d').'"),"A ",round((TIMESTAMPDIFF(MONTH,c.fecha_nac,"'.$this->erFhHoy('Y-m-d').'")/12-TIMESTAMPDIFF(YEAR,c.fecha_nac,"'.$this->erFhHoy('Y-m-d').'"))*12,0),"M") edad 
  from n3clie c where c.id="'.$erCab[id_clieprov].'"');
  $erCab=array_merge($erCab,is_array($d)?$d:array());
  $d=$this->getRow('select concat(u.nombres," ",u.ape_pat) nom from usuario_usuario u where u.id="'.$erCab[id_usuadd].'"');
  $erCab[nom_usu]=$d[nom];
  
  $erDet=$this->toarray('select d.id,e.nom_1,p.ersku,d.cn,d.pv,d.mo from n3exis_entsal_prod d,n3exis_estilo e,n3exis_estilo_prod p 
  where d.id_entsal="'.$iES.'" and d.id_prod=p.id and p.id_estilo=e.id');
  $erDia=$this->erGDia($iES); 
  $erExAu=$this->erGExAu($iES);  
  $erRec=$this->erGRec($iES);
  $erDig=$this->toarray('select d.id,d.nom_img,d.ext,d.fecha_add from n3exis_entsal_dig d where d.id_entsal="'.$iES.'"');
  
  $erGru=$this->toarray('select "" i,"SELECCIONE" d union all select g.id i,g.nom_grupo d from n3exis_cla_3grupo g 
  where g.id_sede="'.$erCab[id_sede].'" and g.id_cate="'.$erCab[id_cate].'" and !g.er_anulado');
  $erTur=$this->toarray('select "" i,"SELECCIONE" d union all select t.id i,t.nom_turno d from n3conf_turno t');
  //$erMed=$this->toarray('select from usuario_usuario u where u.id_gru');
  $this->erJE(array($erCab,$erDet,$erDia,$erExAu,is_array($erRec)?$erRec:array(),is_array($erDig)?$erDig:array(),$erGru,$erTur));
  }
  public function erDelS3($iHARDB=0,$erDes=''){
    include_once(ERREACTOR_PHP.'/erMas/s3.php');
     $r=$this->getRow('select * from sysdat_erdigdir limit 1');
    $s3=new S3($r[ers3_key],$r[ers3_secretkey]);    
    //echo 'erx92/v2/'.$iHARDB.'/'.$erDes;
   return $s3::deleteObject($r[ers3_cubo],'erx92/v2/'.$iHARDB.'/'.$erDes); 
  }
  public function erSavS3($erLisImg=array(),$iHARDB=0){
    $r=$this->getRow('select * from sysdat_erdigdir limit 1');
    include_once(ERREACTOR_PHP.'/erMas/s3.php');
    $s3=new S3($r[ers3_key],$r[ers3_secretkey]);
    $rp=array();
    if(count($erLisImg))foreach($erLisImg as $a){
        /*er_ruta_reddir con /*/
    $rp[]=$s3::putObject($s3::inputFile($a[tmp],false),$r[ers3_cubo],'erx92/v2/'.$iHARDB.'/'.$a[erRDes],$s3::ACL_PUBLIC_READ,array(),array(),$s3::STORAGE_CLASS_RRS);            
    }                  
  }
  
  public function erDelImg(){
  $iES=$_REQUEST[iES];$ext=$_REQUEST[ext];  
  $d=$this->getRow('select h.id from hargen h where h.nom_db="'.DB_NOMBD.'"',0,1);
  $iHARDB=$d[id];
  $this->exec('delete from n3exis_entsal_dig where id="'.$iES.'"');
  echo $this->erDelS3($iHARDB,'erESdig/'.$iES.'.'.$ext);
  }
  public function erSubImg(){
  $erMat=array();
     if(count($_FILES)){
   $iES=$_REQUEST[iES];  
  
  $erFile=$this->erSubirPrin(); 
   $ba=explode('.',basename($erFile[name]));
   $ext=$ba[count($ba)-1];
   
   $erMat=array(id_entsal=>$iES,nom_img=>$_REQUEST[nom_img],ext=>$ext,id_usuadd=>ID_USUARIO,fecha_add=>$this->erFhHoy());
  $iImg=$this->insert('n3exis_entsal_dig',$erMat,false,true); 
  $erMat[id]=$iImg;        
   
  $d=$this->getRow('select h.id from hargen h where h.nom_db="'.DB_NOMBD.'"',0,1);
  $iHARDB=$d[id];
  $erNom=$iImg;//$_POST[iMod].'-'.$_POST[iEs].'-'.$_POST[iCo];
  
  $erCar=NUCLEO_DIR.'/tmp/erESdig/';
  if(!is_dir($erCar))mkdir($erCar);
  $erImgDes=$erCar.$erNom.'.'.$ext;
  move_uploaded_file($erFile[tmp_name],$erImgDes);
   //echo $iHARDB.'  '.$erImgDes;
  $this->erSavS3(array(array(tmp=>$erImgDes,erRDes=>'erESdig/'.$erNom.'.'.$ext)),$iHARDB);
  //Manejo del cache
  /*$iDig=$this->insert('sysdat_erdig',array(id_emp=>ID_EMP,clasi=>$_POST[iMod],fecha=>$this->erFhHoy()),false,true);
  $this->exec('update n3exis_estilo_prod p set p.id_dig="'.$iDig.'" where p.id_estilo="'.$_POST[iEs].'" and p.id_color="'.$_POST[iCo].'"');*/
  if(is_file($erImgDes))unlink($erImgDes);
   
   }
  $this->erJE($erMat);
   // echo $iImg;
    //echo 5555;
  }
  public function erGEntSal(){
    $er=$_REQUEST[er];
  $erDet=$this->toarray('select d.cn,e.nom_1 from n3exis_entsal_prod d,n3exis_estilo e,n3exis_estilo_prod p where d.id_entsal="'.$er.'" and d.id_prod=p.id and p.id_estilo=e.id');
  $erEx=$this->toarray('select a_peact,a_talla,a_fc,a_fr,a_pa  from n3exis_entsal c where c.id="'.$er.'"');
  $this->erJE(array($erDet,$erEx)); 
  }
  public function erSavCo(){
   $c=$_POST[c];
   if($c[id]>0){
    $c[id_usumed]=ID_USUARIO;
    if(count($c))foreach($c as $i=>$a)$c[$i]=str_ireplace('"',"'",$a);
    $this->update('n3exis_entsal',$c,array(id=>$c[id]),false); 
   
   $erTabCie3='n3exis_entsal_cie3';
   if(count($_POST[erDiaAdd])){
    $erCamDia=array('id','id_cie3','tipo');
    foreach($_POST[erDiaAdd] as $a){
     $erDat=array();
      foreach($erCamDia as $i=>$nom)$erDat[$nom]=$a[$i];
      $erDat[id_entsal]=$c[id];
     if($erDat[id]>0){
       $this->update($erTabCie3,$erDat,array(id=>$erDat[id]),false);  
     }else{
      $this->insert($erTabCie3,$erDat,false);  
     }
   }}
   if(count($_POST[erDiaDel]))$this->exec('delete from '.$erTabCie3.' where id in ('.implode(',',$_POST[erDiaDel]).')');
   
   $erFeHoy=$this->erFhHoy();$erTabOrd='n3erseller_ord';$erTabOrdD='n3erseller_ord_prod';
   if($_POST[erExAuAdd]){    
    $erC=$this->getRow('select c.id,c.id_moneda,c.id_usumed,c.id_clieprov,c.id_gru,c.fecha_apro from n3exis_entsal c where c.id="'.$c[id].'"');
     foreach($_POST[erExAuAdd] as $iCat=>$erMat){
        $d=$this->getRow('select o.id,o.er_anulado from '.$erTabOrd.' o where o.id_entsal_anex="'.$erC[id].'" and o.id_cate="'.$iCat.'"');
         if($d[id]>0){
         $iOrd=$d[id]; 
         if($d[er_anulado]>0)$this->exec('update '.$erTabOrd.' set er_anulado="",fecha_anu=null where id="'.$iOrd.'"');   
        }else{
        $erSerie='OM'.substr($erC[fecha_apro],2,2).ID_SEDE;
            
        $d=$this->getRow('select max(cast(o.nro_comprob as decimal(10,0))) n from '.$erTabOrd.
        ' o where o.id_emp="'.ID_EMP.'" and  o.modo="2" and o.nro_serie="'.$erSerie.'" and year(o.fecha_emi)=year("'.$erC[fecha_apro].'")');
         //if($d[n]>=$erDat[nro_comprob])$erDat[nro_comprob]=$d[n]+1;            
         $iOrd=$this->insert($erTabOrd,array(id_emp=>ID_EMP,id_sede=>ID_SEDE,modo=>2,nro_serie=>$erSerie,nro_comprob=>$d[n]+1,
         id_moneda=>$c[id_moneda],id_clieprov=>$erC[id_clieprov],fecha_emi=>$erC[fecha_apro],id_usuadd=>$erC[id_usumed],fecha_add=>$erFeHoy,
         id_medpag=>1,id_cate=>$iCat,id_gru=>$erC[id_gru]/*consultorio proveniente*/,id_entsal_anex=>$erC[id]),false,true);   
        }       
       if(count($erMat))foreach($erMat as $m){
        //[g('id'),g('cn'),g('iEs'),g('id_prod'),g('iUni'),g('iMen'),iCat,g('iGru')]
            
       $this->insert($erTabOrdD,array(id_emp=>ID_EMP,id_sede=>ID_SEDE,id_ord=>$iOrd,modo=>2,id_estilo=>$m[2],id_co=>1,id_ta=>1,
       fecha_emi=>$erC[fecha_apro],//nro_serie=>$erSerie,nro_comprob=>0,
       id_prod=>$m[3],cn=>$m[1],cnu=>$m[1],id_unimed=>$m[4],id_moneda=>$erC[id_moneda],id_menu=>$m[5],id_cate=>$m[6],id_gru=>$m[7],
       id_entsal_anex=>$erC[id]),false);
        
       }  
        
     }
   }
   
   if(count($_POST[erExAuDel]))foreach($_POST[erExAuDel] as $id){
    $erOrd=$this->getRow('select d.id_ord from '.$erTabOrdD.' d where d.id="'.$id.'"');
    $this->exec('delete from '.$erTabOrdD.' where id="'.$id.'"');
    $d=$this->getRow('select count(*) n from '.$erTabOrdD.' d where d.id_ord="'.$erOrd[id_ord].'"');
    //echo 55;
    if($d[n]<=0)$this->exec('update '.$erTabOrd.' set er_anulado="1",fecha_anu="'.$erFeHoy.'" where id="'.$erOrd[id_ord].'"');
   }
   
   $erTabRec='n3exis_entsal_rece';   
   if(count($_POST[erRec]))foreach($_POST[erRec] as $a){
    $erDat=array(id_entsal=>$c[id],id=>$a[0],nom_1=>$a[1],cn=>$a[2],conc=>$a[3],form=>$a[4],dosis=>$a[5],id_int=>$a[6],id_via=>$a[7],
    dura=>$a[8],indadi=>$a[9]);
    if($erDat[id]>0){
      $this->update($erTabRec,$erDat,array(id=>$erDat[id]),false);  
    }else{
      $this->insert($erTabRec,$erDat,false);  
    }    
   }
   if(count($_POST[erRecDel]))$this->exec('delete from '.$erTabRec.' where id in ('.implode(',',$_POST[erRecDel]).')');
   
   
   //echo 'fgfg';
   }
   echo $c[id];
  }
  public function erSavDes(){
   $iES=$_POST[er]; 
    $this->erJE(array($this->erGDia($iES),$this->erGExAu($iES),$this->erGRec($iES)));
  }
  public function erBusProd(){
    //{5:'iEs',6:'iUni',7:'iMen',8:'iGru'}    
    $this->erComplete('select e.nom_1,p.id,ca.nom_cate,p.eralu,e.id_cate,e.id iEs,e.id_unimed,e.id_menu,e.id_gru from n3exis_estilo e,n3exis_estilo_prod p,n3exis_cla_2cate ca where e.nom_1 like "%'.$_REQUEST[q].'%" and e.id=p.id_estilo and e.id_cate=ca.id'.($_REQUEST[iCat]>0?' and e.id_cate="'.$_REQUEST[iCat].'"':''));
  }
  public function erBusCie2(){
    $this->erComplete('select c.nom_cate,c.id,c.cod from n3erclinic_confcie2 c where '.
    (stripos($_REQUEST[q],'.')?'c.cod':'c.nom_cate').' like "%'.$_REQUEST[q].'%"',1);    
  }
  public function erBusCie(){
  /*echo 'select c.nom_gru,c.id,c.cod from n3erclinic_confcie3 c where '.
    (stripos($_REQUEST[q],'.')>0?'c.cod':'c.nom_gru').' like "%'.$_REQUEST[q].'%"'.($_REQUEST[iCat]>0?' and c.id_cate="'.$_REQUEST[iCat].'"':''); **/ 
  
    $this->erComplete('select c.nom_gru,c.id,c.cod from n3erclinic_confcie3 c where '.
    (stripos($_REQUEST[q],'.')>0?'c.cod':'c.nom_gru').' like "%'.$_REQUEST[q].'%"'.($_REQUEST[iCat]>0?' and c.id_cate="'.$_REQUEST[iCat].'"':''),1);
  }
  public function erGMed(){
  $iGru=$_POST[iGru];
    $this->erJE($this->toarray('select "" i,"SELECCIONE" d union all select u.id i,concat(u.nombres," ",u.ape_pat," ",u.ape_mat) d from usuario_usuario u where u.id_gru="'.$iGru.'"'));
  }
  public function erGRec($iES){
  $erRec=$this->toarray('select r.id,r.nom_1,r.cn,r.conc,r.form,r.dosis,r.id_int,r.id_via,r.dura,r.indadi 
  from n3exis_entsal_rece r where r.id_entsal="'.$iES.'"');
  $erDat=array();
  $d=$this->toarray('select nro,id_value,descri from har_erp_tab13 c where c.nro in (14,15)',0,1);  
  if(count($d))foreach($d as $a){
    if(!$erDat[$a[nro]])$erDat[$a[nro]]=array();
  $erDat[$a[nro]][$a[id_value]]=$a[descri];  
  }
  if(count($erRec))foreach($erRec as $i=>$a){
   $erRec[$i][nom_inter]=$erDat[15][$a[id_int]];
   $erRec[$i][nom_via]=$erDat[14][$a[id_via]];
  }
  return $erRec;
  }
  public function erGDia($iES){
      $erDia=$this->toarray('select c.id,c.id_cie3,c.tipo from n3exis_entsal_cie3 c where c.id_entsal="'.$iES.'"');
  $erIdCie3=array();
  if(count($erDia)){
     foreach($erDia as $a)$erIdCie3[$a[id_cie3]]=$a[id_cie3];
    if(count($erIdCie3)){
     $erDatCie3=array();
     $d=$this->toarray('select c.id,c.cod,c.nom_gru from n3erclinic_confcie3 c where c.id in ('.implode(',',$erIdCie3).')',0,1);
     if(count($d))foreach($d as $a)$erDatCie3[$a[id]]=array($a[cod],$a[nom_gru]);
     }
   foreach($erDia as $i=>$a){
    $M=$erDatCie3[$a[id_cie3]];
    $erDia[$i][cod_cie]=$M[0];
    $erDia[$i][nom_cie]=$M[1];
    }
  }
  return $erDia;
  }
  public function erGExAu($iES=0){
    return $this->toarray('select d.id,d.cn,ca.nom_cate nom_cat,p.eralu ersku,e.nom_1  
  from n3erseller_ord_prod d,n3exis_estilo_prod p,n3exis_estilo e,n3exis_cla_2cate ca where d.id_entsal_anex="'.$iES.'" and d.id_prod=p.id and p.id_estilo=e.id and e.id_cate=ca.id');      
  }  
  public function erLisHis(){
   $iClie=$_REQUEST[iClie];
      $erHis=$this->toarray('select c.id,date_format(c.fecha_apro,"%d/%m/%Y") fecha_ate,
(select ca.nom_cate from n3exis_cla_2cate ca where ca.id=c.id_cate) nom_cate,
(select g.nom_grupo from n3exis_cla_3grupo g where g.id=c.id_gru) nom_gru,
(select concat(u.nombres," ",u.ape_pat) from usuario_usuario u where u.id=c.id_usuapro) nom_apro,
concat(ifnull((select substr(co.nom_comprob,1,3) from n3conf_comprob co where co.id_comprob=c.id_comprob limit 1),"")," ",c.nro_serie,"-",c.nro_comprob ) nom_com 
from n3exis_entsal c where c.id_clieprov="'.$iClie.'" and !c.er_anulado and c.id_comprob not in (7,8) order by c.fecha_apro desc');
  echo json_encode($erHis);
  }
  public function erSelCo(){
    $iES=$_POST[iES];
  $c=$this->getRow('select c.id,
  c.id_clieprov,
  c.a_moti,c.a_time_enf,c.a_ini_enf,c.a_cur_enf,c.a_rela,c.a_fc,c.a_pa,c.a_fr,c.a_taxi,c.a_trec,c.a_pehab,c.a_peact,c.a_talla,c.a_impgen,
  c.a_uesptemp,c.a_estnut,c.a_esthid,c.a_piel,c.a_cabcue,c.a_torax,c.a_mamas,c.a_apares,c.a_apacar,c.a_abdpel,c.a_exaobs,c.a_apagen,
  c.a_sisner,c.a_ostart,
  c.id_cate,(select substr(co.nom_comprob,1,3) from n3conf_comprob co where co.id_comprob=c.id_comprob) ncom,
  c.nro_serie ser,c.nro_comprob nro,date_format(date(c.fecha_apro),"%d/%m/%Y") fecha_apro,time(c.fecha_apro) hora,
  c.id_usuapro,c.id_gru,c.obs,c.id_turno,
  if(c.id_clieprov>0,(select if(cl.tip_clie="I",concat(cl.ape_pat," ",cl.ape_mat," ",cl.nombres),cl.razon_soc) from n3clie cl where cl.id=if(c.id_clieprov_>0,c.id_clieprov_,c.id_clieprov)),"") 
  raz,if(c.id_cate>0,(select ca.nom_cate from n3exis_cla_2cate ca where ca.id=c.id_cate),"") ncat from n3exis_entsal c where c.id="'.$iES.'"');
  $erGru=$this->toarray('select "" i,"SELECCIONE" d union all select g.id i,g.nom_grupo d from n3exis_cla_3grupo g where g.id_cate="'.$c[id_cate].'" and !g.er_anulado');
  $erTur=$this->toarray('select "" i,"SELECCIONE" d union all select t.id i,t.nom_turno d from n3conf_turno t');
  $erHis=$this->toarray('select c.id,date_format(c.fecha_apro,"%d/%m/%Y") fecha_ate,
(select ca.nom_cate from n3exis_cla_2cate ca where ca.id=c.id_cate) nom_cate,
(select g.nom_grupo from n3exis_cla_3grupo g where g.id=c.id_gru) nom_gru,
(select concat(u.nombres," ",u.ape_pat) from usuario_usuario u where u.id=c.id_usuapro) nom_apro,
concat(ifnull((select substr(co.nom_comprob,1,3) from n3conf_comprob co where co.id_comprob=c.id_comprob limit 1),"")," ",c.nro_serie,"-",c.nro_comprob ) nom_com 
from n3exis_entsal c where c.id_clieprov="'.$c[id_clieprov].'" and !c.er_anulado and c.id_comprob not in (7,8) order by c.fecha_emi desc');
  
  $erDia=$this->erGDia($iES);
  $erExAu=$this->erGExAu($iES);
  $erDig=$this->toarray('select d.id,d.nom_img,d.ext,d.fecha_add from n3exis_entsal_dig d where d.id_entsal="'.$iES.'"');
  //$erUsu=$this->toarray('select from usuario_usuario u where u.id_gru="'.$c.'"');
  $this->erJE(array($c,$erGru,$erTur,$erDia,$erExAu,$erHis,$erDig));
  }
  public function erCoBus(){
  $erICom=$_POST[id_comprob];
  $erSer=$_POST[nro_serie];
  $erNro=$_POST[nro_comprob];
  $iCat=$_POST[iCat];
  $erDat=array();
  
  if((strlen($erSer)||strlen($erNro))){
      $s='select c.id,
    (select m.nom_medpag from n3clie_pv_medpag m where m.id=c.id_medpag) nMedPag,
    (select ca.nom_cate from n3exis_cla_2cate ca where ca.id=c.id_cate limit 1) nCate,
    (select co.nom_comprob from n3conf_comprob co where co.id_comprob=c.id_comprob limit 1) ncom,
    c.nro_serie ser,c.nro_comprob nro,date_format(c.fecha_emi,"%d/%m/%Y") femi,
    date_format(c.fecha_apro,"%d/%m/%Y") fate,time(c.fecha_apro) hate,
    (select concat(u.nombres," ",u.ape_pat," ",u.ape_mat) from usuario_usuario u where u.id=c.id_usuapro) nom_medico,
    (select IF(cl.tip_clie="C",cl.razon_soc,trim(concat(cl.ape_pat," ",cl.ape_mat," ",cl.nombres)) ) from n3clie cl where cl.id=if(c.id_clieprov_>0,c.id_clieprov_,c.id_clieprov)) nom_clie,
    c.id_clieprov iClie  
    from n3exis_entsal c where c.id_sede="'.ID_SEDE.'" and c.id_comprob not in (7,8) and '.
    ' (isnull(c.cn_sal) or c.cn_sal<=0) and '.
    ($erICom>0?'c.id_comprob="'.$erICom.'"':'1').
    (strlen($erSer)?' and c.nro_serie="'.$erSer.'" ':'').
    (strlen($erNro)?' and c.nro_comprob="'.$erNro.'"':'').
    //(isset($_POST[iCat])?' and c.id_cate="'.$_POST[iCat].'"':'').
    ' limit 50';
  //echo $s;
  $erDat=$this->toarray($s);
  }
   
  $this->erJE($erDat);
  }
  public function erHistoFrCo(){
    $this->erAddPhp('erHistoFrCo');
  }
  public function erFrMov(){
    $this->erAddPhp('erHistoFr');
  }
  public function bus(){
    $this->erGrRB();    
  }    
  public function edit(){      
     $this->erGridRedSav();        
  }
}
?>