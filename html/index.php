<html lang="en">
    <body>
        <form action="/" method="post">
            <label>
                Username
                <input type="text" name="username" required/>
            </label>
            <label>
                Password
                <input type="password" name="password" required/>
            </label>
            <button type="submit">Login</button>
        </form>
    </body>
</html>

<?php

if ($_POST) {
    echo htmlspecialchars($_POST['username']);
}