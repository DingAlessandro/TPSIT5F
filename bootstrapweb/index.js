import { initializeTooltips } from './script.js';

document.addEventListener('DOMContentLoaded', function () {
    // Carica il JSON dal file
    fetch('index.json')
        .then(response => response.json())
        .then(siteContent => {
            // Popolare il titolo e la descrizione
            document.getElementById('title').innerText = siteContent.content.title;
            document.getElementById('description').innerText = siteContent.content.description;

            // Popolare l'indice
            document.getElementById('index_title').innerText = siteContent.content.index.title;
            document.getElementById('index_items').children[0].innerHTML = `<a href="${siteContent.content.index.items[0].link}">${siteContent.content.index.items[0].text}</a>`;
            document.getElementById('index_items').children[1].innerHTML = `<a href="${siteContent.content.index.items[1].link}">${siteContent.content.index.items[1].text}</a>`;
            document.getElementById('index_items').children[2].innerHTML = `<a href="${siteContent.content.index.items[2].link}">${siteContent.content.index.items[2].text}</a>`;

            // Popolare le sezioni
            document.getElementById('socket').innerText = siteContent.content.sections[0].title;
            document.getElementById('socket_content').innerHTML = siteContent.content.sections[0].content;

            document.getElementById('MCR').innerText = siteContent.content.sections[1].title;
            document.getElementById('MCR_content').innerHTML = siteContent.content.sections[1].content;

            document.getElementById('TCP_UDP').innerText = siteContent.content.sections[2].title;
            document.getElementById('TCP_description').innerText = siteContent.content.sections[2].content[0].description;
            document.getElementById('UDP_description').innerText = siteContent.content.sections[2].content[1].description;

            // Inizializzare i tooltips
            initializeTooltips();
        })
        .catch(error => {
            console.error("Errore nel caricamento del file JSON:", error);
        });
});




