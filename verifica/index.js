const containerElement = document.querySelector('.containers');
const resultElement = document.getElementById('result');
 // Funzione per il div "Fine"
 function endTimer() {
    clearInterval(timerInterval); // Ferma il timer
    const elapsedTime = 60 * 60 - totalTime; 
    const minutesUsed = Math.floor(elapsedTime / 60);
    const secondsUsed = elapsedTime % 60;

    // Nascondi il container e il timer
    containerElement.style.display = 'none';
    timerElement.style.display = 'none';

    // Mostra il messaggio finale
    resultElement.style.display = 'block';
    resultElement.textContent = `Tempo usato: ${minutesUsed} minuti e ${secondsUsed} secondi`;
}
