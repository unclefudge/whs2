// Post data to url via POST method
function postAndRedirect(url, postData) {
    var postFormStr = "<form method='POST' action='" + url + "'>\n";

    for (var key in postData) {
        if (postData.hasOwnProperty(key))
            postFormStr += "<input type='hidden' name='" + key + "' value='" + postData[key] + "'></input>";
    }

    postFormStr += "</form>";
    var formElement = $(postFormStr);

    $('body').append(formElement);
    $(formElement).submit();
}