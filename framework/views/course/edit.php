<div class="form-container">
<h2>Edit course</h2>
<?php
    if($course == null) {
        $formRenderer = new CBootstrap3FormRenderer($form);
        echo $formRenderer->asHtml();
    }
    else {
        echo '<h2>Course &ldquo;' . htmlentities($course->title,ENT_COMPAT,'utf-8') . '&rdquo; was successfully edited.</h2>';
        echo '<a href="/" title="Go back to course list">Go back to course list</a>';
    }
?>
</div>