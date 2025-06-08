const { createApp, ref, onMounted, reactive } = Vue;

createApp({
    setup() {

        // Variabili ref
        const messaggio = ref("")
        const messaggioOrdine = ref("");
        const prodotti = ref([])
        const numProdottiCarrello = ref(0)
        const dettagliCarrello = ref({});
        const subtotaleCarrello = ref(0);
        const totaleCarrello = ref(0);
        const scontato = ref(false);
        const orderId = ref(null);
        const dettagliOrdine = ref(null);

        // Oggetto reattivo per quantità specifica per prodotto nel carrello
        const quantitaCarrello = reactive({});

        // Funzione per caricare i prodotti dal database
        const caricaProdotti = async () => {
            try {
                const res = await fetch('./api/getProdotti.php')
                const data = await res.json()
                prodotti.value = data

            } catch (error) {
                messaggio.value = 'Errore nel caricamento prodotti';
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

                    // Se c'è almeno un prodotto, resetta il messaggio Ordine
                    if (totale > 0) {
                        messaggioOrdine.value = "";
                    }
                }
            } catch (error) {
                messaggio.value='Errore caricamento carrello';
            }
        }

        // Funzione per aaggiornare carrello
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
                    messaggio.value="Errore API";
                }
            } catch (error) {
                messaggio.value="Errore di rete";
            }
        };

        // Funzione per caricare i dettagli di un ordine specifico
        const caricaDettagliOrdine = async (id) => {
            try {
                const res = await fetch(`./api/getDettagliOrdine.php?id=${id}`);
                const data = await res.json();

                if (data.success) {
                    dettagliOrdine.value = data.ordine;
                } else {
                    messaggio.value="Errore nel caricamento dettagli ordine";
                }
            } catch (err) {
                messaggio.value="Errore rete";
            }
        };

        // Funzione per salvare l'ordine
        const salvaOrdine = async () => {
            // Controlla se il carrello è vuoto
            if (Object.keys(quantitaCarrello).length === 0) {
                messaggioOrdine.value = "Il carrello è vuoto. Aggiungi almeno un prodotto per procedere all'ordine.";
                return;
            }

            try {
                const articoli = Object.keys(dettagliCarrello.value).map(id => ({
                    prodotto_id: parseInt(id),
                    quantita: quantitaCarrello[id]
                }));

                const res = await fetch("./api/salvaOrdine.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ articoli })
                });

                const data = await res.json();

                if (data.success) {

                    // Salva id ordine generato
                    orderId.value = data.order_id;
                    // Carica i dettagli dell'ordine
                    await caricaDettagliOrdine(orderId.value);

                    // Reset carrello
                    dettagliCarrello.value = {};
                    for (const key in quantitaCarrello) delete quantitaCarrello[key];
                    numProdottiCarrello.value = 0;
                    subtotaleCarrello.value = 0;
                    totaleCarrello.value = 0;
                    scontato.value = false;

                    // Chiudi il modale del carrello
                    const cartModalEl = document.getElementById('cartModal');
                    const cartModal = bootstrap.Modal.getInstance(cartModalEl);
                    if (cartModal) cartModal.hide();

                    // Apri modale resoconto ordine
                    const orderModal = new bootstrap.Modal(document.getElementById('orderModal'));
                    orderModal.show();

                } else {
                    messaggio.value="Errore nel salvataggio dell'ordine";
                }

            } catch (err) {
                messaggio.value="Errore di rete";
            }
        };

        // Carica i prodotti e carrello al montaggio del componente
        onMounted(async () => {
            await caricaProdotti();
            await caricaCarrello();
        });

        return {
            messaggio,
            messaggioOrdine,
            prodotti,
            dettagliCarrello,
            numProdottiCarrello,
            quantitaCarrello,
            subtotaleCarrello,
            scontato,
            totaleCarrello,
            orderId,
            dettagliOrdine,
            aggiornaCarrello,
            salvaOrdine,
        }
    }

}).mount('#app');