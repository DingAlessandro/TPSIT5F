const contentData = {
    "navbar": {
        "home": "Home",
        "socket": "Socket",
        "client_server": "Modello Client Server",
        "tcp_udp": "TCP UDP",
        "glossario": "Glossario"
    },
    "footer": {
        "author": "Alessandro Ding 5F"
    },
    "content": {
        "title": "Contenuti",
        "description": "Il sito tratterà sui socket e sugli argomenti correlati come modello client-server e i protocolli TCP e UDP",
        "index": {
            "title": "Indice",
            "items": [
                {
                    "text": "Socket",
                    "link": "#socket"
                },
                {
                    "text": "Modello Client Server",
                    "link": "#MCR"
                },
                {
                    "text": "TCP e UDP",
                    "link": "#TCP_UDP"
                }
            ]
        },
        "sections": [
            {
                "id": "socket",
                "title": "Socket",
                "content": "Un socket è un punto di comunicazione tra due <a href=\"glossario.html#processo\" data-bs-toggle=\"tooltip\" title=\"esecuzione di un programma.\">processi</a> su una rete, utilizzato per inviare e ricevere dati attraverso una rete (come Internet), utilizzando protocolli come <a href=\"glossario.html#tcp\" data-bs-toggle=\"tooltip\" title=\"Protocollo orientato connessione.\">TCP</a> o <a href=\"glossario.html#udp\" data-bs-toggle=\"tooltip\" title=\"Protocollo senza connessione.\">UDP</a>. I socket gestiscono la connessione, la trasmissione dei dati e la chiusura della comunicazione tra <a href=\"glossario.html#client\" data-bs-toggle=\"tooltip\" title=\"Richiedente del servizio.\">client</a> e <a href=\"glossario.html#server\" data-bs-toggle=\"tooltip\" title=\"Fornitore di servizio.\">server</a>."
            },
            {
                "id": "MCR",
                "title": "Modello Client Server",
                "content": "Il modello client-server è un'architettura in cui il <a href=\"glossario.html#client\" data-bs-toggle=\"tooltip\" title=\"Richiedente del servizio.\">client</a> richiede servizi o dati e il <a href=\"glossario.html#server\" data-bs-toggle=\"tooltip\" title=\"Fornitore di servizio.\">server</a> li fornisce. Il <a href=\"glossario.html#server\" data-bs-toggle=\"tooltip\" title=\"Fornitore di servizio.\">server</a> è centralizzato e gestisce le richieste dei <a href=\"glossario.html#client\" data-bs-toggle=\"tooltip\" title=\"Richiedente del servizio.\">client</a>, usando <a href=\"glossario.html#protocollo\" data-bs-toggle=\"tooltip\" title=\"Regole di comunicazione.\">protocolli</a> di comunicazione come TCP/IP."
            },
            {
                "id": "TCP_UDP",
                "title": "TCP e UDP",
                "content": [
                    {
                        "protocol": "TCP",
                        "description": "TCP è un protocollo affidabile e orientato alla connessione, che garantisce la consegna ordinata dei dati, adatto per applicazioni che richiedono precisione come il web."
                    },
                    {
                        "protocol": "UDP",
                        "description": "UDP è un protocollo più veloce ma senza connessione, che non garantisce l'affidabilità o l'ordine dei dati, ideale per applicazioni in tempo reale come lo streaming o le chiamate VoIP."
                    }
                ]
            }
        ]
    }
};

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
