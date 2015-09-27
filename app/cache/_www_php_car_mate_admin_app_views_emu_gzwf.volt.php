<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>gzwf</title>
</head>
<body>
<form action="/emu/gzwf" method="post">
    <input name="client" type="hidden" value="<?php echo $client; ?>"/>
    <input name="hpzl" type="text"/>
    <input name="hphm" type="text"/>
    <input name="fdjh" type="text"/>
    <input name="captcha" type="text"/>
    <img src="data:image/png;base64,<?php echo $img_data; ?>" alt=""/>
    <input type="submit" value=""/>
</form>
</body>
</html>