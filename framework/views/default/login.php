<div class="login well" style="width: 350px; margin-top:100px; margin-left:auto; margin-right: auto;">
    <h2>login</h2>
<?php
    $formRenderer = new CBootstrap3FormRenderer($loginForm);
    echo $formRenderer->asHtml();
?>
</div>