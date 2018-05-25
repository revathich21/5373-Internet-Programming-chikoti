

$(document).ready(function () {
    $.get("app.php/navigation")
        .done(function (data) {
            data = data.data;
            console.log(data);
            $('#main-nav').html("");
            var html = "";
            // for (var k in data) {
            //     if (data.hasOwnProperty(k)) {
            //         console.log("Key is " + k + ", value is" + data[k].text);
            //         html += '<li class="nav-item"><a class="nav-link" href="#">' + data[k].text +
            //             '</a></li>\n';

            //     }
            // }
            $('#main-nav').html(html);


        });
});