var Script = function() {

    $.validator.setDefaults({
        submitHandler: function() {
            submit();
            //alert("submitted!");
        }
    });

    $().ready(function() {
        // validate the comment form when it is submitted
        $("#commentForm").validate();

        // validate signup form on keyup and submit
        $("#signupForm").validate({
            rules: {
                firstname: "required",
                lastname: "required",
                username: {
                    required: true,
                    minlength: 2
                },
                picture: {
                    required: true
                },
                full_name: {
                    required: true,
                    minlength: 6
                },
                ocupation: {
                    required: true,
                    minlenght: 3
                },
                year: {
                    required: true,
                    minlength: 4
                },
                password: {
                    required: true,
                    minlength: 5
                },
                confirm_password: {
                    required: true,
                    minlength: 5,
                    equalTo: "#password"
                },
                email: {
                    required: true,
                    email: true
                },
                topic: {
                    required: "#newsletter:checked",
                    minlength: 2
                },
                agree: "required"
            },
            messages: {
                firstname: "Digite seu primeiro nome",
                picture: "Por Favor insira uma imagem",
                lastname: "Please enter your lastname",
                username: {
                    required: "Por favor insira um nome de usuário",
                    minlength: "Your username must consist of at least 2 characters"
                },
                full_name: {
                    required: "Por favor insira um nome e sobrenome",
                    minlength: "O nome e sobrenome precisa ter pelo menos 6 caracteres"
                },
                ocupation: {
                    required: "Por favor insira uma ocupação",
                    minlength: "A ocupação precisa ter pelo menos 3 caracteres"
                },
                password: {
                    required: "Por favor, forneça uma senha",
                    minlength: "Sua senha deve ter pelo menos 5 caracteres"
                },
                confirm_password: {
                    required: "Por favor, forneça uma senha",
                    minlength: "Sua senha deve ter pelo menos 5 caracteres",
                    equalTo: "Por favor, digite a mesma senha como acima"
                },
                email: "Por favor insira um endereço de e-mail válido",
                agree: "Please accept our policy"
            }
        });

        // propose username by combining first- and lastname
        $("#username").focus(function() {
            var firstname = $("#firstname").val();
            var lastname = $("#lastname").val();
            if (firstname && lastname && !this.value) {
                this.value = firstname + "." + lastname;
            }
        });

        //code to hide topic selection, disable for demo
        var newsletter = $("#newsletter");
        // newsletter topics are optional, hide at first
        var inital = newsletter.is(":checked");
        var topics = $("#newsletter_topics")[inital ? "removeClass" : "addClass"]("gray");
        var topicInputs = topics.find("input").attr("disabled", !inital);
        // show when newsletter is checked
        newsletter.click(function() {
            topics[this.checked ? "removeClass" : "addClass"]("gray");
            topicInputs.attr("disabled", !this.checked);
        });
    });


}();