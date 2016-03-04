<div class="col-sm-9">
    <h2>Course list</h2>
        <a href="<?= $webroot ?>/course/add/" title="Add a new course">Add a new course</a>
        <table class="table">
            <tr>
                <th>Code</th>
                <th>Title</th>
                <th>Presentation</th>
                <th>Institution</th>
                <th>Faculty</th>
                <th>Owner</th>
                <th>Actions</th>
            </tr>
<?php
        if(count($courses) == 0) {
            echo '<tr><td colspan="6">There are currently no courses entered onto the system</td>';
        }
        else {
            foreach($courses as $course) {
                echo '<tr><td>' . htmlentities($course->code,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td><a href="/workload/view/?courseId=' . $course->courseId .'">' . htmlentities($course->title,ENT_COMPAT,'utf-8') . '</a></td>';
                echo '<td>' . htmlentities($course->presentation,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($course->faculty->institution->name,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($course->faculty->name,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($course->owner->firstName . ' ' . $course->owner->lastName,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>';
                echo '<a href="/course/edit/?courseId=' . $course->courseId . '">edit</a><br />';
                echo '<a class="confirm" data-confirm="Are you sure you want to delete this course?" href="/course/delete/?courseId=' . $course->courseId . '">delete</a><br />';
                echo '<a href="/workload/export/?courseId=' . $course->courseId . '">export&nbsp;detail</a><br />';
                echo '<a href="/workload/export-summary/?courseId=' . $course->courseId . '">export&nbsp;summary</a>';
                echo '</td></tr>';
            }

        }
?>
        </table>
</div>

<div class="col-sm-3" role="navigation">
    <h2>Users</h2>
    <ul class="list">
        <li><a href="<?= $webroot ?>/user/add/" title="Add new user">Add a new user</a></li>
        <li><a href="<?= $webroot ?>/user/view/" title="List users">List users</a></li>
    </ul>
<?php
    if($this->application->user->isSuperAdministrator()) {
        echo '<h2>Institutions</h2>';
        echo '<ul class="list">';
        echo '<li><a href="/institution/add/" title="Add new institution">Add a new institution</a></li>';
        echo '<li><a href="/institution/view/" title="List institutions">List institutions</a></li>';
        echo '</ul>';
    }
    if($this->application->user->isAdministrator()) {
        echo '<h2>Faculties</h2>';
        echo '<ul class="list">';
        echo '<li><a href="/faculty/add/" title="Add a new faculty">Add a new faculty</a></li>';
        echo '<li><a href="/faculty/view/" title="List faculties">List faculties</a></li>';
        echo '</ul>';
    }
?>

</div>
