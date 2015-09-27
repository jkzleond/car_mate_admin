<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<form action="/emu/anhui" method="post">
    <input name="client" type="hidden" value="<?php echo $client; ?>"/>
    <input name="hpzl" type="text" placeholder="号牌种类"/>
    <input name="hphm" type="text" placeholder="号牌"/>
    <input name="fdjh" type="text" placeholder="发动机号"/>
    <input name="captcha" type="text" placeholder="验证码"/>
    <img src="data:image/png;base64,<?php echo $img_data; ?>" alt=""/>
    <input type="submit" value="提交"/>
</form>
</body>
</html>