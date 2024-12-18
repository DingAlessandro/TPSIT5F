document.addEventListener('DOMContentLoaded', function() {
    // Carica il JSON dal file
    fetch('glossario.json')
        .then(response => response.json())
        .then(data => {
            const accordionContainer = document.getElementById('accordionFlushExample');
            
            // Itera su ogni elemento dell'array accordionItems
            data.accordionItems.forEach(item => {
                // Crea l'HTML per ogni accordion item
                const accordionItem = document.createElement('div');
                accordionItem.classList.add('accordion-item');
                accordionItem.id = item.id;

                // Crea la struttura HTML dell'accordion
                accordionItem.innerHTML = `
                    <h2 class="accordion-header" id="flush-heading${item.id}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapse${item.id}" aria-expanded="false"
                                aria-controls="flush-collapse${item.id}">
                            ${item.title}
                        </button>
                    </h2>
                    <div id="flush-collapse${item.id}" class="accordion-collapse collapse" 
                         aria-labelledby="flush-heading${item.id}" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            ${item.content}
                        </div>
                    </div>
                `;

                // Aggiungi l'item all'accordion container
                accordionContainer.appendChild(accordionItem);
            });
        })
        .catch(error => {
            console.error("Errore nel caricamento del file JSON:", error);
        });
});
