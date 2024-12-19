var type;

document.addEventListener('DOMContentLoaded', () => {
    // Recupera il valore del tipo dalla localStorage
    type = localStorage.getItem("type");

    // Funzione per caricare le domande da un file JSON
    function loadQuestions(type) {
        let filePath = `question${type}.json`; // Costruisce il percorso del file JSON in base al tipo
        fetch(filePath)
        .then(response => response.json()) // Parsea il JSON
        .then(data => {
            const questions = data.questions;

            // Carica il titolo dell'esercizio
            const exerciseTitle = document.getElementById('question');
            exerciseTitle.textContent = data.question; // Imposta il titolo principale

            const questionText = document.getElementById('question_text');
            questionText.textContent = data.question_text; // Imposta il testo dell'esercizio

            // Carica le domande e le opzioni nei rispettivi contenitori esistenti
            questions.forEach((question, index) => {
                // Usa l'ID esistente per la domanda
                const questionContainer = document.getElementById(`question${index + 1}`);

                // Imposta il testo della domanda
                const questionTitle = questionContainer.querySelector('h3');
                questionTitle.textContent = `${question.question_text}`;

                // Aggiungi le opzioni come radio buttons
                const optionsList = questionContainer.querySelector('.options');
                optionsList.innerHTML = ''; // Pulisce le opzioni precedenti

                question.options.forEach(option => {
                    const listItem = document.createElement('li');
                    const label = document.createElement('label');
                    const input = document.createElement('input');
                    input.type = 'radio';
                    input.name = 'q' + (index + 1); // Associa ogni domanda con il proprio gruppo di radio buttons
                    input.value = option.value;

                    label.appendChild(input);
                    label.appendChild(document.createTextNode(option.text));
                    listItem.appendChild(label);
                    optionsList.appendChild(listItem);
                });
            });
        })
        .catch(error => {
            console.error('Errore nel caricare il JSON:', error);
        });
    }

    // Funzione per inviare le risposte
    function submitAnswers() {
        const answers = {}; // Oggetto per raccogliere tutte le risposte

        // Raccogli tutte le risposte selezionate
        const questions = document.querySelectorAll('.question');
        questions.forEach((question, index) => {
            const selectedOption = question.querySelector('input[type="radio"]:checked');
            if (selectedOption) {
                answers[`q${index + 1}`] = selectedOption.value;
            }
        });

        // Salva le risposte nel localStorage
        localStorage.setItem(`Answers${type}`, JSON.stringify(answers));

        alert('Risposte inviate e salvate nel localStorage!');

        // Reindirizza alla pagina index.html
        window.location.href = 'index.html';
    }

    // Gestisce il tipo di domanda e carica il contenuto appropriato
    switch(type) {
        case 'A':
            loadQuestions("A");
            break;
        case 'B':
            loadQuestions("B");
            break;
        default:
            console.error('Tipo di domanda non valido');
            alert('Tipo di domanda non valido.');
            break;
    }

    // Aggiungi un evento per il click sul pulsante "Invia"
    const submitButton = document.getElementById('submit');
    submitButton.addEventListener('click', submitAnswers);
});
