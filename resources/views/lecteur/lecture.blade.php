<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $memoire->titre }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl sm:px-6 lg:px-8">
            <a href="{{ route('lecteur.detail', $memoire) }}" class="mb-3 inline-block text-sm text-gray-600 hover:underline">
                ← Retour à la fiche du mémoire
            </a>

            @if ($limitePages)
                <div class="mb-3 rounded-md bg-yellow-50 p-3 text-sm text-yellow-800">
                    Aperçu limité aux {{ $limitePages }} premières pages.
                    <a href="{{ route('lecteur.obtenir', $memoire) }}" class="font-medium underline">Obtenir le mémoire complet</a>
                </div>
            @endif

            <div class="rounded-lg bg-white p-4 shadow">
                <div class="mb-3 flex items-center justify-center gap-3 text-sm">
                    <button id="precedent" type="button"
                        class="rounded-md bg-gray-100 px-3 py-1.5 hover:bg-gray-200 disabled:opacity-40">Précédent</button>
                    <span id="compteur" class="min-w-24 text-center text-gray-600">Chargement…</span>
                    <button id="suivant" type="button"
                        class="rounded-md bg-gray-100 px-3 py-1.5 hover:bg-gray-200 disabled:opacity-40">Suivant</button>
                </div>

                <div id="zone" class="overflow-auto text-center">
                    <canvas id="page" class="mx-auto max-w-full"></canvas>
                </div>

                <p id="finApercu" class="mt-3 hidden rounded-md bg-yellow-50 p-3 text-center text-sm text-yellow-800">
                    Fin de l'aperçu. Pour lire la suite, faites une demande du mémoire complet.
                </p>
                <p id="erreur" class="mt-3 hidden text-center text-sm text-red-600"></p>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        const urlPdf = @json(route('lecteur.pdf', $memoire));
        const limite = @json($limitePages);

        pdfjsLib.GlobalWorkerOptions.workerSrc =
            'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        const canvas = document.getElementById('page');
        const contexte = canvas.getContext('2d');
        const zone = document.getElementById('zone');
        const compteur = document.getElementById('compteur');
        const boutonPrecedent = document.getElementById('precedent');
        const boutonSuivant = document.getElementById('suivant');
        const finApercu = document.getElementById('finApercu');
        const erreur = document.getElementById('erreur');

        let document_pdf = null;
        let numero = 1;
        let pagesLisibles = 0;
        let totalPages = 0;
        let enCours = false;

        function mettreAJour() {
            compteur.textContent = 'Page ' + numero + ' / ' + pagesLisibles;
            boutonPrecedent.disabled = numero <= 1;
            boutonSuivant.disabled = numero >= pagesLisibles;
            // Message affiché seulement sur la dernière page d'un aperçu tronqué
            const apercuTronque = limite !== null && totalPages > pagesLisibles;
            finApercu.classList.toggle('hidden', !(apercuTronque && numero === pagesLisibles));
        }

        async function afficher(n) {
            enCours = true;
            const page = await document_pdf.getPage(n);
            const base = page.getViewport({ scale: 1 });
            const echelle = Math.min((zone.clientWidth || 800) / base.width, 2);
            const vue = page.getViewport({ scale: echelle });

            canvas.width = vue.width;
            canvas.height = vue.height;
            await page.render({ canvasContext: contexte, viewport: vue }).promise;

            numero = n;
            enCours = false;
            mettreAJour();
        }

        boutonPrecedent.addEventListener('click', function () {
            if (!enCours && numero > 1) { afficher(numero - 1); }
        });
        boutonSuivant.addEventListener('click', function () {
            if (!enCours && numero < pagesLisibles) { afficher(numero + 1); }
        });

        pdfjsLib.getDocument(urlPdf).promise
            .then(function (doc) {
                document_pdf = doc;
                totalPages = doc.numPages;
                pagesLisibles = limite !== null ? Math.min(limite, totalPages) : totalPages;
                return afficher(1);
            })
            .catch(function () {
                compteur.textContent = '';
                erreur.textContent = 'Impossible de charger le fichier PDF.';
                erreur.classList.remove('hidden');
            });
    </script>
</x-app-layout>