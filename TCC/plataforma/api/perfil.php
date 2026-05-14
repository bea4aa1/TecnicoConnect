<?php

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/router.php';
setCors();
$action = param('action');
$id_param = (int)param('id');

// GET perfil
if (isGet() && !$action) {
    $auth = requireAuth();
    jsonOut(getFullProfile($id_param ?: $auth['id']));
}
// PUT dados básicos
if (isPut() && !$action) {
    $auth = requireDev(); $b = bodyJson(); $db = getDB();
    $fields = ['nome','titulo','bio','cidade','modelo','github','linkedin','portfolio','telefone','email_contato','especialidade','nivel'];
    $sets=[]; $vals=[]; $types='';
    foreach ($fields as $f) { if (array_key_exists($f,$b)) { $sets[]="$f=?"; $vals[]=clean((string)($b[$f]??'')); $types.='s'; } }
    if (empty($sets)) jsonOut(['erro'=>'Nada para atualizar.'],400);
    $vals[]=$auth['id']; $types.='i';
    $stmt=$db->prepare('UPDATE usuarios SET '.implode(',',$sets).' WHERE id=?');
    $stmt->bind_param($types,...$vals); $stmt->execute();
    jsonOut(['ok'=>true,'user'=>getFullProfile($auth['id'])]);
}
// Foto
if (isPost() && $action==='photo') {
    $auth=requireAuth(); $b=bodyJson(); $data=$b['base64']??'';
    if (!$data||!str_contains($data,',')) jsonOut(['erro'=>'Imagem inválida.'],400);
    [,$raw]=explode(',',$data,2); $bin=base64_decode($raw);
    $ext=str_contains($data,'png')?'png':'jpg';
    $filename='user_'.$auth['id'].'_'.time().'.'.$ext;
    file_put_contents(__DIR__.'/../uploads/'.$filename,$bin);
    $url='/plataforma/uploads/'.$filename; $db=getDB();
    $stmt=$db->prepare('UPDATE usuarios SET photo_url=? WHERE id=?');
    $stmt->bind_param('si',$url,$auth['id']); $stmt->execute();
    jsonOut(['ok'=>true,'photo_url'=>$url]);
}
// Habilidades
if ($action==='habilidade') {
    $auth=requireDev(); $db=getDB();
    if (isPost()) {
        $b=bodyJson(); $nome=clean($b['nome']??''); $nivel=clean($b['nivel']??'Básico'); $cat=clean($b['categoria']??'');
        if (!$nome) jsonOut(['erro'=>'Nome obrigatório.'],400);
        $stmt=$db->prepare('INSERT IGNORE INTO habilidades (usuario_id,nome,nivel,categoria) VALUES (?,?,?,?)');
        $stmt->bind_param('isss',$auth['id'],$nome,$nivel,$cat); $stmt->execute();
        jsonOut(['ok'=>true,'habilidades'=>getHabilidades($auth['id'])]);
    }
    if (isDelete()) {
        $id=(int)param('id');
        $stmt=$db->prepare('DELETE FROM habilidades WHERE id=? AND usuario_id=?');
        $stmt->bind_param('ii',$id,$auth['id']); $stmt->execute();
        jsonOut(['ok'=>true,'habilidades'=>getHabilidades($auth['id'])]);
    }
}
// Experiências
if ($action==='experiencia') {
    $auth=requireDev(); $db=getDB();
    if (isPost()) {
        $b=bodyJson();
        $cargo=clean($b['cargo']??''); $emp=clean($b['empresa']??''); $inicio=$b['inicio']??null; $fim=$b['fim']?:null; $desc=clean($b['desc']??''); $empLi=clean($b['emp_linkedin']??'');
        if (!$cargo||!$emp||!$inicio) jsonOut(['erro'=>'Cargo, empresa e início obrigatórios.'],400);
        $stmt=$db->prepare('INSERT INTO experiencias (usuario_id,cargo,empresa,emp_linkedin,inicio,fim,descricao) VALUES (?,?,?,?,?,?,?)');
        $stmt->bind_param('issssss',$auth['id'],$cargo,$emp,$empLi,$inicio,$fim,$desc); $stmt->execute();
        jsonOut(['ok'=>true,'experiencias'=>getExperiencias($auth['id'])]);
    }
    if (isDelete()) {
        $id=(int)param('id');
        $stmt=$db->prepare('DELETE FROM experiencias WHERE id=? AND usuario_id=?');
        $stmt->bind_param('ii',$id,$auth['id']); $stmt->execute();
        jsonOut(['ok'=>true,'experiencias'=>getExperiencias($auth['id'])]);
    }
}
// Projetos
if ($action==='projeto') {
    $auth=requireDev(); $db=getDB();
    if (isPost()) {
        $b=bodyJson(); $nome=clean($b['nome']??''); $desc=clean($b['desc']??''); $stack=clean($b['stack']??''); $github=clean($b['github']??''); $demo=clean($b['demo']??'');
        if (!$nome) jsonOut(['erro'=>'Nome obrigatório.'],400);
        if (!$github) jsonOut(['erro'=>'GitHub obrigatório.'],400);
        $stmt=$db->prepare('INSERT INTO projetos (usuario_id,nome,descricao,stack,github,demo) VALUES (?,?,?,?,?,?)');
        $stmt->bind_param('isssss',$auth['id'],$nome,$desc,$stack,$github,$demo); $stmt->execute();
        jsonOut(['ok'=>true,'projetos'=>getProjetos($auth['id'])]);
    }
    if (isDelete()) {
        $id=(int)param('id');
        $stmt=$db->prepare('DELETE FROM projetos WHERE id=? AND usuario_id=?');
        $stmt->bind_param('ii',$id,$auth['id']); $stmt->execute();
        jsonOut(['ok'=>true,'projetos'=>getProjetos($auth['id'])]);
    }
}
// Certificações
if ($action==='certificacao') {
    $auth=requireDev(); $db=getDB();
    if (isPost()) {
        $b=bodyJson(); $nome=clean($b['nome']??''); $inst=clean($b['inst']??''); $ano=$b['ano']??null; $horas=clean($b['horas']??''); $url=clean($b['url']??'');
        if (!$nome) jsonOut(['erro'=>'Nome obrigatório.'],400);
        $stmt=$db->prepare('INSERT INTO certificacoes (usuario_id,nome,instituicao,ano,horas,url) VALUES (?,?,?,?,?,?)');
        $stmt->bind_param('isssss',$auth['id'],$nome,$inst,$ano,$horas,$url); $stmt->execute();
        jsonOut(['ok'=>true,'certificacoes'=>getCerts($auth['id'])]);
    }
    if (isDelete()) {
        $id=(int)param('id');
        $stmt=$db->prepare('DELETE FROM certificacoes WHERE id=? AND usuario_id=?');
        $stmt->bind_param('ii',$id,$auth['id']); $stmt->execute();
        jsonOut(['ok'=>true,'certificacoes'=>getCerts($auth['id'])]);
    }
}
// Idiomas
if ($action==='idioma') {
    $auth=requireDev(); $db=getDB();
    if (isPost()) {
        $b=bodyJson(); $idioma=clean($b['idioma']??''); $nivel=clean($b['nivel']??'');
        if (!$idioma) jsonOut(['erro'=>'Idioma obrigatório.'],400);
        $stmt=$db->prepare('INSERT IGNORE INTO idiomas (usuario_id,idioma,nivel) VALUES (?,?,?)');
        $stmt->bind_param('iss',$auth['id'],$idioma,$nivel); $stmt->execute();
        jsonOut(['ok'=>true,'idiomas'=>getIdiomas($auth['id'])]);
    }
    if (isDelete()) {
        $id=(int)param('id');
        $stmt=$db->prepare('DELETE FROM idiomas WHERE id=? AND usuario_id=?');
        $stmt->bind_param('ii',$id,$auth['id']); $stmt->execute();
        jsonOut(['ok'=>true,'idiomas'=>getIdiomas($auth['id'])]);
    }
}
jsonOut(['erro'=>'Rota não encontrada.'],404);

