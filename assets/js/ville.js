document.addEventListener("turbo:load", () => {

    const input = document.getElementById("inputSearch");
    const modal = document.getElementById("modalEdit");
    const editId = document.getElementById("editId");
    const editNom = document.getElementById("editNom");
    const editCodePostal = document.getElementById("editCodePostal");
    const form = document.getElementById("formEdit");

    /* =========================
       FILTRAGE
    ========================== */
    input.addEventListener("input", filtreVille);

    function filtreVille() {
        const nomVille = input.value;

        fetch("/ville/filtrer?nom=" + encodeURIComponent(nomVille))
            .then(response => response.text())
            .then(html => {
                document.getElementById("tabVille").innerHTML = html;

                // 🔥 Re-attacher les événements après mise à jour du tableau
                attachModalEvents();
                attachAddEvent();
            });
    }

    /* =========================
       MODAL
    ========================== */

    function attachModalEvents() {

        const btns = document.querySelectorAll(".btn-modifier");

        btns.forEach(btn => {

            btn.addEventListener("click", function (e) {
                e.preventDefault();

                editId.value = this.dataset.id;
                editNom.value = this.dataset.nom;
                editCodePostal.value = this.dataset.codepostal;

                form.action = "/ville/modifier/" + this.dataset.id;

                modal.style.display = "flex";
            });

        });
    }

    /* =========================
       FERMETURE MODAL
    ========================== */

    function closeModalFunction() {
        modal.style.display = "none";
    }

    // Bouton X
    document.addEventListener("click", function (e) {
        if (e.target.id === "closeModal") {
            closeModalFunction();
        }

        // Fermer si clic en dehors de la modal
        if (e.target === modal) {
            closeModalFunction();
        }
    });

    /* =========================
                AJOUT
    ========================= */

    function attachAddEvent()
    {

        const btnAdd = document.getElementById("btnAjouterVille");

        if (!btnAdd) return;

        btnAdd.addEventListener("click", function (e) {
            e.preventDefault();

            const nom = document.getElementById("addNomVille").value.trim();
            const codePostal = document.getElementById("addCodePostal").value.trim();

            // Validation JS
            if (nom === "" || codePostal === "") {
                alert("Tous les champs sont obligatoires");
                return;
            }

            if (!/^[0-9]{5}$/.test(codePostal)) {
                alert("Le code postal doit contenir 5 chiffres");
                return;
            }

            fetch("/ville/ajouter", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "nom=" + encodeURIComponent(nom) +
                    "&codePostal=" + encodeURIComponent(codePostal)
            })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => { throw new Error(text); });
                    }
                    return response.text();
                })
                .then(html => {
                    document.getElementById("tabVille").innerHTML = html;

                    // Réattacher les events
                    attachModalEvents();
                    attachAddEvent();
                })
                .catch(error => {
                    alert(error.message);
                });
        });
    }
    attachModalEvents();
    attachAddEvent();

});
