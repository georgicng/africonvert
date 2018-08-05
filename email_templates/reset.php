 <?php $this->layout('layout') ?>

<p style="color:#5b5f65; font-size:28px; text-align:left; font-family: Verdana, Geneva, sans-serif">Hi <?=$this->e($name)?>, </p>
<div style="color:#5b5f65; font-size:16px; text-align:left; font-family: Verdana, Geneva, sans-serif">
    <p>We got a request to reset your Afriflow password. </p>
    <p>Here is your newly generated password: <strong><?=$this->e($password)?></strong></p>
    <p>You can now use the generated password to log into your account.</p>
</div>