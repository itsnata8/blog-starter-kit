<p>Dear <?= $mail_data['user']->name; ?></p>
<p>We have received a request to reset your password. Please click the button below to reset your password.
    <br><br>
    <a href="<?= $mail_data['actionLink'] ?>" style="color:#fff;border-color:#22bc66;border-style:solid;box-width:5px 10px;background-color:#22bc66;display:inline-block;text-decoration:none;border-radius:3px;box-shadow:0 2px 3px rgba(0,0,0,.15);-webkit-text-size-adjust:none;box-sizing:border-box;" target="_blank">Reset Password</a>
    <br><br>
    <b>NB:</b> This link will expire in 15 minutes.
    <br><br>
    If you did not request a password reset, please ignore this email.
</p>