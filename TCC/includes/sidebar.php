<aside class="app-sidebar">
    <div class="brand">Técnico Connect</div>
    
    <nav id="dynamic-menu">
        <a href="?page=home" class="nav-item <?php echo ($page == 'home') ? 'active' : ''; ?>">
            <span>🏠</span> Painel Principal
        </a>
        
        <a href="?page=vagas" class="nav-item <?php echo ($page == 'vagas') ? 'active' : ''; ?>">
            <span>💼</span> Minhas Vagas
        </a>
        
        <a href="?page=academy" class="nav-item <?php echo ($page == 'academy') ? 'active' : ''; ?>">
            <span>🛠️</span> Central de Falhas
        </a>
    </nav>
    
    <div style="margin-top: auto; padding: 20px; background: rgba(0,0,0,0.05); border-radius: 20px; border: 1px solid rgba(0,0,0,0.05);">
        <p style="font-weight: 800; font-size: 0.7rem; color: var(--primary); margin-bottom: 5px; opacity: 0.8;">LOGADO COMO:</p>
        
        <p id="profile-name" style="font-weight: 800; text-transform: uppercase; font-size: 0.9rem; margin: 0; color: #333;">
            <?php 
                if (isset($_SESSION['user_nome'])) {
                    echo $_SESSION['user_nome'];
                } elseif (isset($_SESSION['user_email'])) {
                    echo explode('@', $_SESSION['user_email'])[0];
                } else {
                    echo 'Visitante';
                }
            ?>
        </p>

        <p style="font-size: 0.65rem; opacity: 0.6; margin-bottom: 15px;">
            <?php echo (isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'tech') ? '⚙️ TÉCNICO' : '🏢 EMPRESA'; ?>
        </p>
        
        <button onclick="location.href='logout.php'" style="color: #e74c3c; border:none; background:none; font-weight:800; cursor:pointer; font-size: 0.8rem; padding: 0; display: flex; align-items: center; gap: 5px;">
            <span>🚪</span> SAIR DO SISTEMA
        </button>
    </div>
</aside>