document.addEventListener('DOMContentLoaded', () => {
    
    
    const themeBtn = document.getElementById('theme-toggle');
    if(themeBtn) {
        themeBtn.onclick = () => {
            document.body.hasAttribute('data-theme') ? 
            document.body.removeAttribute('data-theme') : 
            document.body.setAttribute('data-theme', 'dark');
        };
    }

 
    const range = document.getElementById('expRange');
    if(range) {
        range.oninput = (e) => {
            const val = e.target.value;
            const label = document.getElementById('expLabel');
            const valor = document.getElementById('valorHora');
            const desc = document.getElementById('descNivel');

            const dados = {
                "1": ["Iniciante / Auxiliar", "R$ 28 - 35/h", "Suporte, organização e preventivas básicas."],
                "2": ["Técnico Pleno", "R$ 55 - 75/h", "Autonomia em diagnósticos e pequenas automações."],
                "3": ["Especialista / Sênior", "R$ 95 - 150/h+", "Gestão de projetos e redução de downtime crítico."]
            };

            label.innerText = `Nível: ${dados[val][0]}`;
            valor.innerText = dados[val][1];
            desc.innerText = dados[val][2];
        };
    }

    
    const quizData = [
        { q: "Qual a função principal de um contator?", opt: ["Proteção Térmica", "Comando de potências elevadas", "Medição", "Frequência"], correct: 1 },
        { q: "Na linguagem Ladder, os 'degraus' representam:", opt: ["Fluxogramas", "Esquemas de relés", "Código C++", "Lógica de Blocos"], correct: 1 },
        { q: "Inversores de frequência variam a velocidade via:", opt: ["Resistência", "Frequência e Tensão", "Apenas Tensão", "Corrente Contínua"], correct: 1 },
        { q: "O sinal de 4-20mA é padrão para indicar:", opt: ["Economia", "Zero Vivo / Cabo Rompido", "Alta Tensão", "Curto-circuito"], correct: 1 },
        { q: "Qual o papel da IHM na automação?", opt: ["Executar lógica", "Interface Homem-Máquina", "Converter AC/DC", "Armazenar Energia"], correct: 1 }
    ];

    let currentQ = 0; let score = 0;
    const qText = document.getElementById('question-text');

    if(qText) {
        function loadQuestion() {
            if(currentQ >= quizData.length) {
                document.getElementById('quiz-content').style.display = "none";
                document.getElementById('result-container').style.display = "block";
                document.getElementById('result-text').innerText = `Acertos: ${score} de ${quizData.length}`;
                localStorage.setItem('temp_score', score); 
                return;
            }
            qText.innerText = quizData[currentQ].q;
            const optionsBox = document.getElementById('options-container');
            optionsBox.innerHTML = "";
            quizData[currentQ].opt.forEach((opt, i) => {
                const btn = document.createElement('button');
                btn.className = 'option-btn'; btn.innerText = opt;
                btn.onclick = () => {
                    if(i === quizData[currentQ].correct) { btn.classList.add('correct'); score++; }
                    else { btn.classList.add('wrong'); }
                    setTimeout(() => { currentQ++; loadQuestion(); }, 700);
                };
                optionsBox.appendChild(btn);
            });
        }
        loadQuestion();
    }

    
    const reveal = () => {
        document.querySelectorAll(".reveal").forEach(el => {
            if (el.getBoundingClientRect().top < window.innerHeight - 80) el.classList.add("active");
        });
    };
    window.onscroll = reveal;
    reveal();
});