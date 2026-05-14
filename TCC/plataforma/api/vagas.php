<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/router.php';
setCors();
$action = param('action');
$id     = (int)param('id');

// LISTA DE VAGAS
if (isGet() && !$action && !$id) {
    $db=$db=getDB(); $where=['v.ativa=1']; $vals=[]; $types='';
    if ($q=param('q'))         { $like='%'.$q.'%'; $where[]='(v.titulo LIKE ? OR v.stack LIKE ? OR u.nome LIKE ?)'; $vals[]=$like;$vals[]=$like;$vals[]=$like; $types.='sss'; }
    if ($n=param('nivel'))     { $where[]='v.nivel=?'; $vals[]=$n; $types.='s'; }
    if ($e=param('espec'))     { $where[]='v.especialidade=?'; $vals[]=$e; $types.='s'; }
    if ($m=param('modelo'))    { $where[]='v.modelo=?'; $vals[]=$m; $types.='s'; }
    if ($eid=(int)param('empresa_id')) { $where[]='v.empresa_id=?'; $vals[]=$eid; $types.='i'; }
    $sql='SELECT v.*,u.nome AS empresa,u.photo_url AS empresa_logo FROM vagas v JOIN usuarios u ON u.id=v.empresa_id WHERE '.implode(' AND ',$where).' ORDER BY v.criado_em DESC';
    $stmt=$db->prepare($sql); if($types) $stmt->bind_param($types,...$vals); $stmt->execute();
    $vagas=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach($vagas as &$v) { $v['stack']=$v['stack']?array_map('trim',explode(',',$v['stack'])):[];  $v['total_cands']=candCount($v['id']); }
    jsonOut($vagas);
}
// UMA VAGA
if (isGet() && !$action && $id) {
    $db=getDB(); $stmt=$db->prepare('SELECT v.*,u.nome AS empresa FROM vagas v JOIN usuarios u ON u.id=v.empresa_id WHERE v.id=? LIMIT 1');
    $stmt->bind_param('i',$id); $stmt->execute(); $v=$stmt->get_result()->fetch_assoc();
    if(!$v) jsonOut(['erro'=>'Vaga não encontrada.'],404);
    $v['stack']=$v['stack']?array_map('trim',explode(',',$v['stack'])):[];
    $v['total_cands']=candCount($v['id']); jsonOut($v);
}
// CRIAR VAGA
if (isPost() && !$action) {
    $auth=requireEmpresa(); $b=bodyJson(); $db=getDB();
    $titulo=clean($b['titulo']??''); $espec=clean($b['espec']??''); $nivel=clean($b['nivel']??'');
    $stack=clean($b['stack']??''); $desc=clean($b['descricao']??''); $sal=clean($b['salario']??'');
    $mod=clean($b['modelo']??'Remoto'); $local=clean($b['local']??'');
    if(!$titulo||!$espec||!$nivel) jsonOut(['erro'=>'Título, especialidade e nível obrigatórios.'],400);
    $stmt=$db->prepare('INSERT INTO vagas (empresa_id,titulo,especialidade,nivel,stack,descricao,salario,modelo,local) VALUES (?,?,?,?,?,?,?,?,?)');
    $stmt->bind_param('issssssss',$auth['id'],$titulo,$espec,$nivel,$stack,$desc,$sal,$mod,$local); $stmt->execute();
    jsonOut(['ok'=>true,'id'=>$db->insert_id],201);
}
// REMOVER VAGA
if (isDelete() && !$action && $id) {
    $auth=requireEmpresa(); $db=getDB();
    $stmt=$db->prepare('UPDATE vagas SET ativa=0 WHERE id=? AND empresa_id=?');
    $stmt->bind_param('ii',$id,$auth['id']); $stmt->execute(); jsonOut(['ok'=>true]);
}
// CANDIDATAR
if (isPost() && $action==='candidatar') {
    $auth=requireDev(); $db=getDB();
    $chk=$db->prepare('SELECT id FROM vagas WHERE id=? AND ativa=1 LIMIT 1');
    $chk->bind_param('i',$id); $chk->execute();
    if(!$chk->get_result()->fetch_assoc()) jsonOut(['erro'=>'Vaga não encontrada.'],404);
    $score=clean((string)((bodyJson())['score']??'0%'));
    $stmt=$db->prepare('INSERT IGNORE INTO candidaturas (usuario_id,vaga_id,score) VALUES (?,?,?)');
    $stmt->bind_param('iis',$auth['id'],$id,$score); $stmt->execute();
    if($db->affected_rows===0) jsonOut(['erro'=>'Você já se candidatou a esta vaga.'],409);
    $vaga=$db->query("SELECT titulo,empresa_id FROM vagas WHERE id=$id")->fetch_assoc();
    if($vaga) { $msg="Olá! Me candidatei à vaga \"{$vaga['titulo']}\". Meu match é $score."; $ins=$db->prepare('INSERT INTO mensagens (de_id,para_id,texto) VALUES (?,?,?)'); $ins->bind_param('iis',$auth['id'],$vaga['empresa_id'],$msg); $ins->execute(); }
    jsonOut(['ok'=>true,'cand_id'=>$db->insert_id],201);
}
// CANDIDATURAS DA EMPRESA
if (isGet() && $action==='candidaturas') {
    $auth=requireEmpresa(); $db=getDB(); $vid=(int)param('vaga_id');
    $sql='SELECT c.*,u.nome,u.especialidade,u.nivel,u.github,u.linkedin,u.photo_url,v.titulo AS vaga_titulo FROM candidaturas c JOIN usuarios u ON u.id=c.usuario_id JOIN vagas v ON v.id=c.vaga_id WHERE v.empresa_id=?';
    $vals=[$auth['id']]; $types='i';
    if($vid){$sql.=' AND c.vaga_id=?';$vals[]=$vid;$types.='i';}
    $sql.=' ORDER BY c.criado_em DESC';
    $stmt=$db->prepare($sql); $stmt->bind_param($types,...$vals); $stmt->execute();
    jsonOut($stmt->get_result()->fetch_all(MYSQLI_ASSOC));
}
// MUDAR STATUS
if (isPut() && $action==='status') {
    $auth=requireEmpresa(); $db=getDB(); $b=bodyJson(); $status=$b['status']??'';
    if(!in_array($status,['em-analise','aprovado','recusado'])) jsonOut(['erro'=>'Status inválido.'],400);
    $stmt=$db->prepare('UPDATE candidaturas SET status=? WHERE id=?'); $stmt->bind_param('si',$status,$id); $stmt->execute(); jsonOut(['ok'=>true]);
}
jsonOut(['erro'=>'Rota não encontrada.'],404);
function candCount(int $vid): int { $db=getDB(); $s=$db->prepare('SELECT COUNT(*) FROM candidaturas WHERE vaga_id=?'); $s->bind_param('i',$vid); $s->execute(); return (int)$s->get_result()->fetch_row()[0]; }
