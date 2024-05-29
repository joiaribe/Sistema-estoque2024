<!-- echo out the system feedback (error and success messages) -->
<?php $this->renderFeedbackMessages(); ?>

<div class="clearfix"></div>

<div class="page_title">
    <div class="container">
        <div class="title"><h1>Cadastre-se</h1></div>
        <div class="pagenation">&nbsp;<a href="<?php echo URL; ?>index/index">Principal</a> <i>/</i> Cadastre-se</div>
    </div>
</div><!-- end page title --> 

<div class="clearfix"></div>


<!-- Contant
======================================= -->

<div class="container">

    <div class="content_fullwidth">

        <div class="one_half">
            <form action="<?php echo URL; ?>login/register_action" method="post">

                <fieldset>

                    <h1>Cadastrar</h1>

                    <label for="name" class="blocklabel">Seu Nome*</label>
                    <p class="error" ><input name="yourname" class="input_bg" type="text" id="name" value=''/></p>
                    
                    <label for="user_name" class="blocklabel">Usuário (ou e-mail)*</label>
                    <p class="" ><input name="login_input_email" class="input_bg" type="text" id="user_name" value=''/></p>


                    <label for="user_password_new" class="blocklabel">Senha*</label>
                    <p class="" ><input name="user_password_new" class="input_bg" type="password" id="user_password" pattern=".{6,}" value='' /></p>

                    <label for="user_password_repeat" class="blocklabel">Confirmar Senha*</label>
                    <p class="" ><input name="user_password_repeat" class="input_bg" type="password" id="user_password" pattern=".{6,}" value='' /></p>


                    <label for="user_password" class="blocklabel">Senha*</label>
                    <!-- show the captcha by calling the login/showCaptcha-method in the src attribute of the img tag -->
                    <!-- to avoid weird with-slash-without-slash issues: simply always use the URL constant here -->
                    <img id="captcha" src="<?php echo URL; ?>login/showCaptcha" />
                    <span style="display: block; font-size: 11px; color: #999; margin-bottom: 10px">
                        <!-- quick & dirty captcha reloader -->
                        <a href="#" onclick="document.getElementById('captcha').src = '<?php echo URL; ?>login/showCaptcha?' + Math.random();
                                return false">[ Recarregar Captcha ]</a>
                    </span>
                    <p class="" ><input name="captcha" class="input_bg" type="text" id="captcha"/></p>

                    <div class="clearfix"></div>
                    <input name="register" type="submit" value="Cadastrar" class="comment_submit" id="send"/></p>

                </fieldset>

            </form> 

        </div>

        <div class="one_half last">
            <?php if (FACEBOOK_LOGIN == true) { ?>
                <div class="login-facebook-box">
                    <h1>Ou</h1>
                    <a href="<?php echo $this->facebook_register_url; ?>" class="facebook-login-button">Registre com o Facebook</a>

                </div>
            <?php } ?>
        </div>

    </div>

</div><!-- end content area -->
