import Inputmask from "inputmask";

document.addEventListener("DOMContentLoaded", function () {
    Inputmask("999.999.999-99").mask(document.getElementById("cpf"));
    Inputmask("9999 9999 9999 9999").mask(document.getElementById("card-number"));
    Inputmask("99/99").mask(document.getElementById("expiry-date"));
    Inputmask("999").mask(document.getElementById("cvc"));
});
