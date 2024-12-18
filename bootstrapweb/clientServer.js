import { initializeTooltips } from './script.js';
document.addEventListener('DOMContentLoaded', function () {
    fetch('clientServer.json')
      .then(response => response.json())
      .then(data => {
        const content = data.content;
  
        // 1. Popola il titolo e la descrizione
        document.getElementById('title').innerText = content.title;
        document.getElementById('description').innerText = content.description;
  
        // 2. Popola l'indice
        document.getElementById('index_title').innerText = content.index.title;
        document.getElementById('index_items').children[0].innerHTML = `<a href="${content.index.items[0].link}">${content.index.items[0].text}</a>`;
        document.getElementById('index_items').children[1].innerHTML = `<a href="${content.index.items[1].link}">${content.index.items[1].text}</a>`;
        document.getElementById('index_items').children[2].innerHTML = `<a href="${content.index.items[2].link}">${content.index.items[2].text}</a>`;
  
        // 3. Popola la sezione "Struttura e descrizione"
        document.getElementById('structure').innerText = content.sections[0].title;
        document.getElementById('structure_content').innerHTML = content.sections[0].content;
  
        // 4. Popola la sezione "Elementi chiave del modello C/S"
        document.getElementById('key-elements').innerText = content.sections[1].title;
        document.getElementById('key-elements_content').innerHTML = content.sections[1].content;
  
        // 5. Popola la sezione "Vantaggi"
        document.getElementById('advantages_title').innerText = 'Vantaggi';
        content.sections[2].content.vantaggi.forEach((vantaggio, index) => {
          if (index < 3) { // Limitiamo a 3 vantaggi al massimo
            document.getElementById('advantages_list').children[index].innerHTML = `<a data-bs-toggle="tooltip" title="${vantaggio.tooltip}">${vantaggio.text}</a>`;
          }
        });
  
        // 6. Popola la sezione "Svantaggi"
        document.getElementById('disadvantages_title').innerText = 'Svantaggi';
        content.sections[2].content.svantaggi.forEach((svantaggio, index) => {
          if (index < 3) { // Limitiamo a 3 svantaggi al massimo
            document.getElementById('disadvantages_list').children[index].innerHTML = `<a data-bs-toggle="tooltip" title="${svantaggio.tooltip}">${svantaggio.text}</a>`;
          }
        });
        // Inizializzare i tooltips
        initializeTooltips();
      })
      .catch(error => {
        console.error("Errore nel caricamento del file JSON:", error);
      });
  });
  