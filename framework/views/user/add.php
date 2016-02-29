<div class="form-container">
<?php
    if($user == null) {
        echo '<h2>Add a user</h2>';
        $formRenderer = new CBootstrap3FormRenderer($form);
        echo $formRenderer->asHtml();
    }
    else {
        echo '<h2>User &ldquo;' . htmlentities($user->firstName . ' ' . $user->lastName,ENT_COMPAT,'utf-8') . '&rdquo; was successfully added.</h2>';
        echo '<a href="/user/view/" title="Go back to user list">Go back to user list</a>';
    }
?>
</div>