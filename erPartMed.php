<?php

/**
 * @author eamerick66
 * @copyright 2020
 */

class erPartMed extends eri{
  public function _defecto(){
    $this->erAddPhp('erPartMed_');
  }

  public function bus(){
    $cond=$this->erGrRBCond();      
     $tab='n3exis_entsal';
     
     $erUsu=$this->getRow('select g.id_cate from usuario_usuario u,n3exis_cla_3grupo g where u.id="'.ID_USUARIO.'" and u.id_gru=g.id');
     $erCondFija=' (isnull(c.cn_sal) or c.cn_sal<=0) and c.id_sede="'.ID_SEDE.'" and !c.er_anulado and c.id_cate="'.$erUsu[id_cate].'" '.($_REQUEST[iGru]>0?'and c.id_gru="'.$_REQUEST[id_gru].'"':'').' 
      and date(c.fecha_apro) between "'.$this->cambiarFecha($_REQUEST[fini]).'" and "'.$this->cambiarFecha($_REQUEST[ffin]).'" and c.modo=2 and c.modo_det=1'.
      ($_REQUEST[id_usumed]>0?' and c.id_usuapro="'.$_REQUEST[id_usumed].'"':'').
      ($_REQUEST[iTurno]>0?' and c.id_turno="'.$_REQUEST[iTurno].'"':'');
      
     $erCab='select c.id,concat(c.nro_serie," ",c.nro_comprob) nom_comprob,c.fecha_emi,c.fecha_apro,c.id_medpag,if(c.id_clieprov_>0,c.id_clieprov_,c.id_clieprov) id_clieprov,c.id_usuapro id_usumed,c.id_turno 
      from '.$tab.' c where '.$erCondFija.' and '.$cond;  
      //echo $erCab;
     $n=$this->getRow('select count(*) n from ('.$erCab.') c');
     //$n=$n[n];
     
     $tab_=$tab.'_';
     $iniLim=$this->erGrRBPag($n,$_REQUEST[p],$_REQUEST[l]);
    //$s='create temporary table '.$tab_.' '.$erCab.$iniLim;
    //echo $s;
    
    $this->exec('create temporary table '.$tab_.' '.$erCab.$iniLim);
   /* echo 'alter table '.$tab_.' add nom_medpag varchar(128) after fecha_emi,
  add nom_clie varchar(128) after fecha_apro,add edad varchar(128) after nom_clie,
  add sexo varchar(56) after edad,add nom_usu varchar(128) after sexo,
  add nom_turn varchar(128) after nom_usu';*/
  //add sexo varchar(56) after edad,
  $this->exec('alter table '.$tab_.' add nom_medpag varchar(128) after fecha_emi,
  add nom_clie varchar(128) after fecha_apro,
  add edad varchar(128) after nom_clie,  
  add nom_usu varchar(128) after edad,
  add nom_turn varchar(128) after nom_usu,
  add fecha_nac date after id_turno');
  
$this->exec('update '.$tab_.' t,n3clie c set t.nom_clie=if(c.tip_clie="I",concat(c.nombres," ",c.ape_pat," ",c.ape_mat),c.razon_soc),
t.edad=TIMESTAMPDIFF(YEAR,c.fecha_nac,"'.$this->erFhHoy('Y-m-d').'"),t.fecha_nac=c.fecha_nac where t.id_clieprov=c.id');
$this->exec('update '.$tab_.' t set t.edad=concat(t.edad,"A ",round((TIMESTAMPDIFF(MONTH,t.fecha_nac,"'.$this->erFhHoy('Y-m-d').'")/12-t.edad)*12,0),"M")');
$this->exec('update '.$tab_.' t,n3clie_pv_medpag m set t.nom_medpag=m.nom_medpag where t.id_medpag=m.id');
$this->exec('update '.$tab_.' t,usuario_usuario u set t.nom_usu=concat(u.nombres," ",u.ape_pat) where t.id_usumed=u.id');
$this->exec('update '.$tab_.' t,n3conf_turno tu set t.nom_turn=tu.nom_turno where t.id_turno=tu.id');
   //echo 45;  
     $c=$_REQUEST[l];
  if($c)$lim=$c;
    
  $c=$_REQUEST[p];
  if($c)$pag=$c;
   
  $c=$_REQUEST[s];
  if($c)$sor=$c; 
  $c=$this->getRow('select count(*) n from '.$tab.' c where '.$erCondFija);
 $_erPag=$this->erGrRBPag($c,$pag,$lim,1);
 
       $r[page]=$_erPag[pag];
      $r[total]=$_erPag[tp];
      $r[records]=$_erPag[n];
      $grupo=array();            
      if($_erPag[n]){
        $c=$this->toarray('select * from '.$tab_,MYSQL_NUM);  
        if(is_array($c))if(count($c))foreach($c as $n=>$v){
	   	$r[rows][$n]['id']=$v[0];
        $r[rows][$n]['cell']=$v;

        }
      if($esGrupo)$r[userdata]=$grupo;
      }
      /*return;*/
      
    echo json_encode($r);    
    
  }  
}
?>