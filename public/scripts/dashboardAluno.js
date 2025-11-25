/* --- VARIÁVEIS GLOBAIS --- */
let db = null; // Agora começa vazio e é preenchido pela API
let currentDayKey = null;
let currentExerciseIndex = 0;
let timerInterval = null;
let timeLeft = 0;
let timerRunning = false;

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    fetchStudentData();
    
    // Inicializa ícones se a biblioteca estiver carregada
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

/* --- COMUNICAÇÃO COM O BACKEND --- */
async function fetchStudentData() {
    try {
        // Chama a rota que criamos no PHP
        // OBS: Se seu site estiver em subpasta, ajuste para 'projects/LumiG/public/api/aluno-data'
        // Mas como definimos BASE_URL no HTML, podemos usar caminho relativo se o JS estiver na mesma origem
        const response = await fetch('api/aluno-data'); 
        
        if (!response.ok) throw new Error('Erro ao carregar dados');

        db = await response.json();
        
        // Só carrega a tela depois de receber os dados
        loadDashboard();
        
    } catch (error) {
        console.error(error);
        alert("Erro ao conectar com o servidor. Verifique o console.");
    }
}

/* --- RENDERIZAÇÃO DO DASHBOARD --- */
function loadDashboard() {
    if (!db) return;

    // Header Info
    const firstName = db.user.name.split(' ')[0];
    document.getElementById('user-greeting').innerText = `Bom dia, ${firstName}!`;
    document.getElementById('current-date').innerText = db.user.lastAccess;
    document.getElementById('user-initials').innerText = db.user.name.substring(0, 2).toUpperCase();
    
    // Render Grid Semanal
    const grid = document.getElementById('week-grid');
    const days = { seg:'Segunda', ter:'Terça', qua:'Quarta', qui:'Quinta', sex:'Sexta', sab:'Sábado' };
    
    if (grid) {
        grid.innerHTML = Object.keys(days).map(key => {
            // Verifica se existe o dia no plano, senão cria vazio
            const dayData = db.plan[key] || { title: 'Descanso', exercises: [] };
            const count = dayData.exercises.length;
            const exName = count > 0 ? dayData.exercises[0].name : 'Descanso';
            
            return `
                <div class="day-card">
                    <h4>${days[key]}</h4>
                    <h5>${dayData.title}</h5>
                    <ul class="preview-list">
                        <li>${exName}</li>
                        ${count > 1 ? `<li>+${count-1} exercícios</li>` : ''}
                    </ul>
                    <button class="btn-details" onclick="openList('${key}')">Detalhes</button>
                </div>
            `;
        }).join('');
    }

    // Render Stats (Gráficos)
    const freq = db.stats.freq || 0;
    const target = db.stats.target || 1;
    const freqPercent = Math.min((freq / target) * 100, 100);

    const freqVal = document.getElementById('prog-freq-val');
    const freqBar = document.getElementById('prog-freq-bar');
    
    if (freqVal) freqVal.innerText = `${freq}/${target}`;
    if (freqBar) setTimeout(() => freqBar.style.width = `${freqPercent}%`, 500);
    
    setTimeout(() => {
        const chartCal = document.getElementById('chart-cal');
        const valCal = document.getElementById('val-cal');
        if (chartCal && valCal) {
            chartCal.style.setProperty('--p', db.stats.cal);
            valCal.innerText = db.stats.cal + '%';
        }

        const chartCardio = document.getElementById('chart-cardio');
        const valCardio = document.getElementById('val-cardio');
        if (chartCardio && valCardio) {
            chartCardio.style.setProperty('--p', db.stats.cardio);
            valCardio.innerText = db.stats.cardio + '%';
        }
    }, 500);
}

