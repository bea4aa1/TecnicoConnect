<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/router.php';
setCors(); requireAuth();
$db=$db=getDB(); $id=(int)param('id');

if (isGet() && $id) {
    $s=$db->prepare('SELECT id,nome,especialidade,nivel,titulo,bio,cidade,modelo,github,linkedin,portfolio,telefone,email_contato,photo_url,score,trust_score FROM usuarios WHERE id=? AND tipo="dev" LIMIT 1');
    $s->bind_param('i',$id); $s->execute(); $dev=$s->get_result()->fetch_assoc();
    if(!$dev) jsonOut(['erro'=>'Dev não encontrado.'],404);
    foreach(['habilidades'=>'SELECT * FROM habilidades WHERE usuario_id=?','experiencias'=>'SELECT * FROM experiencias WHERE usuario_id=? ORDER BY inicio DESC','projetos'=>'SELECT * FROM projetos WHERE usuario_id=? ORDER BY criado_em DESC','certificacoes'=>'SELECT * FROM certificacoes WHERE usuario_id=? ORDER BY ano DESC','idiomas'=>'SELECT * FROM idiomas WHERE usuario_id=?'] as $key=>$sql) {
        $s=$db->prepare($sql); $s->bind_param('i',$id); $s->execute(); $dev[$key]=$s->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    jsonOut($dev);
}

if (isGet()) {
    $where=['u.tipo="dev"','u.ativo=1']; $vals=[]; $types='';
    if ($q=param('q'))     { $like='%'.$q.'%'; $where[]='(u.nome LIKE ? OR u.especialidade LIKE ? OR h_s.nomes LIKE ?)'; $vals[]=$like;$vals[]=$like;$vals[]=$like; $types.='sss'; }
    if ($n=param('nivel')) { $where[]='u.nivel LIKE ?'; $vals[]=$n.'%'; $types.='s'; }
    if ($e=param('espec')) { $where[]='u.especialidade=?'; $vals[]=$e; $types.='s'; }
    $sql="SELECT u.id,u.nome,u.especialidade,u.nivel,u.cidade,u.photo_url,u.github,u.score,u.trust_score,IFNULL(h_s.nomes,'') AS stack_nomes FROM usuarios u LEFT JOIN (SELECT usuario_id,GROUP_CONCAT(nome SEPARATOR ', ') AS nomes FROM habilidades GROUP BY usuario_id) h_s ON h_s.usuario_id=u.id WHERE ".implode(' AND ',$where)." ORDER BY u.trust_score DESC,u.score DESC LIMIT 50";
    $stmt=$db->prepare($sql); if($types) $stmt->bind_param($types,...$vals); $stmt->execute();
    $devs=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    foreach($devs as &$d) { $s=$db->prepare('SELECT nome,nivel FROM habilidades WHERE usuario_id=? LIMIT 4'); $s->bind_param('i',$d['id']); $s->execute(); $d['habilidades']=$s->get_result()->fetch_all(MYSQLI_ASSOC); unset($d['stack_nomes']); }
    jsonOut($devs);
}
jsonOut(['erro'=>'Método não permitido.'],405);
