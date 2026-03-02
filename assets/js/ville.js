document.addEventListener("turbo:load", initHome);

function initVille()
{
    const nomVille = document.getElementById("inputSearch").value;

    fetch("/ville/filtrer?" + nomVille.toString())
        .then(response => response.text())
        .then(html => {
            document.getElementById("tabVille").innerHTML = html;
        });

}
