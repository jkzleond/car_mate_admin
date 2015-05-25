<?php
/**
 * Created by PhpStorm.
 * User: jkzleond
 * Date: 15-3-20
 * Time: 下午1:11
 */

class ValidatecodeController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->disable();
        $this->response->setContentType('image/jpeg');
        $texts = array(
            '2', '3', '4', '5', '6', '7', '8', '9',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H',
            'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R',
            'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'
        );

        $rands = array();

        for($i = 0; $i < 4; $i++)
        {
            $rand_key = array_rand($texts);
            $rands[] = $texts[$rand_key];
        }

        $validate_code = join('', $rands);

        $this->session->set('validate_code', $validate_code);
        //创建图片
        $img_width = 80;
        $img_height = 27;
        $font_size = 18;
        $font_file = realpath('./fonts/roboto/Roboto-Black-webfont.ttf');
        $img = imagecreate($img_width, $img_height);
        $bg_color = imagecolorallocate($img, 255, 255, 255);
        imagefill($img, 0, 0, $bg_color);




        //绘制验证码
        foreach($rands as $offset=>$rand_text)
        {
            $r = rand(32, 187);
            $g = rand(32, 187);
            $b = rand(32, 187);

            $color = imagecolorallocate($img, $r, $g, $b);
            imagettftext($img, $font_size, rand(-20, 20), $offset*$font_size + ($img_width - $font_size*count($rands)), round(($img_height-$font_size)/2) + $font_size, $color, $font_file, $rand_text);
        }


        //绘制随机线条
        $line_color = imagecolorallocatealpha($img, 187, 238, 187, 175);

        for($i = 0; $i < 50; $i++)
        {
            $x = rand(0, $img_width);
            $y = rand(0, $img_height);
            $dx = rand(0, 12);
            $dy = rand(0, 12);
            imageline($img, $x, $y, $x + $dx, $y + $dy, $line_color);
        }

        imagejpeg($img);
    }
}