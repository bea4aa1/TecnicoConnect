# DevConnect — Guia de Instalação Completo

## Estrutura do projeto

```
devconnect_completo/
│
├── 📁 site/                        ← Site informativo (landing page)
│   ├── index.php                   ← Página principal
│   ├── sobre.php                   ← Sobre a equipe
│   ├── quiz.php                    ← Quiz técnico interativo
│   ├── login.php                   ← Página de login
│   ├── cadastro.php                ← Página de cadastro
│   ├── logout.php                  ← Encerra sessão
│   ├── process-newsletter.php      ← Recebe e-mails da newsletter
│   ├── script.js                   ← Animações e interações
│   ├── styles/
│   │   └── styles.css              ← CSS unificado
│   └── includes/
│       ├── header.php              ← Cabeçalho e navbar
│       └── footer.php              ← Rodapé e scripts
│
├── 📁 plataforma/                  ← Plataforma DevConnect
│   ├── index.html                  ← App completo (frontend)
│   ├── api/
│   │   ├── auth.php                ← Login, cadastro, logout, sessão
│   │   ├── perfil.php              ← Perfil, skills, exp, projetos, certs
│   │   ├── vagas.php               ← Vagas e candidaturas
│   │   ├── social.php              ← Posts, mensagens, conexões
│   │   └── devs.php                ← Busca de devs (para empresas)
│   ├── includes/
│   │   ├── db.php                  ← ⚠️ CONFIGURE AQUI suas credenciais MySQL
│   │   ├── auth.php                ← Middleware de autenticação
│   │   └── router.php              ← CORS e helpers HTTP
│   └── uploads/                    ← Fotos de perfil (deve ter permissão 755)
│
└── banco_devconnect.sql            ← Schema completo do banco de dados
```

---

## Passo a passo de instalação

### Passo 1 — Instalar o XAMPP

1. Baixe o XAMPP em: https://www.apachefriends.org
2. Instale e abra o **XAMPP Control Panel**
3. Clique em **Start** no **Apache** e no **MySQL**
4. Confirme que os dois ficam verdes ✅

---

### Passo 2 — Copiar os arquivos

1. Abra o explorador de arquivos e vá até a pasta do XAMPP
   - Windows: `C:\xampp\htdocs\`
   - Mac: `/Applications/XAMPP/htdocs/`
2. Crie uma pasta chamada **`devconnect`** dentro de `htdocs`
3. Copie as pastas **`site`** e **`plataforma`** para dentro de `htdocs/devconnect/`

A estrutura final deve ficar:
```
htdocs/
└── devconnect/
    ├── site/
    └── plataforma/
```

---

### Passo 3 — Criar o banco de dados

1. Abra o navegador e acesse: **http://localhost/phpmyadmin**
2. Clique em **Novo** (ou "New") na barra lateral esquerda
3. No campo **Nome do banco de dados**, digite: `devconnect`
4. Clique em **Criar**
5. Com o banco `devconnect` selecionado, clique na aba **Importar**
6. Clique em **Escolher arquivo** e selecione o arquivo `banco_devconnect.sql`
7. Clique em **Executar** (botão no final da página)
8. Deve aparecer a mensagem: *"Importação concluída com sucesso"* ✅

---

### Passo 4 — Configurar a conexão com o banco

1. Abra o arquivo: `plataforma/includes/db.php`
2. Edite as 4 linhas de configuração:

```php
define('DB_HOST', 'localhost');  // não mude
define('DB_USER', 'root');       // usuário padrão do XAMPP
define('DB_PASS', '');           // senha — no XAMPP geralmente é vazia
define('DB_NAME', 'devconnect'); // não mude
```

> ⚠️ Se você configurou uma senha no MySQL, coloque ela em `DB_PASS`.

---

### Passo 5 — Permissão da pasta uploads

A pasta `plataforma/uploads/` precisa ter permissão de escrita para salvar fotos.

- **Windows (XAMPP):** não precisa fazer nada, já funciona
- **Mac/Linux:** abra o terminal e execute:
```bash
chmod 755 /Applications/XAMPP/htdocs/devconnect/plataforma/uploads/
```

---

### Passo 6 — Acessar o projeto

Abra o navegador e acesse:

| O quê | URL |
|-------|-----|
| 🌐 Site informativo | http://localhost/devconnect/site/ |
| 🚀 Plataforma DevConnect | http://localhost/devconnect/plataforma/ |
| 📊 phpMyAdmin | http://localhost/phpmyadmin |

---

## Fluxo de navegação

```
Site (site/)
   ↓
Botão "Criar conta" ou "Entrar"
   ↓
login.php / cadastro.php
   ↓  (após autenticação)
Plataforma (plataforma/index.html)
   ↓
Feed → Vagas → Perfil → Currículo → Conexões
```

---

## Possíveis erros e soluções

### ❌ "Falha na conexão com o banco de dados"
- Verifique se o MySQL está **Started** (verde) no XAMPP
- Confirme as credenciais em `plataforma/includes/db.php`

### ❌ Página em branco ou erro 500
- Verifique se o Apache está **Started** no XAMPP
- Confirme que os arquivos estão em `htdocs/devconnect/`

### ❌ Login não funciona após cadastro
- Verifique se a importação do SQL foi feita corretamente
- No phpMyAdmin, confira se as tabelas aparecem dentro do banco `devconnect`

### ❌ Foto não salva
- Verifique a permissão da pasta `plataforma/uploads/`
- No Windows isso raramente acontece, mas no Mac/Linux use o `chmod 755`

### ❌ Site redireciona para plataforma mas dá erro
- O login.php usa `curl` para chamar a API. Verifique se a extensão `curl` está ativa no PHP
- No XAMPP: vá em `php.ini`, procure `;extension=curl` e remova o `;`

---

## Resumo rápido (TL;DR)

```
1. Instalar XAMPP → Start Apache + MySQL
2. Copiar pasta devconnect/ para htdocs/
3. phpMyAdmin → importar banco_devconnect.sql
4. Editar plataforma/includes/db.php com suas credenciais
5. Acessar http://localhost/devconnect/site/
```

---

## Equipe

Desenvolvido por Anna Beatriz, Beatriz Santos, Lucca Messi, Luiz Henrique e Thaína Santana — TCC Técnico em Informática.
