
document.addEventListener("DOMContentLoaded", function () {
    function appliquerFiltres()
    {
        const site = document.getElementById("filtreSite").value;
        const nom = document.getElementById("inputSearch").value;

        const params = new URLSearchParams();

        if (site) {
            params.append("idSite", site);
        }

        if (nom) {
            params.append("nomSortie", nom);
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
});
