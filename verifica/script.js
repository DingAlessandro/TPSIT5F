let totalTime; // Variabile globale per il tempo totale
let timerInterval;
let timeExpired = false; // Variabile che indica se il tempo è scaduto

const timerElement = document.getElementById('timer'); // Elemento DOM per il timer

// Recupera il tempo dal localStorage o lo inizializza a 10 minuti
function initializeTime() {
    const savedTime = localStorage.getItem('totalTime'); // Ottieni il valore dal localStorage
    if (savedTime && !isNaN(savedTime)) {
        totalTime = parseInt(savedTime, 10); // Converti in numero
    } else {
        totalTime = 10 * 60; // 10 minuti in secondi
        localStorage.setItem('totalTime', totalTime); // Salva il tempo iniziale
    }
    updateTimerDisplay(); // Mostra il timer iniziale
}

// Aggiorna il timer nel DOM
function updateTimerDisplay() {
    const minutes = Math.floor(totalTime / 60); // Calcola i minuti
    const seconds = totalTime % 60; // Calcola i secondi
    timerElement.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
}

// Aggiorna il timer ogni secondo
function updateTimer() {
    if (totalTime > 0) {
        totalTime--; // Riduci il tempo di 1 secondo
        updateTimerDisplay(); // Aggiorna il timer visibile
        localStorage.setItem('totalTime', totalTime); // Salva il tempo rimanente
    } else {
        clearInterval(timerInterval); // Ferma il timer quando il tempo finisce
        timeExpired = true; // Imposta il flag come vero quando il tempo scade
        alert('Tempo scaduto!');
    }
}

// Funzione per salvare il tempo nel localStorage
function saveTime() {
    console.log("Tempo salvato nel localStorage");
    localStorage.setItem('totalTime', totalTime); // Salva il tempo rimanente
}

// Disabilita i link quando il tempo è scaduto
function handleLinkClick(event) {
    if (timeExpired && event.target.tagName === 'A') {
        event.preventDefault(); // Blocca il link
        alert('Tempo scaduto! Puoi solo cliccare su "Fine".');
    }
}

// Funzione per il pulsante "Fine"
function endTimer() {
    alert('Timer fermato.');
    clearInterval(timerInterval); // Ferma il timer se l'utente clicca su "Fine"
    timeExpired = true; // Imposta il flag come vero
    localStorage.setItem('totalTime', totalTime); // Salva il tempo rimanente
}

// Disabilita la textarea quando il tempo è scaduto (solo per la pagina delle domande)
function disableTextArea() {
    const userAnswer = document.getElementById('userAnswer');
    if (userAnswer && timeExpired) {
        userAnswer.disabled = true; // Disabilita la textarea
    }
}

// Inizializza il timer quando la pagina è caricata
document.addEventListener('DOMContentLoaded', () => {
    initializeTime(); // Inizializza il tempo
    timerInterval = setInterval(updateTimer, 1000); // Avvia il timer

    // Salva il tempo quando l'utente clicca su un link
    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", () => {
            saveTime();
            localStorage.setItem('type', link.text);
        });
    });

    // Disabilita i link quando il tempo è scaduto
    document.querySelectorAll('.box').forEach(box => {
        box.addEventListener('click', handleLinkClick);
    });

    // Se siamo nella pagina delle domande, disabilitiamo la textarea quando il tempo scade
    disableTextArea();
});


//index
const containerElement = document.querySelector('.containers');
const resultElement = document.getElementById('result');

// Risposte corrette (modifica con le risposte giuste delle tue domande)
const correctAnswers1 = "2"; 
const correctAnswers2 = "4"; 
const correctAnswers3 = "9";

const correctAnswersA = {
    "q1": "b", 
    "q2": "a", 
    "q3": "b"  
};
const correctAnswersB = {
    "q1": "b", 
    "q2": "c", 
    "q3": "a"  
};

// Funzione per il div "Fine"
function endTimer() {
    clearInterval(timerInterval); // Ferma il timer
    const elapsedTime = 10 * 60 - totalTime; // Calcola il tempo trascorso
    const minutesUsed = Math.floor(elapsedTime / 60);
    const secondsUsed = elapsedTime % 60;

    // Nascondi il container e il timer
    containerElement.style.display = 'none';
    timerElement.style.display = 'none';

    // Calcola i punti
    let points = 0;

    if(correctAnswers1 == localStorage.getItem("Answer1")){
        points+=20;
    }
    if(correctAnswers2 == localStorage.getItem("Answer2")){
        points+=20;
    }
    if(correctAnswers3 == localStorage.getItem("Answer3")){
        points+=20;
    }
    if(JSON.stringify(correctAnswersA) == localStorage.getItem("AnswersA")){
        points+=20;
    }
    if(JSON.stringify(correctAnswersB) == localStorage.getItem("AnswersB")){
        points+=20;
    }
    // Mostra il messaggio finale con il punteggio e il tempo usato
    resultElement.style.display = 'block';
    resultElement.textContent = `Hai ottenuto ${points} punti. Tempo usato: ${minutesUsed} minuti e ${secondsUsed} secondi`;

    // Rimuovi il tempo dal localStorage
    localStorage.removeItem("totalTime");
    localStorage.removeItem("Answer1");
    localStorage.removeItem("Answer2");
    localStorage.removeItem("Answer3");
    localStorage.removeItem("AnswersA");
    localStorage.removeItem("AnswersB");
}
