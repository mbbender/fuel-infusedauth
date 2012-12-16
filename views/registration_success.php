<script type="text/javascript">
    function send_validation_ajax()
    {
        $('#resend_button').attr('disabled','disabled');
        $('#message').fadeOut(function(){
            $('#message').text("Sending verification email")
        }).fadeIn();
        $.post('<?php echo isset($resend_action) ? $resend_action : 'resend_verification_request' ?>','user_id=<?php echo $user_id?>', function(data){
            if(data.success)
            {
                $('#heading').text('Resend completed successfully.');

                $('#message').fadeOut(function(){
                    $('#message').text('An email has been sent to '+data.email)
                }).fadeIn();
            }
            else
            {
                $('#heading').text('Resend failed.');

                $('#message').fadeOut(function(){
                    $('#message').text('A problem occurred sending your email. Please try again or contact support.')
                }).fadeIn();
            }
            $('#resend_button').removeAttr('disabled');
        }, 'json')
        .error(function(xhr){
                $('#heading').text('Resend failed.');

                $('#message').fadeOut(function(){
                    $('#message').text('A problem occurred sending your email. Please try again or contact support.')
                }).fadeIn();
                $('#resend_button').removeAttr('disabled');
            })

    }
</script>

<div class="hero-unit">
    <h1>Registration Success</h1>
    <h3 id="heading">Account activation required</h3>
    <p id="message">Please check your email to validate your account. If you have not received your email in a few minutes please check your SPAM folder. You can resend
    the email by clicking the button below.</p>
    <p>
        <button id="resend_button" class="btn btn-primary btn-large" onclick="send_validation_ajax()">Resend verification email</button>
    </p>
</div>
