export function initVilleSelect() {
    document.getElementById('sortie_villes').addEventListener('change', function() {
        const villeId = this.value;
        if (villeId) {
            document.getElementById('lieux_villes').value = villeId;
            document.getElementById('lieu-select').innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '--Sélectionner un lieu--';
            document.getElementById('lieu-select').appendChild(defaultOption);
            //appel de la route ajax créée dans le controller
            fetch(`/sortie/ville/${villeId}`)
                .then(response => response.json())
                .then(data => {
                    //récupération des informations
                    for (const datum of data) {
                        const option = document.createElement('option');
                        option.value = datum.idLieux;
                        option.textContent = datum.nomLieux;
                        document.getElementById('lieu-select').appendChild(option);
                    }
                });
        } else {
            document.getElementById('lieu-select').innerHTML = '';
        }
    });
}
export function initLieuSelect() {
    document.getElementById('lieu-select').addEventListener('change', function() {
        const lieuId = this.value;
        if (lieuId) {
            //compléter le champ lieu avec le select-lieu custom
            document.getElementById('sortie_lieux').value = lieuId;
            //appel de la route ajax créée dans le controller
            fetch(`/sortie/lieu/${lieuId}`)
                .then(response => response.json())
                .then(data => {
                    //récupération des informations
                    document.getElementById('lieu-rue').textContent = data.rue;
                    document.getElementById('lieu-cp').textContent = data.codePostal;
                });
        } else {
            document.getElementById('lieu-rue').textContent = '';
            document.getElementById('lieu-cp').textContent = '';
        }
    });
}

export function initLieuForm() {
    document.querySelector('form[name="lieux"]').addEventListener('submit', function (e){
        e.preventDefault();

        const formData = new FormData(this);

        //utilisation de la route Ajax réalisée dans le controller
        fetch('/sortie/createLieu', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                // repeupler le select avec les lieux de la ville
                const villeId = document.getElementById('sortie_villes').value;
                fetch(`/sortie/ville/${villeId}`)
                    .then(response => response.json())
                    .then(lieux => {
                        document.getElementById('lieu-select').innerHTML = '';
                        const defaultOption = document.createElement('option');
                        defaultOption.value = '';
                        defaultOption.textContent = '--Sélectionner un lieu--';
                        document.getElementById('lieu-select').appendChild(defaultOption);

                        for (const datum of lieux) {
                            const option = document.createElement('option');
                            option.value = datum.idLieux;
                            option.textContent = datum.nomLieux;
                            document.getElementById('lieu-select').appendChild(option);
                        }

                        // ajouter dans le select caché symfony
                        const optionCache = document.createElement('option');
                        optionCache.value = data.id;
                        document.getElementById('sortie_lieux').appendChild(optionCache);

                        // sélectionner le nouveau lieu
                        document.getElementById('lieu-select').value = data.id;
                        document.getElementById('sortie_lieux').value = data.id;
                        document.getElementById('lieu-select').dispatchEvent(new Event('change'));

                        // fermer la popin
                        document.getElementById('maPopin').querySelector('[data-bs-dismiss="modal"]').click();
                    });
            });
    });
}

export function initEditMode(villeId, lieuId) {
    // 1. sélectionner la ville
    document.getElementById('sortie_villes').value = villeId;

    // 2. peupler le select avec les lieux de cette ville
    fetch(`/sortie/ville/${villeId}`)
        .then(response => response.json())
        .then(lieux => {
            document.getElementById('lieu-select').innerHTML = '';
            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = '--Sélectionner un lieu--';
            document.getElementById('lieu-select').appendChild(defaultOption);

            for (const datum of lieux) {
                const option = document.createElement('option');
                option.value = datum.idLieux;
                option.textContent = datum.nomLieux;
                document.getElementById('lieu-select').appendChild(option);
            }

            // 3. sélectionner le bon lieu et déclencher le change pour rue/cp
            document.getElementById('lieu-select').value = lieuId;
            document.getElementById('lieu-select').dispatchEvent(new Event('change'));
        });
}