/* --- LÓGICA MODAL LISTA --- */
function openList(key) {
    if (!db) return;
    currentDayKey = key;
    const data = db.plan[key] || { title: 'Descanso', exercises: [] };
    const dayNames = { seg:'Segunda', ter:'Terça', qua:'Quarta', qui:'Quinta', sex:'Sexta', sab:'Sábado' };
    
    document.getElementById('list-title').innerText = dayNames[key];
    document.getElementById('list-subtitle').innerText = data.title;
    
    const tbody = document.getElementById('list-body');
    tbody.innerHTML = '';
    
    if(data.exercises.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; color:gray; padding:2rem;">Sem treino hoje. Aproveite o descanso!</td></tr>';
    } else {
        data.exercises.forEach((ex, idx) => {
            tbody.innerHTML += `
                <tr>
                    <td><strong>${ex.name}</strong></td>
                    <td>${ex.sets}</td>
                    <td>${ex.reps}</td>
                    <td>${ex.load}kg</td>
                    <td><button class="btn-icon" onclick="startWorkout(${idx})"><i data-lucide="play" width="16"></i></button></td>
                </tr>
            `;
        });
    }
    
    document.getElementById('modal-list').classList.add('active');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeListModal() {
    document.getElementById('modal-list').classList.remove('active');
}

/* --- LÓGICA DE EXECUÇÃO --- */
function startWorkout(startIndex = 0) {
    const data = db.plan[currentDayKey];
    if(!data || data.exercises.length === 0) return alert("Não há exercícios para iniciar.");

    currentExerciseIndex = startIndex;
    closeListModal();
    document.getElementById('modal-execution').classList.add('active');
    loadExerciseScreen();
}

function loadExerciseScreen() {
    const list = db.plan[currentDayKey].exercises;
    const ex = list[currentExerciseIndex];

    // Atualiza UI
    document.getElementById('exec-step').innerText = currentExerciseIndex + 1;
    document.getElementById('exec-total').innerText = list.length;
    
    const progBar = document.getElementById('exec-progress-bar');
    if(progBar) progBar.style.width = `${((currentExerciseIndex + 1) / list.length) * 100}%`;
    
    document.getElementById('exec-name').innerText = ex.name;
    
    // Tratamento de Imagem
    const imgEl = document.getElementById('exec-img');
    const placeholderEl = document.getElementById('exec-img-placeholder');
    
    if(ex.img && ex.img.trim() !== "") {
        imgEl.src = ex.img;
        imgEl.style.display = 'block';
        if(placeholderEl) placeholderEl.style.display = 'none';
        
        imgEl.onerror = function() {
            this.style.display = 'none';
            if(placeholderEl) placeholderEl.style.display = 'flex';
        };
    } else {
        imgEl.style.display = 'none';
        if(placeholderEl) placeholderEl.style.display = 'flex';
    }
    
    document.getElementById('exec-sets').innerText = ex.sets;
    document.getElementById('exec-reps').innerText = ex.reps;
    document.getElementById('exec-load').innerText = ex.load + 'kg';
    document.getElementById('exec-note').innerText = ex.note || "Sem observações.";

    // Configura Timer
    timeLeft = ex.rest || 60; // Default 60s se não vier do banco
    updateTimerDisplay();
    pauseTimer(); 
}

function closeExecutionModal() {
    if(confirm("Deseja sair do modo de execução?")) {
        pauseTimer();
        document.getElementById('modal-execution').classList.remove('active');
        openList(currentDayKey); 
    }
}

/* --- CONTROLES DE FLUXO --- */
function finishSet() {
    const btn = document.querySelector('.exec-footer .btn-primary');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="check"></i> Feito!';
    
    setTimeout(() => {
        btn.innerHTML = originalText;
        if (typeof lucide !== 'undefined') lucide.createIcons();
        
        const list = db.plan[currentDayKey].exercises;
        if(currentExerciseIndex < list.length - 1) {
            currentExerciseIndex++;
            loadExerciseScreen();
        } else {
            alert("Parabéns! Treino finalizado com sucesso! 💪");
            closeExecutionModal();
        }
    }, 500);
}

function prevExercise() {
    if(currentExerciseIndex > 0) {
        currentExerciseIndex--;
        loadExerciseScreen();
    }
}

function skipExercise() {
    const list = db.plan[currentDayKey].exercises;
    if(currentExerciseIndex < list.length - 1) {
        currentExerciseIndex++;
        loadExerciseScreen();
    }
}

/* --- LÓGICA DO TIMER --- */
function updateTimerDisplay() {
    const m = Math.floor(timeLeft / 60).toString().padStart(2, '0');
    const s = (timeLeft % 60).toString().padStart(2, '0');
    const timerDisplay = document.getElementById('timer-val');
    if(timerDisplay) timerDisplay.innerText = `${m}:${s}`;
}

function toggleTimer() {
    if(timerRunning) {
        pauseTimer();
    } else {
        startTimer();
    }
}

function startTimer() {
    if(timeLeft <= 0) return;
    timerRunning = true;
    const btn = document.getElementById('btn-timer-toggle');
    if(btn) {
        btn.innerHTML = '<i data-lucide="pause"></i> Pausar';
        btn.classList.replace('btn-primary', 'btn-outline');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
    
    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();
        if(timeLeft <= 0) {
            pauseTimer();
            alert("Tempo de descanso acabou!");
        }
    }, 1000);
}

function pauseTimer() {
    timerRunning = false;
    clearInterval(timerInterval);
    const btn = document.getElementById('btn-timer-toggle');
    if(btn) {
        btn.innerHTML = '<i data-lucide="play"></i> Iniciar';
        btn.classList.replace('btn-outline', 'btn-primary');
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }
}

function resetTimer() {
    pauseTimer();
    const data = db.plan[currentDayKey];
    if(data && data.exercises[currentExerciseIndex]) {
        timeLeft = data.exercises[currentExerciseIndex].rest || 60;
        updateTimerDisplay();
    }
}