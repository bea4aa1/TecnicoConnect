<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/router.php';
setCors();
$auth   = requireAuth();
$uid    = $auth['id'];
$action = param('action');
$id     = (int)param('id');
$db     = getDB();

//POSTS
if ($action==='posts' && isGet()) {
    $tid=(int)(param('usuario_id')?:$uid);
    $s=$db->prepare('SELECT p.*,u.nome,u.especialidade,u.nivel,u.photo_url FROM posts p JOIN usuarios u ON u.id=p.usuario_id WHERE p.usuario_id=? ORDER BY p.criado_em DESC');
    $s->bind_param('i',$tid); $s->execute(); $posts=$s->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach($posts as &$p) $p['tags']=$p['tags']?array_map('trim',explode(',',$p['tags'])):[];
    jsonOut($posts);
}
if ($action==='feed' && isGet()) {
    $s=$db->prepare('SELECT p.*,u.nome,u.especialidade,u.nivel,u.photo_url FROM posts p JOIN usuarios u ON u.id=p.usuario_id WHERE p.usuario_id IN (SELECT CASE WHEN de_id=? THEN para_id ELSE de_id END FROM conexoes WHERE (de_id=? OR para_id=?) AND status="aceita") ORDER BY p.criado_em DESC LIMIT 50');
    $s->bind_param('iii',$uid,$uid,$uid); $s->execute(); $posts=$s->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach($posts as &$p) $p['tags']=$p['tags']?array_map('trim',explode(',',$p['tags'])):[];
    jsonOut($posts);
}
if ($action==='posts' && isPost()) {
    $b=bodyJson(); $txt=clean($b['texto']??''); $tags=clean($b['tags']??'');
    if(!$txt) jsonOut(['erro'=>'Texto obrigatório.'],400);
    $s=$db->prepare('INSERT INTO posts (usuario_id,texto,tags) VALUES (?,?,?)');
    $s->bind_param('iss',$uid,$txt,$tags); $s->execute(); jsonOut(['ok'=>true,'id'=>$db->insert_id],201);
}
if ($action==='like' && isPost() && $id) {
    $s=$db->prepare('UPDATE posts SET likes=likes+1 WHERE id=?'); $s->bind_param('i',$id); $s->execute(); jsonOut(['ok'=>true]);
}
if ($action==='posts' && isDelete() && $id) {
    $s=$db->prepare('DELETE FROM posts WHERE id=? AND usuario_id=?'); $s->bind_param('ii',$id,$uid); $s->execute(); jsonOut(['ok'=>true]);
}

//MENSAGENS
if ($action==='mensagens' && isGet()) {
    $s=$db->prepare('SELECT m.*,u.nome AS parceiro_nome,u.photo_url AS parceiro_photo FROM mensagens m JOIN usuarios u ON u.id=IF(m.de_id=?,m.para_id,m.de_id) WHERE (m.de_id=? OR m.para_id=?) AND m.id=(SELECT MAX(m2.id) FROM mensagens m2 WHERE (m2.de_id=m.de_id AND m2.para_id=m.para_id) OR (m2.de_id=m.para_id AND m2.para_id=m.de_id)) ORDER BY m.criado_em DESC');
    $s->bind_param('iii',$uid,$uid,$uid); $s->execute(); jsonOut($s->get_result()->fetch_all(MYSQLI_ASSOC));
}
if ($action==='chat' && isGet()) {
    $com=(int)param('com'); if(!$com) jsonOut(['erro'=>'Parâmetro "com" obrigatório.'],400);
    $upd=$db->prepare('UPDATE mensagens SET lida=1 WHERE de_id=? AND para_id=?'); $upd->bind_param('ii',$com,$uid); $upd->execute();
    $s=$db->prepare('SELECT * FROM mensagens WHERE (de_id=? AND para_id=?) OR (de_id=? AND para_id=?) ORDER BY criado_em ASC LIMIT 200');
    $s->bind_param('iiii',$uid,$com,$com,$uid); $s->execute(); jsonOut($s->get_result()->fetch_all(MYSQLI_ASSOC));
}
if ($action==='mensagens' && isPost()) {
    $b=bodyJson(); $para=(int)($b['para_id']??0); $texto=clean($b['texto']??'');
    if(!$para||!$texto) jsonOut(['erro'=>'para_id e texto obrigatórios.'],400);
    $s=$db->prepare('INSERT INTO mensagens (de_id,para_id,texto) VALUES (?,?,?)');
    $s->bind_param('iis',$uid,$para,$texto); $s->execute(); jsonOut(['ok'=>true,'id'=>$db->insert_id],201);
}

