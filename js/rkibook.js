$(document).ready(function () {
    send_request(0, $("#id_rkibookid").val());

    $('.rkibook-form-control').keypress(function (event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (event.keyCode === 13) {
            event.preventDefault();
            document.getElementById("rkibook-search").click();
        }
    });
});

$("#rkibook-search").click(function () {
    send_request();
});

function send_request(page = 0, rkibookid = 0) {
    var filter = $(".rki-filter"),
        title = $("#filter-title").val();

    $.ajax({
        url: M.cfg.wwwroot + "/mod/rkibook/ajax.php?action=getlist&page=" + page + "&title=" + title + "&rkibookid=" + rkibookid
    }).done(function (data) {
        clear_details();

        // set data
        $("#rki-items-list").scrollTop(0);
        $("#rki-items-list").html(data.html);

        // set details click listener
        $(".rkibook-select").click(function () {
            $(".rki-item").removeClass("rki-item-selected");
            set_details($(this).data("id"));
            $(this).parent().parent().parent().addClass("rki-item-selected");
        });

        if (rkibookid > 0) {
            $('#rki-items-details').html(data.details);
        }

        // pagination
        $(".rki-page").click(function () {
            send_request($(this).data('page'));
        });
    });
}

function set_details(id) {
    var title = $("#rki-item-title-" + id).html();

    $("#id_rkibookid").val(id);
    $("#id_name").val(title.substring(title.lastIndexOf(">") + 1));
    $("#rki-item-detail-image").html($("#rki-item-image-" + id).html());
    $("#rki-item-detail-title").html(title);
    $("#rki-item-detail-pubhouse").html($("#rki-item-pubhouse-" + id).html());
    $("#rki-item-detail-authors").html($("#rki-item-authors-" + id).html());
    $("#rki-item-detail-pubyear").html($("#rki-item-pubyear-" + id).html());
    $("#rki-item-detail-description").html($("#rki-item-description-" + id).html());
    $("#rki-item-detail-keywords").html($("#rki-item-keywords-" + id).html());
    $("#rki-item-detail-pubtype").html($("#rki-item-pubtype-" + id).html());

    // var rb = $("#rki-item-detail-read");
    // rb.attr("href", $("#rki-item-url-" + id).attr("href"));
    // if ($("#rki-item-url-" + id).attr("href")) {
    //     rb.show();
    // }
}

function clear_details() {
    $("#rki-item-detail-image").html('');
    $("#rki-item-detail-title").html('');
    $("#rki-item-detail-pubhouse").html('');
    $("#rki-item-detail-authors").html('');
    $("#rki-item-detail-pubyear").html('');
    $("#rki-item-detail-description").html('');
    $("#rki-item-detail-keywords").html('');
    $("#rki-item-detail-pubtype").html('');
}