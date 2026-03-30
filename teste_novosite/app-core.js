const errorsAcademy = [
    { code: "F001", machine: "Inversor Weg", desc: "Sobretensão no Link DC", fix: "Verificar frenagem e rede." },
    { code: "E704", machine: "Robô Kuka", desc: "Erro de Comunicação Fieldbus", fix: "Checar cabos Profinet." },
    { code: "AL-01", machine: "Servo Fanuc", desc: "Erro de Pulso do Encoder", fix: "Limpeza de conector ou troca." }
];

const talentos = [
    { nome: "Carlos Mendes", score: 5, tags: ["NR10", "NR12", "Siemens"], match: "100%" },
    { nome: "Fabiana Luz", score: 4, tags: ["Weg", "Pneumática"], match: "85%" },
    { nome: "Jorge Santos", score: 5, tags: ["Rockwell", "NR35"], match: "98%" }
];

document.addEventListener('DOMContentLoaded', () => {
    const type = localStorage.getItem('user_type') || 'tech';
    const email = localStorage.getItem('user_email') || 'visitante@tech.com';
    const score = localStorage.getItem('user_score') || 0;


    document.getElementById('profile-name').innerText = email.split('@')[0].toUpperCase();
    document.getElementById('profile-role').innerText = type === 'tech' ? 'TÉCNICO ESPECIALISTA' : 'RECRUTADOR INDUSTRIAL';


    const menu = document.getElementById('dynamic-menu');
    if(type === 'tech') {
        menu.innerHTML = `
            <div class="nav-item active" onclick="switchTab('tech')">🏠 <span>Meu Perfil</span></div>
            <div class="nav-item" onclick="switchTab('academy')">🎓 <span>Academy</span></div>
            <div class="nav-item" onclick="switchTab('chat')">💬 <span>Mensagens</span></div>
        `;
        document.getElementById('final-score').innerText = score + "/5";
        switchTab('tech');
        loadAcademy();
    } else {
        menu.innerHTML = `
            <div class="nav-item active" onclick="switchTab('company')">🏢 <span>Buscar Talentos</span></div>
            <div class="nav-item" onclick="switchTab('chat')">💬 <span>Conversas</span></div>
        `;
        switchTab('company');
        loadTalentos();
    }
});

function switchTab(tabId) {
    document.querySelectorAll('.view-section').forEach(v => v.style.display = 'none');
    document.getElementById('tab-' + tabId).style.display = 'block';
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    document.getElementById('page-title').innerText = tabId.toUpperCase();
}

function loadAcademy() {
    const container = document.getElementById('academy-list');
    container.innerHTML = errorsAcademy.map(e => `
        <div class="error-item">
            <div><span class="error-code">${e.code}</span> - <strong>${e.machine}</strong><br><small>${e.desc}</small></div>
            <div style="color:var(--secondary); font-size:0.8rem; cursor:pointer;" onclick="alert('Solução: ${e.fix}')">VER SOLUÇÃO</div>
        </div>
    `).join('');
}

function loadTalentos() {
    const container = document.getElementById('talent-results');
    container.innerHTML = talentos.map(t => `
        <div style="padding:20px; border:1px solid #eee; border-radius:15px; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
            <div><strong>${t.nome}</strong><br><small>${t.tags.join(' | ')}</small></div>
            <div class="badge-match">${t.match} Match</div>
            <button class="btn-prime" style="padding:10px;" onclick="switchTab('chat')">Contatar</button>
        </div>
    `).join('');
}

function sendMsg() {
    const input = document.getElementById('msg-input');
    if(!input.value) return;
    const box = document.getElementById('chat-box');
    box.innerHTML += `<div class="msg sent">${input.value}</div>`;
    input.value = "";
    box.scrollTop = box.scrollHeight;
}

function logout() { localStorage.clear(); window.location.href = 'index.html'; }