function loadJSON() {
    fetch('NavFootCon.json')  // Carica il file JSON
        .then(response => response.json())  // Converte la risposta in un oggetto JSON
        .then(data => {
            // Verifica che i dati siano caricati correttamente
            console.log(data);
            // Passa i dati del navbar alla funzione Navbar
            Navbar(data.navbar);
            Footer(data.footer);
        })
        .catch(error => console.error('Errore nel caricamento del file JSON:', error));
}

function Navbar(navbar) { 
    // Verifica che i dati navbar siano corretti
    console.log(navbar);  // Aggiungi questa riga per debug
    document.getElementById("homeN").textContent = navbar.home;
    document.getElementById("socketN").textContent = navbar.socket;
    document.getElementById("clientServerN").textContent = navbar.client_server;
    document.getElementById("tcpUdpN").textContent = navbar.tcp_udp;
    document.getElementById("glossarioN").textContent = navbar.glossario;
}

function Footer(footer){
    // Verifica che i dati footer siano corretti
    console.log(footer);  // Aggiungi questa riga per debug
    document.getElementById("Footer").textContent = footer.author;
}

document.addEventListener('DOMContentLoaded', function() {
    loadJSON();  // Carica il JSON quando il DOM è pronto
});
