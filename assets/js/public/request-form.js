
(function($) {
    "use strict";

    /* Move Form Fields Label When User Types */
    // for input and textarea fields
    $("input, textarea").keyup(function(){
        if ($(this).val() != '') {
            $(this).addClass('notEmpty');
        } else {
            $(this).removeClass('notEmpty');
        }
    });


    /* Request Form */
    $("#requestForm").validator().on("submit", function(event) {
        if (event.isDefaultPrevented()) {
            // handle the invalid form...
            rformError();
            rsubmitMSG(false, "Completa tutti i campi!");
        } else {
            // everything looks good!
            event.preventDefault();
            rsubmitForm();
        }
    });

    $(".team-member").click(function(){
        var category = $(this).attr("data-category");
        $("#rselect").val(category)
        $('html, body').stop().animate({
            scrollTop: $("#section-4").offset().top
        }, 600, 'easeInOutExpo');
    })

    function rsubmitForm() {
        // initiate variables with form content
        var name = $("#rname").val();
        var email = $("#remail").val();
        var phone = $("#rphone").val();
        var select = $("#rselect").val();
        var terms = $("#rterms").val();

        $.ajax({
            type: "POST",
            url: "/home/contact",
            data: "name=" + name + "&email=" + email + "&phone=" + phone + "&select=" + select + "&terms=" + terms,
            success: function(response) {
                if (!response.error) {
                    rformSuccess();
                } else {
                    rformError();
                    rsubmitMSG(false, text);
                }
            }
        });
    }

    function rformSuccess() {
        $("#requestForm")[0].reset();
        rsubmitMSG(true, "Grazie, ti contatteremo al più presto!");
        $("input").removeClass('notEmpty'); // resets the field label after submission
    }

    function rformError() {
        $("#requestForm").removeClass().addClass('shake animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function() {
            $(this).removeClass();
        });
    }

    function rsubmitMSG(valid, msg) {
        if (valid) {
            var msgClasses = "h3 text-center tada animated";
        } else {
            var msgClasses = "h3 text-center";
        }
        $("#rmsgSubmit").removeClass().addClass(msgClasses).text(msg);
    }

})(jQuery);