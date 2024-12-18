import { initializeTooltips } from './script.js';

// Funzione per caricare il file JSON e popolare i contenuti nell'HTML
document.addEventListener('DOMContentLoaded', function () {
    fetch('tcpUdp.json')  // Assicurati che il percorso sia corretto
        .then(response => {
            if (!response.ok) {
                throw new Error('Errore nel caricamento del file JSON');
            }
            return response.json();  // Converte la risposta JSON
        })
        .then(data => {
            // Popolamento dei titoli e delle descrizioni principali
            document.getElementById('title').textContent = data.content.title;
            document.getElementById('description').textContent = data.content.description;

            // Popolamento dell'indice
            document.getElementById('index_title').textContent = data.content.index.title;

            // Aggiungi un controllo per ogni li che deve essere popolato
            const indexItems = document.getElementById('index_items').children;
            if (indexItems.length > 0) indexItems[0].innerHTML = `<a href="${data.content.index.items[0].link}">${data.content.index.items[0].text}</a>`;
            if (indexItems.length > 1) indexItems[1].innerHTML = `<a href="${data.content.index.items[1].link}">${data.content.index.items[1].text}</a>`;
            if (indexItems.length > 2) indexItems[2].innerHTML = `<a href="${data.content.index.items[2].link}">${data.content.index.items[2].text}</a>`;

            // Popolamento delle sezioni TCP e UDP
            const tcpSection = data.content.sections[0];
            document.getElementById('tcp').textContent = tcpSection.title;
            document.getElementById('tcp_content').textContent = tcpSection.content;

            // Aggiungi un controllo per ogni li che deve essere popolato per TCP
            const tcpAdvantagesList = document.getElementById('tcp_advantages_list').children;
            if (tcpAdvantagesList.length > 0) tcpAdvantagesList[0].innerHTML = tcpSection.characteristics[0].html;
            if (tcpAdvantagesList.length > 1) tcpAdvantagesList[1].innerHTML = tcpSection.characteristics[1].html;
            if (tcpAdvantagesList.length > 2) tcpAdvantagesList[2].innerHTML = tcpSection.characteristics[2].html;

            const udpSection = data.content.sections[1];
            document.getElementById('udp').textContent = udpSection.title;
            document.getElementById('udp_content').textContent = udpSection.content;

            // Aggiungi un controllo per ogni li che deve essere popolato per UDP
            const udpAdvantagesList = document.getElementById('udp_advantages_list').children;
            if (udpAdvantagesList.length > 0) udpAdvantagesList[0].innerHTML = udpSection.characteristics[0].html;
            if (udpAdvantagesList.length > 1) udpAdvantagesList[1].innerHTML = udpSection.characteristics[1].html;

            // Popolamento dei vantaggi e svantaggi di TCP
            document.getElementById('tcp_advantages_title').textContent = "Vantaggi TCP";
            document.getElementById('tcp_disadvantages_title').textContent = "Svantaggi TCP";

            const tcpDisadvantagesList = document.getElementById('tcp_disadvantages_list').children;
            if (tcpDisadvantagesList.length > 0) tcpDisadvantagesList[0].innerHTML = data.content.sections[2].sections[0].disadvantages[0].html;

            // Popolamento dei vantaggi e svantaggi di UDP
            document.getElementById('udp_advantages_title').textContent = "Vantaggi UDP";
            document.getElementById('udp_disadvantages_title').textContent = "Svantaggi UDP";

            const udpDisadvantagesList = document.getElementById('udp_disadvantages_list').children;
            if (udpDisadvantagesList.length > 0) udpDisadvantagesList[0].innerHTML = data.content.sections[2].sections[1].disadvantages[0].html;

            // Inizializzare i tooltips
            initializeTooltips();
        })
        .catch(error => {
            console.error("Errore nel caricamento del file JSON:", error);
        });
});
