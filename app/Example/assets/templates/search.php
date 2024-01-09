<head>
    <?php include_once 'public/templates/html_core/head.php'; ?>
</head>
<body>
    <a href="/">Main</a>

    <h1>Search Personnel</h1>
    <form method="get" action="/records/search">
        <label for="forename">Firstame</label><br>
        <input type="text" name="forename" value="J"><br>
        <input type="submit" value="Search Database">
    </form>

    <hr>

    <h1>Search By One or Many input values</h1>
    <form method="post" action="/records/discover">
        <label for="forename">Forename</label><br>
        <input type="text" name="forename" value=""><br>

        <label for="surname">Surname</label><br>
        <input type="text" name="surname" value=""><br>

        <label for="email">Email</label><br>
        <input type="text" name="email" value=""><br>

        <label for="description">Description</label><br>
        <input type="text" name="description" value=""><br>
        
        <b>Modify with caution</b><br>
        <label for="_csrf">CSRF Token</label><br>
        <input type="text" name="_csrf" value="<?php echo $csrf ?>"><br>

        <label for="_method">Http Method</label><br>
        <input type="text" name="_method" value="post"><br>

        <input type="submit" value="Search Database">
    </form>

    <hr>


    <h1>Create</h1>
    <div style="font-family:sans-serif; margin:20px 0px; padding:24px; color:rgba(200,200,200,1); background:rgba(36,36,50,1);">
        <b>Notice</b><br>
        Posting data from this form will send the request, but not creation action will be made.<br>
        This section was to test the validation of the CSRF token being sent.<br><br>
        Data presented is being fetched.
    </div>

    <form method="post" action="/records/search">
        <label for="forename">First name</label><br>
        <input type="text" name="forename" value="John"><br>

        <label for="surname">Last name</label><br>
        <input type="text" name="surname" value="Shepard"><br>
        <input type="submit" value="Search  Database">
        
        <br><br><br>
        <b>Modify with caution</b><br>
        <label for="_csrf">CSRF Token</label><br>
        <input type="text" name="_csrf" value="<?php echo $csrf ?>"><br><br>

        <label for="_method">Http Method</label><br>
        <input type="text" name="_method" value="put"><br>
        
    </form>
</body>