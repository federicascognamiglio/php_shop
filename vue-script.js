const { createApp, ref, onMounted, reactive } = Vue;

createApp({
    setup() {

        // Variabili ref
        const messaggio = ref("")
        const prodotti = ref([])
        const numProdottiCarrello = ref(0)
        const dettagliCarrello = ref({});
        const subtotaleCarrello = ref(0);
        const totaleCarrello = ref(0);
        const scontato = ref(false);

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

                    // Aggiorna lo stato del carrello
                    subtotaleCarrello.value = data.subtotale;
                    scontato.value = data.scontato;
                    totaleCarrello.value = data.totale;

                    // reset quantità e dettagli
                    for (const key in quantitaCarrello) delete quantitaCarrello[key];
                    dettagliCarrello.value = {};

                    // Popola le quantità e i dettagli del carrello
                    for (const key in carrello) {
                        quantitaCarrello[key] = carrello[key].quantita;
                        dettagliCarrello.value[key] = carrello[key];
                    }

                    // Aggiorna il numero totale di prodotti nel carrello
                    let totale = 0;
                    for (const key in carrello) totale += carrello[key].quantita;
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

                    // Aggiorna lo stato del carrello
                    subtotaleCarrello.value = data.subtotale;
                    totaleCarrello.value = data.totale;
                    scontato.value = data.scontato;

                    // Rimuove chiavi non più presenti nel carrello 
                    for (const key in quantitaCarrello) {
                        if (!carrello.hasOwnProperty(key)) {
                            delete quantitaCarrello[key];
                            delete dettagliCarrello.value[key];
                        }
                    }

                    // Aggiorna prodotti nel carrello
                    for (const key in carrello) {
                        quantitaCarrello[key] = carrello[key].quantita;
                        dettagliCarrello.value[key] = carrello[key];
                    }

                    // Aggiorna quantità totale
                    let totale = 0;
                    for (const key in carrello) {
                        totale += carrello[key].quantita;
                    }
                    numProdottiCarrello.value = totale;

                    // Aggiorna il carrello visualizzato
                    await caricaCarrello();
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
            dettagliCarrello,
            numProdottiCarrello,
            quantitaCarrello,
            subtotaleCarrello,
            scontato,
            totaleCarrello,
            aggiornaCarrello,
        }
    }

}).mount('#app');