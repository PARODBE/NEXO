<?php
require_once __DIR__.'/includes/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='POST'){http_response_code(405);exit('Método no permitido.');}
verify_csrf();$user=require_login();$id=filter_var($_POST['configuration_id'] ?? null,FILTER_VALIDATE_INT) ?: null;$return='dashboard.php';
try{
 $repo=new BusinessRepository(db());$action=post_text('action');
 if($action==='configuration_save' || $action==='configuration_delete') {
   if($id && !$repo->configuration($id,(int)$user['id'])){http_response_code(403);exit('No puedes modificar este equipo.');}
 }
 if($action==='configuration_save'){
   $data=['name'=>normalize_text(post_text('name')),'objective'=>post_text('objective'),'sector'=>post_text('sector'),'plan'=>post_text('plan')];
   plain_length($data['name'],3,100,'El nombre del equipo');plain_length($data['objective'],20,2000,'El objetivo');
   if(!isset(sector_labels()[$data['sector']]) || !isset(plan_definitions()[$data['plan']]))throw new InvalidArgumentException('Selecciona un sector y un plan válidos.');
   $raw=$_POST['agents'] ?? [];
   if(!is_array($raw))throw new InvalidArgumentException('La selección de agentes no es válida.');
   $agentIds=[];foreach($raw as $value){$agentId=filter_var($value,FILTER_VALIDATE_INT);if(!$agentId || $agentId<1)throw new InvalidArgumentException('La selección de agentes no es válida.');$agentIds[]=$agentId;}
   if(count($agentIds)!==count(array_unique($agentIds)))throw new InvalidArgumentException('No repitas agentes en el mismo equipo.');
   if(count($agentIds)<1 || count($agentIds)>plan_definitions()[$data['plan']]['limit'])throw new InvalidArgumentException('Elige entre 1 y '.plan_definitions()[$data['plan']]['limit'].' agentes para este plan.');
   $id=$repo->saveConfiguration((int)$user['id'],$data,$agentIds,$id);
   unset($_SESSION['configuration_old']);flash('success','Equipo guardado. Puedes revisarlo o utilizarlo para solicitar una propuesta.');
   $return='builder.php?id='.$id;
 }elseif($action==='configuration_delete'){
   if(!$id || post_text('confirmation')!=='ELIMINAR')throw new InvalidArgumentException('Escribe ELIMINAR para confirmar el borrado.');
   $repo->deleteConfiguration($id,(int)$user['id']);flash('success','Equipo eliminado. Las solicitudes enviadas conservan sus datos.');
 }elseif($action==='request_create'){
   $data=['configuration_id'=>$id,'company'=>normalize_text(post_text('company')),'contact_email'=>post_text('contact_email'),'sector'=>post_text('sector'),'objective'=>post_text('objective'),'plan'=>post_text('plan'),'message'=>post_text('message')];
   plain_length($data['company'],2,120,'El nombre de la empresa');plain_length($data['objective'],20,2000,'El objetivo');plain_length($data['message'],0,2000,'El mensaje');
   if(!filter_var($data['contact_email'],FILTER_VALIDATE_EMAIL) || mb_strlen($data['contact_email'])>190)throw new InvalidArgumentException('Introduce un correo de contacto válido.');
   if(!isset(sector_labels()[$data['sector']]) || !isset(plan_definitions()[$data['plan']]))throw new InvalidArgumentException('Selecciona un sector y un plan válidos.');
   if($id && !$repo->configuration($id,(int)$user['id'])){http_response_code(403);exit('No puedes utilizar este equipo.');}
   $requestId=$repo->createRequest((int)$user['id'],$data);unset($_SESSION['request_old']);
   flash('success','Solicitud #'.$requestId.' registrada en tu espacio. Es una demostración académica: no se ha cobrado ni enviado un correo.');
 }else throw new InvalidArgumentException('Acción desconocida.');
}catch(InvalidArgumentException $error){
 flash('error',$error->getMessage());
 if(($action ?? '')==='configuration_save'){
   $_SESSION['configuration_old']=array_filter($_POST,'is_string');
   $_SESSION['configuration_old']['agents']=is_array($_POST['agents'] ?? null)?array_values(array_filter($_POST['agents'],'is_scalar')):[];
   $return='builder.php'.($id?'?id='.$id:'');
 }
 elseif(($action ?? '')==='request_create'){$_SESSION['request_old']=array_filter($_POST,'is_string');$return='request.php'.($id?'?configuration='.$id:'');}
 elseif(($action ?? '')==='configuration_delete' && $id)$return='builder.php?id='.$id;
}catch(PDOException $error){error_log($error->getMessage());flash('error','No se pudo guardar el cambio. Comprueba MySQL e inténtalo de nuevo.');}
redirect($return);
