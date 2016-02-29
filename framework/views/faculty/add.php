<div class="form-container">
<?php
    if($faculty == null) {
        echo '<h2>Add a faculty</h2>';
        $formRenderer = new CBootstrap3FormRenderer($form);
        echo $formRenderer->asHtml();
    }
    else {
        echo '<h2>Faculty &ldquo;' . htmlentities($faculty->name,ENT_COMPAT,'utf-8') . '&rdquo; was successfully added.</h2>';
        echo '<a href="/faculty/view/" title="Go back to faculty list">Go back to faculty list</a>';
    }
?>
</div>