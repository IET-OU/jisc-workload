<div class="form-container">
<?php
    echo '<h2>Institution &ldquo;' . htmlentities($institution->name,ENT_COMPAT,'utf-8') . '&rdquo; was successfully deleted.</h2>';
    echo '<a href="/institution/view/" title="Go back to institution list">Go back to institution list</a>';
?>
</div>