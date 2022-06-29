$(function() {

    // Timeout al click del bottone dashboard
    $("#go_to_dashboard").click(function() {
        $("#go_to_dashboard").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "dashboard.php"', 500);
        $("#go_to_dashboard").css("cursor", "wait");
    });

    // Timeout al click del bottone aggiungi blog
    $("#add_blog").click(function() {
        $("#add_blog").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "new_blog.php"', 500);
        $("#add_blog").css("cursor", "wait");
    });

    // Timeout al click del bottone gestisci commenti
    $("#manage_comments").click(function() {
        $("#manage_comments").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "commenti.php"', 500);
        $("#manage_comments").css("cursor", "wait");
    });

    // Timeout al click del bottone gestisci utenti
    $("#manage_users").click(function() {
        $("#manage_users").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "users.php"', 500);
        $("#manage_users").css("cursor", "wait");
    });

    // Timeout al click del bottone gestisci messaggi
    $("#manage_messaggi").click(function() {
        $("#manage_messaggi").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "messaggi.php"', 500);
        $("#manage_messaggi").css("cursor", "wait");
    });

    // Timeout al click del bottone logout
    $("#logout").click(function() {
        $("#logout").html('<img src="../image/ajax-loader.gif" alt="Loader" />&nbsp;Log out...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "../index.php?logout"', 500);
        $("#logout").css("cursor", "wait");
    });

    $(window).scroll(function() {
        // Quando la pagina viene scrollata in *SU* a meno di 50px setta di bianco il colore del logo
        if ($(document).scrollTop() < 50) {
            $('#logo').attr("src", "../image/logo.png");
            $("#logo").hover(function() {
                $("#logo").attr("src", "../image/logo_nero.png");
            }, function() {
                $("#logo").attr("src", "../image/logo.png");
            });
        }
        // Quando la pagina viene scrollata in *GIÙ* di 50px setta di nero il colore del logo
        else {
            $('#logo').attr("src", "../image/logo_nero.png");
            $("#logo").hover(function() {
                $("#logo").attr("src", "../image/logo_nero.png");
            }, function() {
                $("#logo").attr("src", "../image/logo_nero.png");
            });
        }
    });
});