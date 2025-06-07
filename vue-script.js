const { createApp, ref, onMounted, reactive } = Vue;

createApp({
    setup() {

        // Variabili ref
        const messaggio = ref("")
        const prodotti = ref([])
        const numProdottiCarrello = ref(0)

        // Oggetto reattivo per quantità specifica per prodotto nel carrello
        const quantitaCarrello = reactive({});

        // Funzione per caricare i prodotti dal database
        const caricaProdotti = async () => {
            try {
                const res = await fetch('./api/getProdotti.php')
                const data = await res.json()
                prodotti.value = data

            } catch (error) {
                messaggio.value = 'Errore nel caricamento prodotti' + error.message;
            }
        }

        // Funzione per caricare il carrello dallo stato in sessione
        const caricaCarrello = async () => {
            try {
                const res = await fetch('./api/getCarrello.php');
                const data = await res.json();
                if (data.success) {
                    const carrello = data.carrello;

                    // pulisce le quantità
                    for (const key in quantitaCarrello) {
                        delete quantitaCarrello[key];
                    }

                    // Aggiorna le quantità per i prodotti nel carrello
                    for (const key in carrello) {
                        quantitaCarrello[key] = carrello[key].quantita;
                    }

                    // Aggiorna il numero totale di prodotti nel carrello
                    let totale = 0;
                    for (const key in carrello) {
                        totale += carrello[key].quantita;
                    }
                    numProdottiCarrello.value = totale;
                }
            } catch (error) {
                console.error('Errore caricamento carrello:', error);
            }
        }

        // Funzione per aggiungere o rimuovere un prodotto al carrello
        const aggiornaCarrello = async (idProdotto, azione) => {
            try {
                const res = await fetch("./api/handleCarrello.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ idProdotto, azione }),
                });

                const data = await res.json();

                if (data.success) {
                    const carrello = data.carrello;

                    // Rimuove chiavi non più presenti nel carrello
                    for (const key in quantitaCarrello) {
                        if (!carrello.hasOwnProperty(key)) {
                            delete quantitaCarrello[key];
                        }
                    }

                    // Aggiorna quantità per i prodotti ancora nel carrello
                    for (const key in carrello) {
                        quantitaCarrello[key] = carrello[key].quantita;
                    }

                    // Aggiorna quantità totale
                    let totale = 0;
                    for (const key in carrello) {
                        totale += carrello[key].quantita;
                    }
                    numProdottiCarrello.value = totale;
                } else {
                    console.log("Errore API:", data.message);
                }
            } catch (error) {
                console.log("Errore di rete:", error.message);
            }
        };


        // Carica i prodotti e carrello al montaggio del componente
        onMounted(async () => {
            await caricaProdotti();
            await caricaCarrello();
        });

        return {
            messaggio,
            prodotti,
            numProdottiCarrello,
            quantitaCarrello,
            aggiornaCarrello,
        }
    }

}).mount('#app');