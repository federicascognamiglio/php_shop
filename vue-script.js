const { createApp, ref, onMounted } = Vue;

createApp({
    setup() {
        const messaggio = ref("")
        const prodotti = ref([])
        const numProdottiCarrello = ref(0)

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


        // Carica i prodotti al montaggio del componente
        onMounted(caricaProdotti)

        return {
            messaggio,
            prodotti,
            numProdottiCarrello,
            aggiornaCarrello,
        }
    }

}).mount('#app');