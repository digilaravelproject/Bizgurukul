$(document).ready(function () {
    // AJAX Setup
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Handle Form Submit
    $(document).on("submit", ".ajax-form", function (e) {
        e.preventDefault();

        let form = $(this);
        let btn = form.find(".submit-btn");
        let btnText = btn.text();

        // Disable Button & Show Loading
        btn.prop("disabled", true).text("Saving...");

        // Clear Previous Errors
        form.find(".error-text").text("");
        form.find(".form-control").removeClass("is-invalid");

        $.ajax({
            url: form.attr("action"),
            method: form.attr("method"),
            data: new FormData(this),
            processData: false,
            dataType: "json",
            contentType: false,
            success: function (response) {
                if (response.success) {
                    toastr.success(response.message);

                    // Thoda wait karke redirect karo
                    setTimeout(function () {
                        window.location.href = response.redirect;
                    }, 1000);
                }
            },
            error: function (xhr) {
                btn.prop("disabled", false).text(btnText);

                if (xhr.status === 422) {
                    // Validation Errors show karna
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function (key, val) {
                        // Input field ke neeche error message dalna
                        // Note: Input name array ho sakta hai (e.g. roles[]), uske liye fix
                        let inputName = key.replace(".", "_");
                        $("span." + inputName + "_error").text(val[0]);
                        $('input[name="' + key + '"]').addClass("is-invalid");
                    });
                } else {
                    toastr.error("Something went wrong! Please try again.");
                }
            },
        });
    });
});
