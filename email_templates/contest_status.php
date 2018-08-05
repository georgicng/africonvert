 <?php $this->layout('layout') ?>

<p style="color:#5b5f65; font-size:28px; text-align:left; font-family: Verdana, Geneva, sans-serif">Hi <?=$this->e($name)?>, </p>
<div style="color:#5b5f65; font-size:16px; text-align:left; font-family: Verdana, Geneva, sans-serif">
    <p>The status for contest "<?=$this->e($title)?>" has been update to <?=$this->e($status)?>. </p>
    <p> <a href="<?=$this->e($link)?>">View Contest</a></p>
    <p>Cheers</p>
</div>
