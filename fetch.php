<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-17
 * Time: 上午11:09
 */

$res = get_cookies_and_rand('http://2015.956122.com/pages/user/code.jsp?flag=rand');
$cookie = parse_cookie($res);
$rand = parse_rand($res);

$hphm = $argv[1];
$fdjh = $argv[2];
$hpzl = $argv[3];
$pname = '云南';

//echo $hphm.','.$fdjh.','.$hpzl.','.$pname;

$url = "http://2015.956122.com/peccancy.do?action=listwithoutlogin&hphm=$hphm&fdjh=$fdjh&hpzl=02&rand=$rand&pname=$pname";

echo $url."\n";

echo get_result($url, $cookie);

function get_result($url, $cookie){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function get_cookies_and_rand($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    curl_close($ch);
    return $res;
}

function parse_cookie($res){
    $cookie_pattern = '/Set-Cookie: ([^\s]*)/';
    preg_match_all($cookie_pattern, $res, $matches);
    file_put_contents('./res', var_export($matches[1], true));
    return implode('', $matches[1]);
}

function parse_rand($res){
    $rand_pattern = '/value=\"(.*)\"/U';
    preg_match_all($rand_pattern, $res, $matches);
    return $matches[1][0];
}