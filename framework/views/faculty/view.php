<div class="col-sm-12">
    <a href="/" title="Go back to course overview">Go back to course overview</a>
    <h2>Faculty list</h2>
        <a href="/faculty/add/" title="Add a new faculty">Add a new faculty</a>
        <table class="table">
            <tr>
                <th>Institution</th>
                <th>Faculty</th>
                <th>Actions</th>
            </tr>
<?php
        if(count($faculties) == 0) {
            echo '<tr><td colspan="2">There are currently no faculties entered onto the system</td>';
        }
        else {
            foreach($faculties as $faculty) {
                echo '<tr><td>' . htmlentities($faculty->institution->name,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($faculty->name,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td><a class="action-link" href="/faculty/edit/?facultyId=' . $faculty->facultyId . '" title="Edit faculty name">edit</a>';
                echo '<a class="confirm action-link" data-message="Are you sure you want to delete this faculty?" href="/faculty/delete/?facultyId=' . $faculty->facultyId . '" title="Delete faculty">delete</a>';
                echo '</td></tr>';
            }
        }
?>    
        </table>
</div>
