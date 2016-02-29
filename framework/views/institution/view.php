<div class="col-sm-12">
    <a href="/" title="Go back to course overview">Go back to course overview</a>
    <h2>Institution list</h2>
        <a href="/institution/add/" title="Add a new institution">Add a new institution</a>
        <table class="table">
            <tr>
                <th>Institution</th>
                <th>Actions</th>
            </tr>
<?php
        if(count($institutions) == 0) {
            echo '<tr><td colspan="2">There are currently no institutions entered onto the system</td>';
        }
        else {
            foreach($institutions as $institution) {
                echo '<tr><td>' . htmlentities($institution->name,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td><a class="action-link" href="/institution/edit/?institutionId=' . $institution->institutionId . '" title="Edit institution name">edit name</a>';
                echo ' <a class="confirm action-link" data-message="Are you sure you want to delete this institution?" href="/institution/delete/?institutionId=' . $institution->institutionId . '" title="Delete institution">delete</a>';
                echo '</td></tr>';
            }
            
        }
?>    
        </table>
</div>
