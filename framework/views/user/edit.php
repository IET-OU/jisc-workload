<div class="form-container">
<h2>Edit user</h2>
<?php
    if($user == null) {
        $formRenderer = new CBootstrap3FormRenderer($form);
        echo $formRenderer->asHtml();
    }
    else {
        echo '<h2>User &ldquo;' . htmlentities($user->firstName . ' ' . $user->lastName,ENT_COMPAT,'utf-8') . '&rdquo; was successfully edited.</h2>';
        echo '<a href="/user/view/" title="Go back to user list">Go back to user list</a>';
    }
?>
</div>