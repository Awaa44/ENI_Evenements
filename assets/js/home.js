document.addEventListener("DOMContentLoaded", function () {

    const cbInscrit = document.getElementById("filtreInscrit");
    const cbNonInscrit = document.getElementById("filtreNonInscrit");

    function appliquerFiltres()
    {
        const site = document.getElementById("filtreSite").value;
        const nom = document.getElementById("inputSearch").value;
        const dateDebut = document.getElementById("inputDateDebut").value;
        const dateFin = document.getElementById("inputDateFin").value;
        const organisateur = document.getElementById("filtreOrganisateur").checked;
        const inscrit = cbInscrit.checked;
        const nonInscrit = cbNonInscrit.checked;
        const passees = document.getElementById("filtrePassees").checked;

        const params = new URLSearchParams();

        // -------- Site
        if (site) params.append("idSite", site);

        // -------- Nom
        if (nom) params.append("nomSortie", nom);

        // -------- Dates
        if ((dateDebut && !dateFin) || (!dateDebut && dateFin)) {
            return;
        }

        if (dateDebut && dateFin) {
            params.append("dateDebut", dateDebut);
            params.append("dateFin", dateFin);
        }

        // -------- Checkbox
        if (organisateur) params.append("organisateur", 1);
        if (inscrit) params.append("inscrit", 1);
        if (nonInscrit) params.append("nonInscrit", 1);
        if (passees) params.append("passees", 1);

        fetch("/home/filtrer?" + params.toString())
            .then(response => response.text())
            .then(html => {
                document.getElementById("tabHome").innerHTML = html;
            });
    }

    // 🔥 Gestion exclusivité inscrit / nonInscrit
    cbInscrit.addEventListener("change", function () {
        if (this.checked) {
            cbNonInscrit.checked = false;
        }
        appliquerFiltres();
    });

    cbNonInscrit.addEventListener("change", function () {
        if (this.checked) {
            cbInscrit.checked = false;
        }
        appliquerFiltres();
    });

    // Autres listeners
    document.getElementById("filtreSite")
        .addEventListener("change", appliquerFiltres);

    document.getElementById("inputSearch")
        .addEventListener("input", appliquerFiltres);

    document.getElementById("inputDateDebut")
        .addEventListener("change", appliquerFiltres);

    document.getElementById("inputDateFin")
        .addEventListener("change", appliquerFiltres);

    document.getElementById("filtreOrganisateur")
        .addEventListener("change", appliquerFiltres);

    document.getElementById("filtrePassees")
        .addEventListener("change", appliquerFiltres);

});
