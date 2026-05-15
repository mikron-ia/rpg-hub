function showSecrets() {
    $(".confidential").removeClass("redacted");
    $(".secret").show();
    $("#secrets-show").hide();
    $("#secrets-hide").show();
}

function hideSecrets() {
    $(".confidential").addClass("redacted");
    $(".secret").hide();
    $("#secrets-show").show();
    $("#secrets-hide").hide();
}
