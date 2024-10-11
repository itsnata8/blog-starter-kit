<p>Dear <b><?= $mail_data['user']->name; ?></b></p>
<br>
<p>
    Your password has been changed. Please login with your new password. Here are your new login credentials:
    <br><br>
    <b>Login ID:</b> <?= $mail_data['user']->username; ?> or <?= $mail_data['user']->email; ?>
    <br>
    <b>Password:</b> <?= $mail_data['new_password']; ?>
</p>
<br><br>
Please, keep your login credentials safe.
<br>
------------------------------------------------
<p>
    This email was automatically generated. Do not reply to this email.
</p>