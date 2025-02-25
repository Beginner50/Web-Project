<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <base href="/website/">
    <link rel="stylesheet" href="stylesheets/common.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body style="background-color: var(--duskSky);">
    <div class="container">
        <?php
        echo "<h2 style='text-align: center; color: rgb(53, 12, 12);'>" . $formType == "registration" ? 'Registration' : 'Login' . "Unsuccessful </h2>";
        foreach ($errors as $error) {
            echo "<div class='error-message'>" . $error .  "</div>";
        }
        echo "<a href='javascript:self.history.back()'><button class='indigoTheme roundBorder' style='margin-top: 15px; border-width: 4px; font-size:25px; padding:0px 15px;'> Back </button>";
        ?>
    </div>
</body>

</html>