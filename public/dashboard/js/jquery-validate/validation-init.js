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
                nome: {
                    required: true,
                    minlength: 2
                },
                cpf: {
                    required: true,
                    minlength: 12
                },
                money: {
                    required: true
                },
                comissao: {
                    required: true
                },
                titulo: {
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
                qnt: {
                    required: true
                },
                topic: {
                    required: "#newsletter:checked",
                    minlength: 2
                },
                agree: "required"
            },
            messages: {
                firstname: "Digite o primeiro nome",
                cpf: "Digite o CPF",
                nome: "Digite um nome",
                money: "Digite um valor",
                qnt: "Digite uma quantidade",
                comissao: "Digite uma comissão",
                titulo: "Digite um título",
                lastname: "Digite o sobrenome",
                username: {
                    required: "Por favor insira um nome de usuário",
                    minlength: "Seu nome de usuário deve ser composta por pelo menos 2 caracteres"
                },
                password: {
                    required: "Por favor, forneça uma senha",
                    minlength: "Sua senha deve ter pelo menos 5 caracteres"
                },
                confirm_password: {
                    required: "Por favor, forneça uma senha",
                    minlength: "Sua senha deve ter pelo menos 5 caracteres",
                    equalTo: "Por favor, digite a mesma senha que acima"
                },
                email: "Por favor insira um endereço de e-mail válido",
                agree: "Por favor, aceite a nossa política"
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