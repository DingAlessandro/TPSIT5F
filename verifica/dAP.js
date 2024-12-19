var type;


document.addEventListener('DOMContentLoaded', () => {
    // Recupera il valore della textarea
    const userAnswer = document.getElementById('userAnswer');
    const submitButton = document.getElementById('submit');
    type = localStorage.getItem("type");
    // Funzione per caricare il contenuto JSON in base al tipo della domanda
    function loadQuestion(type) {
        let filePath = `question${type}.json`; // Costruisce il nome del file JSON in base al tipo

        fetch(filePath) // Carica il file JSON
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Errore nella richiesta: ${response.statusText}`);
                }
                return response.json(); // Parsea il JSON
            })
            .then(data => {
                // Aggiorna il contenuto del numero della domanda
                document.getElementById('question').textContent = data.question;
                
                // Aggiorna il contenuto del testo della domanda
                document.getElementById('question_text').textContent = data.question_text;
            })
            .catch(error => {
                console.error('Errore nel caricare il JSON:', error);
                alert('Si è verificato un errore nel caricare la domanda. Controlla la connessione o prova a ricaricare la pagina.');
            });
    }

    // Gestisce il tipo di domanda e carica il contenuto appropriato
    switch(type) {
        case '1':
            loadQuestion(1);
            break;
        case '2':
            loadQuestion(2);
            break;
        case '3':
            loadQuestion(3);
            break;
        default:
            console.error('Tipo di domanda non valido');
            alert('Tipo di domanda non valido.');
            break;
    }
    // Aggiungi un evento per il click sul pulsante "Invia"
    submitButton.addEventListener('click', () => {
        const answer = userAnswer.value.trim(); // Ottieni il contenuto della textarea e rimuovi gli spazi all'inizio e alla fine
        // Salva sempre la risposta, anche se è vuota
        switch(type){
            case "1":
                localStorage.setItem('Answer1', answer); // Salva la risposta nel localStorage
                break;
            case "2":
                localStorage.setItem('Answer2', answer); // Salva la risposta nel localStorage
                break;
            case "3":
                localStorage.setItem('Answer3', answer); // Salva la risposta nel localStorage
                break;
        }
        alert('Risposta inviata e salvata nel localStorage!'); // Messaggio di conferma
        // Reindirizza l'utente a index.html
        window.location.href = 'index.html'; // Reindirizzamento alla pagina index.html
    });
});
