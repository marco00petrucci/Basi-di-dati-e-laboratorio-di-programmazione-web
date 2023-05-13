$(function () {

    // Chiamata AJAX alla digitazione nella casella di testo
    $("#testoCerca").keyup(function () {
        $.ajax({
            type: "get",
            url: "read_categoria.php",
            data: 'testo=' + $(this).val(),
            success: function (data) {
                $("#autocompletamento").show().html(data);
            }
        });

        // Mantieni "hover" al keyup
        $('#cerca').css({ "width": "186px", "background": "rgba(255, 255, 255, 1)" });
        $('#testoCerca').css({
            "visibility": "visible",
            "width": "140px",
            "height": "26px",
            "margin": "2px 0 0 3px",
            "padding": "0 2% 0 4%",
            "color": "rgba(0, 0, 0, .7)"
        });
    });

    // Inserisci testo filtrato nel box cerca al click
    $(document).on("click", ".result", function () {
        $("#testoCerca").val($(this).text());
        $("#autocompletamento").hide();
    });

    // Visualizza il subnav all'hover tranne che nei telefoni
    if ($(window).width() >= 950) {
        $("#utente").hover(function () {
            $("#subnav").fadeIn(50);
            $('#dropdown_btn').css("color", "#000");
        }, function () {
            $('#subnav').fadeOut(50);
            $('#dropdown_btn').css("color", "#fff");
        });
    }

    // Timeout al click del bottone dashboard
    $("#go_to_dashboard").click(function () {
        $("#go_to_dashboard").html('<img src="image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "reg-login/dashboard.php"', 500);
        $("#go_to_dashboard").css("cursor", "wait");
    });

    // Timeout al click del bottone aggiungi blog
    $("#add_blog").click(function () {
        $("#add_blog").html('<img src="image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "reg-login/new_blog.php"', 500);
        $("#add_blog").css("cursor", "wait");
    });

    // Timeout al click del bottone gestisci commenti
    $("#manage_comments").click(function () {
        $("#manage_comments").html('<img src="image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "reg-login/commenti.php"', 500);
        $("#manage_comments").css("cursor", "wait");
    });

    // Timeout al click del bottone gestisci utenti
    $("#manage_users").click(function () {
        $("#manage_users").html('<img src="image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "reg-login/users.php"', 500);
        $("#manage_users").css("cursor", "wait");
    });

    // Timeout al click del bottone gestisci messaggi
    $("#manage_messaggi").click(function () {
        $("#manage_messaggi").html('<img src="image/ajax-loader.gif" alt="Loader" />&nbsp;Attendi...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "reg-login/messaggi.php"', 500);
        $("#manage_messaggi").css("cursor", "wait");
    });

    // Timeout al click del bottone logout
    $("#logout").click(function () {
        $("#logout").html('<img src="image/ajax-loader.gif" alt="Loader" />&nbsp;Log out...');
        $("body").delay(300).fadeOut();
        setTimeout('window.location.href = "index.php?logout"', 500);
        $("#logout").css("cursor", "wait");
    });

    // Onclick --> Vai all'inizio della pagina
    $("#button_top").click(function () {
        $("html").animate({ scrollTop: "0" }, 500);
    });

    // Animazione degli avvisi del sito
    setTimeout(function () {
        $(".avviso").css({ "padding": "1%", "left": "29%" }), 1000;
        $(".avviso:not(.no_timeout)").fadeOut(2500);
    });

    $(window).scroll(function () {

        // Quando la pagina viene scrollata in *SU* a meno di 50px
        if ($(document).scrollTop() < 50) {
            // Aggiunge svg e toglie nav bianco
            $('#nav_1').css({
                "transform": "translateY(-100%)",
                "background": "rgb(255, 255, 255, .8)",
                "box-shadow": "none"
            });
            $('#nav_2').css("transform", "translateY(0%)");
            $('header').css("margin", "1.5% 2%");

            // Cambia il logo da nero a bianco
            $('#logo').attr("src", "image/logo.png");
            $("#logo").hover(function () {
                $("#logo").attr("src", "image/logo_nero.png");
            }, function () {
                $("#logo").attr("src", "image/logo.png");
            });

            // Setta di bianco il colore dei paragrafi "p" e collegamenti "a" di header
            $("#dropdown_btn, header a, header p:not(#autocompletamento p), #divisore").css("color", "#fff");
            $("header a, header p:not(#autocompletamento p)").hover(function () {
                $(this).css("color", "#000");
            }, function () {
                $(this).css("color", "#fff");
            });

            $("#utente").hover(function () {
                $('#dropdown_btn, #user_name').css("color", "#000");
            }, function () {
                $('#dropdown_btn, #user_name').css("color", "#fff");
            });

            // Togli box-shadow al campo cerca all'hover
            $("#cerca").hover(function () {
                $('#cerca').css("box-shadow", "none");
            }, function () {
                $('#cerca').css("box-shadow", "none");
            });
        }

        // Quando la pagina viene scrollata in *GIÙ* di 50px
        else {
            // Toglie svg e aggiunge nav bianco
            $('#nav_1').css({
                "height": "80px",
                "transform": "translateY(0%)",
                "background": "rgb(255, 255, 255, 1)",
                "box-shadow": "0 0 10px 0 rgba(0, 0, 0, 0.5)"
            });
            $('#nav_2').css("transform", "translateY(-100%)");
            $('header').css("margin", "0 3%");
            $('#logo').attr("src", "image/logo_nero.png");
            $("#logo").hover(function () {
                $("#logo").attr("src", "image/logo_nero.png");
            }, function () {
                $("#logo").attr("src", "image/logo_nero.png");
            });

            // Setta di nero il colore dei paragrafi "p" e collegamenti "a" di header
            $("#dropdown_btn, header a, header p:not(#autocompletamento p), #divisore").css("color", "#000");
            $("header a, header p:not(#autocompletamento p)").hover(function () {
                $(this).css("color", "rgba(0, 0, 0, .8)");
            }, function () {
                $(this).css("color", "#000");
            });

            $("#utente").hover(function () {
                $('#dropdown_btn, #user_name').css("color", "rgba(0, 0, 0, .8)");
            }, function () {
                $('#dropdown_btn, #user_name').css("color", "#000");
            });

            // Aggiungi box-shadow al campo cerca all'hover
            $("#cerca").hover(function () {
                $('#cerca').css("box-shadow", "0px 0px 5px 0 rgba(0, 0, 0, 0.5)");
            }, function () {
                $('#cerca').css("box-shadow", "none");
            });
        }

        // Fissa aside allo scroll
        if ($(window).width() >= 1200) {
            if ($(this).scrollTop() > 300) {
                var position = $(window).scrollTop(); // Conta di quanto viene scrollata la pagina
                var bottom = $(document).height() - $(window).height(); // Altezza totale della pagina
                $('aside').css({ 'position': 'fixed', 'top': '-130px', "transition": "none" });
                if (window.matchMedia("(max-width: 1600px)").matches) $('aside').css({ 'top': '-300px', "transition": "none" }); // Per gli schermi fino a 1600px di larghezza
                if (position >= bottom) {
                    $('aside').css({ 'position': 'fixed', 'top': '-30%', "transition": ".1s ease-out" });
                    if (window.matchMedia("(max-width: 1600px)").matches) $('aside').css({ 'top': '-60%', "transition": ".1s ease-out" }); // Per gli schermi fino a 1600px di larghezza
                }
            } else $('aside').css({ 'position': 'absolute', 'top': '20%', "transition": "none" });
        }

        // Mostra icona Go to Top quando la pagina viene scrollata di 500px tranne che nei telefoni
        if ($(window).width() >= 950) {
            if ($(document).scrollTop() > 500) $('#button_top').fadeIn(200);
            else $('#button_top').fadeOut(300);
        }
    });
});