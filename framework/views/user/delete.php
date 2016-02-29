<div class="form-container">
<?php
    echo '<h2>User &ldquo;' . htmlentities($user->firstName . ' ' . $user->lastName,ENT_COMPAT,'utf-8') . '&rdquo; was successfully deleted.</h2>';
    echo '<a href="/user/view/" title="Go back to user list">Go back to user list</a>';
?>
</div>