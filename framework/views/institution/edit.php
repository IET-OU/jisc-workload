<div class="form-container">
<?php
    if($institution == null) {
        $formRenderer = new CBootstrap3FormRenderer($form);
        echo $formRenderer->asHtml();
    }
    else {
        echo '<h2>Institution &ldquo;' . htmlentities($institution->name,ENT_COMPAT,'utf-8') . '&rdquo; was successfully edited.</h2>';
        echo '<a href="/institution/view/" title="Go back to institution list">Go back to institution list</a>';
    }
?>
</div>