// CONEXÕES
if ($action==='conexoes' && isGet()) {
    $s=$db->prepare('SELECT c.*,u.nome,u.especialidade,u.nivel,u.photo_url,u.cidade FROM conexoes c JOIN usuarios u ON u.id=IF(c.de_id=?,c.para_id,c.de_id) WHERE (c.de_id=? OR c.para_id=?) AND c.status="aceita" ORDER BY c.criado_em DESC');
    $s->bind_param('iii',$uid,$uid,$uid); $s->execute(); jsonOut($s->get_result()->fetch_all(MYSQLI_ASSOC));
}
if ($action==='sugestoes' && isGet()) {
    $s=$db->prepare('SELECT u.id,u.nome,u.especialidade,u.nivel,u.photo_url,u.cidade FROM usuarios u WHERE u.tipo="dev" AND u.id<>? AND u.id NOT IN (SELECT CASE WHEN de_id=? THEN para_id ELSE de_id END FROM conexoes WHERE de_id=? OR para_id=?) ORDER BY RAND() LIMIT 8');
    $s->bind_param('iiii',$uid,$uid,$uid,$uid); $s->execute(); $devs=$s->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach($devs as &$d) { $h=$db->prepare('SELECT nome FROM habilidades WHERE usuario_id=? LIMIT 5'); $h->bind_param('i',$d['id']); $h->execute(); $d['habilidades']=array_column($h->get_result()->fetch_all(MYSQLI_ASSOC),'nome'); }
    jsonOut($devs);
}
if ($action==='solicitacoes' && isGet()) {
    $s=$db->prepare('SELECT c.*,u.nome,u.especialidade,u.nivel,u.photo_url FROM conexoes c JOIN usuarios u ON u.id=c.de_id WHERE c.para_id=? AND c.status="pendente" ORDER BY c.criado_em DESC');
    $s->bind_param('i',$uid); $s->execute(); jsonOut($s->get_result()->fetch_all(MYSQLI_ASSOC));
}
if ($action==='conectar' && isPost() && $id) {
    if($id===$uid) jsonOut(['erro'=>'Não pode conectar consigo mesmo.'],400);
    $chk=$db->prepare('SELECT id,status FROM conexoes WHERE (de_id=? AND para_id=?) OR (de_id=? AND para_id=?) LIMIT 1');
    $chk->bind_param('iiii',$uid,$id,$id,$uid); $chk->execute(); $ex=$chk->get_result()->fetch_assoc();
    if($ex) jsonOut(['erro'=>'Já existe conexão.','status'=>$ex['status']],409);
    $s=$db->prepare('INSERT INTO conexoes (de_id,para_id) VALUES (?,?)'); $s->bind_param('ii',$uid,$id); $s->execute(); jsonOut(['ok'=>true,'status'=>'pendente'],201);
}
if ($action==='aceitar' && isPut() && $id) {
    $chk=$db->prepare('SELECT id FROM conexoes WHERE id=? AND para_id=? AND status="pendente" LIMIT 1');
    $chk->bind_param('ii',$id,$uid); $chk->execute();
    if(!$chk->get_result()->fetch_assoc()) jsonOut(['erro'=>'Solicitação não encontrada.'],404);
    $s=$db->prepare('UPDATE conexoes SET status="aceita" WHERE id=?'); $s->bind_param('i',$id); $s->execute(); jsonOut(['ok'=>true]);
}
if ($action==='rejeitar' && isPut() && $id) {
    $s=$db->prepare('DELETE FROM conexoes WHERE id=? AND para_id=?'); $s->bind_param('ii',$id,$uid); $s->execute(); jsonOut(['ok'=>true]);
}
if ($action==='conexao' && isDelete() && $id) {
    $s=$db->prepare('DELETE FROM conexoes WHERE (de_id=? AND para_id=?) OR (de_id=? AND para_id=?)'); $s->bind_param('iiii',$uid,$id,$id,$uid); $s->execute(); jsonOut(['ok'=>true]);
}
jsonOut(['erro'=>'Rota não encontrada.'],404);
