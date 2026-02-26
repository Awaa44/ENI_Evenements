
document.addEventListener("DOMContentLoaded", function () {
    function appliquerFiltres()
    {
        const site = document.getElementById("filtreSite").value;
        const nom = document.getElementById("inputSearch").value;
        const dateDebut = document.getElementById("inputDateDebut").value;
        const dateFin = document.getElementById("inputDateFin").value;
        const organisateur = document.getElementById("filtreOrganisateur").checked;
        const inscrit = document.getElementById("filtreInscrit").checked;
        const nonInscrit = document.getElementById("filtreNonInscrit").checked;
        const passees = document.getElementById("filtrePassees").checked;

        const params = new URLSearchParams();

        //--------------------------------Filtre sites--------------------------------------//
        if (site) {
            params.append("idSite", site);
        }
        //--------------------------------Filtre nomSortie--------------------------------------//
        if (nom) {
            params.append("nomSortie", nom);
        }
        //--------------------------------Filtre dates--------------------------------------//
        if ((dateDebut && !dateFin) || (!dateDebut && dateFin)) {
            return;
        }
        if (dateDebut && dateFin) {
            params.append("dateDebut", dateDebut);
            params.append("dateFin", dateFin);
        }
        //--------------------------------Filtre checkbox--------------------------------------//
        if (inscrit && nonInscrit) {
            alert("Vous ne pouvez pas sélectionner les deux filtres inscrit et non inscrit.");
            return;
        }
        if (organisateur) {
            params.append("organisateur", 1);
        }
        if (inscrit) {
            params.append("inscrit", 1);
        }

        if (nonInscrit) {
            params.append("nonInscrit", 1);
        }

        if (passees) {
            params.append("passees", 1);
        }

        fetch("/home/filtrer?" + params.toString())
            .then(response => response.text())
            .then(html => {
                document.getElementById("tabHome").innerHTML = html;
            });
    }

    document.getElementById("filtreSite")
        .addEventListener("change", appliquerFiltres);

    document.getElementById("inputSearch")
        .addEventListener("input", appliquerFiltres);

    document.getElementById("inputDateDebut")
        .addEventListener("change", appliquerFiltres);

    document.getElementById("inputDateFin")
        .addEventListener("change", appliquerFiltres);

    document.querySelectorAll("input[type=checkbox]")
        .forEach(cb => cb.addEventListener("change", appliquerFiltres));
});
