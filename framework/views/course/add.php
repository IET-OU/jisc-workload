<div class="form-container">
<?php
    if($course == null) {
        echo '<h2>Add a course</h2>';
        $formRenderer = new CBootstrap3FormRenderer($form);
        echo $formRenderer->asHtml();
    }
    else {
        echo '<h2>Course &ldquo;' . htmlentities($course->title,ENT_COMPAT,'utf-8') . '&rdquo; was successfully added.</h2>';
        echo '<a href="/" title="Go back to course list">Go back to course list</a>';
    }
?>
</div>