<?php
/**
 * @param $imageDefault
 * @param $textDefault
 * @param $background
 * @param string $filename
 * @param array $config
 *
 * 图片合成 带二维码的海报
 */
function getbgqrcode($imageDefault, $textDefault, $background, $filename = "", $config = array())
{
//如果要看报什么错，可以先注释调这个header
    if (empty($filename)) {
        header("content-type: image/png");
    }
//背景方法

    $backgroundInfo = getimagesize($background);

    $ext = image_type_to_extension($backgroundInfo[2], false);
    $backgroundFun = 'imagecreatefrom' . $ext;
    $background = $backgroundFun($background);
    $backgroundWidth = imagesx($background);  //背景宽度
    $backgroundHeight = imagesy($background);  //背景高度
    $imageRes = imageCreatetruecolor($backgroundWidth, $backgroundHeight);
    $color = imagecolorallocate($imageRes, 0, 0, 0);
    imagefill($imageRes, 0, 0, $color);
    imagecopyresampled($imageRes, $background, 0, 0, 0, 0, imagesx($background), imagesy($background), imagesx($background), imagesy($background));
//处理了图片
    if (!empty($config['url'])) {
        $val['url'] = $config['url'];
        $val = array_merge($imageDefault, $val);

        $info = getimagesize($val['url']);

        $function = 'imagecreatefrom' . image_type_to_extension(2, false);
        if ($val['stream']) {
//如果传的是字符串图像流
            $info = getimagesizefromstring($val['url']);
            $function = 'imagecreatefromstring';
        }
        $res = $function($val['url']);
        $resWidth = $info[0];
        $resHeight = $info[1];
//建立画板 ，缩放图片至指定尺寸
        $canvas = imagecreatetruecolor($val['width'], $val['height']);
        imagefill($canvas, 0, 0, $color);
//关键函数，参数（目标资源，源，目标资源的开始坐标x,y, 源资源的开始坐标x,y,目标资源的宽高w,h,源资源的宽高w,h）
        var_dump($function, $val['url']);
        exit;
        imagecopyresampled($canvas, $res, 0, 0, 0, 0, $val['width'], $val['height'], 300, 300);
        $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) - $val['width'] : $val['left'];
        $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) - $val['height'] : $val['top'];
//放置图像
        imagecopymerge($imageRes, $canvas, $val['left'], $val['top'], $val['right'], $val['bottom'], $val['width'], $val['height'], $val['opacity']);//左，上，右，下，宽度，高度，透明度
    }
//处理文字
    if (!empty($config['text'])) {
        foreach ($config['text'] as $key => $val) {
            $val = array_merge($textDefault, $val);
            list($R, $G, $B) = explode(',', $val['fontColor']);
            $fontColor = imagecolorallocate($imageRes, $R, $G, $B);
            $val['left'] = $val['left'] < 0 ? $backgroundWidth - abs($val['left']) : $val['left'];
            $val['top'] = $val['top'] < 0 ? $backgroundHeight - abs($val['top']) : $val['top'];
            imagettftext($imageRes, $val['fontSize'], $val['angle'], $val['left'], $val['top'], $fontColor, $val['fontPath'], $val['text']);
        }
    }
//生成图片
    if (!empty($filename)) {
        $res = imagejpeg($imageRes, $filename, 90);
//保存到本地
        imagedestroy($imageRes);
    } else {
        imagejpeg($imageRes);
//在浏览器上显示
        imagedestroy($imageRes);
    }


}

?>