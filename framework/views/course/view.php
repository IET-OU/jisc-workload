<div class="col-sm-12">
    <a href="/" title="Go back to course overview">Go back to course overview</a>
    <h2>User list</h2>
        <a href="/user/add/" title="Add a new user">Add a new user</a>
        <table class="table">
            <tr>
                <th>Login</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Institution</th>
                <th>Actions</th>
            </tr>
<?php
        if(count($users) == 0) {
            echo '<tr><td colspan="6">There are currently no users entered onto the system</td>';
        }
        else {
            foreach($users as $user) {
                echo '<tr><td>' . htmlentities($user->login,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($user->firstName . ' ' . $user->lastName,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($user->email,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($user->accessLevelText,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td>' . htmlentities($user->institution->name,ENT_COMPAT,'utf-8') . '</td>';
                echo '<td><a class="action-link" href="/user/edit/?userId=' . $user->userId . '" title="Edit user">edit</a>';
                echo '<a class="confirm action-link" data-message="Are you sure you want to delete this user?" href="/user/delete/?userId=' . $user->userId . '" title="Delete user">delete</a>';
                echo '</td></tr>';
            }
        }
?>    
        </table>
</div>