function getFullProfile(int $id): array {
    $db=$db=getDB(); $stmt=$db->prepare('SELECT * FROM usuarios WHERE id=? LIMIT 1');
    $stmt->bind_param('i',$id); $stmt->execute();
    $user=$stmt->get_result()->fetch_assoc(); if(!$user) return [];
    unset($user['senha']);
    $user['habilidades']  =getHabilidades($id);
    $user['experiencias'] =getExperiencias($id);
    $user['projetos']     =getProjetos($id);
    $user['certificacoes']=getCerts($id);
    $user['idiomas']      =getIdiomas($id);
    return $user;
}
function getHabilidades(int $uid): array  { $db=getDB(); $s=$db->prepare('SELECT * FROM habilidades WHERE usuario_id=? ORDER BY criado_em'); $s->bind_param('i',$uid); $s->execute(); return $s->get_result()->fetch_all(MYSQLI_ASSOC); }
function getExperiencias(int $uid): array { $db=getDB(); $s=$db->prepare('SELECT * FROM experiencias WHERE usuario_id=? ORDER BY inicio DESC'); $s->bind_param('i',$uid); $s->execute(); return $s->get_result()->fetch_all(MYSQLI_ASSOC); }
function getProjetos(int $uid): array     { $db=getDB(); $s=$db->prepare('SELECT * FROM projetos WHERE usuario_id=? ORDER BY criado_em DESC'); $s->bind_param('i',$uid); $s->execute(); return $s->get_result()->fetch_all(MYSQLI_ASSOC); }
function getCerts(int $uid): array        { $db=getDB(); $s=$db->prepare('SELECT * FROM certificacoes WHERE usuario_id=? ORDER BY ano DESC'); $s->bind_param('i',$uid); $s->execute(); return $s->get_result()->fetch_all(MYSQLI_ASSOC); }
function getIdiomas(int $uid): array      { $db=getDB(); $s=$db->prepare('SELECT * FROM idiomas WHERE usuario_id=?'); $s->bind_param('i',$uid); $s->execute(); return $s->get_result()->fetch_all(MYSQLI_ASSOC); }
