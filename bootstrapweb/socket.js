import { initializeTooltips } from './script.js';

document.addEventListener('DOMContentLoaded', function () {
    // Carica il JSON dal file
    fetch('socket.json')
        .then(response => response.json())
        .then(siteContent => {
            // Popolare il titolo e la descrizione
            document.getElementById('title').innerText = siteContent.content.title;
            document.getElementById('description').innerText = siteContent.content.description;

            // Popolare l'indice
            document.getElementById('index-title').innerText = siteContent.content.index.title;
            document.getElementById('index_items').children[0].innerHTML = `<a href="${siteContent.content.index.items[0].link}">${siteContent.content.index.items[0].text}</a>`;
            document.getElementById('index_items').children[1].innerHTML = `<a href="${siteContent.content.index.items[1].link}">${siteContent.content.index.items[1].text}</a>`;
            document.getElementById('index_items').children[2].innerHTML = `<a href="${siteContent.content.index.items[2].link}">${siteContent.content.index.items[2].text}</a>`;
            document.getElementById('index_items').children[3].innerHTML = `<a href="${siteContent.content.index.items[3].link}">${siteContent.content.index.items[3].text}</a>`;

            // Popolare le sezioni
            document.getElementById('intro').innerText = siteContent.content.sections[0].title;
            document.getElementById('intro-content').innerHTML = siteContent.content.sections[0].content;

            document.getElementById('representation').innerText = siteContent.content.sections[1].title;
            document.getElementById('representation-content').innerHTML = siteContent.content.sections[1].content;

            document.getElementById('types').innerText = siteContent.content.sections[2].title;
            document.getElementById('types-content').innerHTML = siteContent.content.sections[2].content;

            document.getElementById('workflow').innerText = siteContent.content.sections[3].title;
            document.getElementById('workflow-content').innerHTML = siteContent.content.sections[3].content;

            // Inizializzare i tooltips
            initializeTooltips();
        })
        .catch(error => {
            console.error("Errore nel caricamento del file JSON:", error);
        });
});

