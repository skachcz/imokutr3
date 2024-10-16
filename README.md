# imokutr3

PHP image thumbnailer created with primary use for Nette framework 3.x, but also usable as a standalone PHP library.

## The basic philosophy

Imokutr3 is PHP library designed to automatically generate thumbnails from original images.

It was created with the following assumptions in mind:

1. We have some original images in directories.

Example:

```
/disk0/web/files/articles/2018/10/image.jpg
/disk0/web/files/gallery/pigs/pig01.png
/disk0/web/files/wallpaper/bifFish.png

/disk0/web/files/wallpaper/bifFish.png


/disk0/web/files/default.jpg    <- default image, when source image doesn't exists

```

2. We have relative paths to these images stored somewhere, like for example in a database:

Example:

```
id |title                 | url
1  | Blue image           | /articles/2018/10/Blueimage.jpg
2  ! Cute pig at our farm | /gallery/pigs/pig01.png
3  | Big fish             | /wallpaper/bifFish.png
```

3. We want to easily create and show listing images, perex images or gallery thumbnails images created from original images on a web site.

# Nette 3.x integration

## Composer
```
composer install skachcz/imokutr3
```

## Nette extension registration in neon.config and configuration:

```

extensions:
    imokutr3: SkachCz\Imokutr3\DI\Nette\ImokutrExtension

imokutr3:
    originalRootPath: %wwwDir%/files
    thumbsRootPath: %wwwDir%/thumbs
    thumbsRootRelativePath: /thumbs
    defaultImageRelativePath: default.jpg
    qualityJpeg: 85
    qualityPng: 8

```

## What we can do in *.latte template:

Parameters - width, height, resizeType, cropParameter

width (mandatory) - Width in pixels for thumbnail.

height (mandatory) - Height in pixels for thumbnail.

resizeType (optional) - default is 'w'

'w' - thumbnail width will be same as width parameter, thumbnail height is calculated according to image ratio
'h' - thumbnail height will be same as height parameter, thumbnail width is calculated according to image ratio
'c' - crop - thumbnail will be cropped to specific width and height

cropParameter (optional) 0-8 - default is 0

```
  ↖    ↑   ↗
    1  2  3
 ←  8  0  4  →
    7  6  5
  ↙    ↓   ↘
```

## Nette Filter

```
{'/files/original/image1.jpg'|imoUrl:100:200:'h'}
```

## Macro

Inside the macro code, you can use placeholders %url%, %width% and %height%:

```
{imoTag '/files/original/image1.jpg', 300, 150, 'w'}
<img src="%url%" width="%width%" height="%height%" alt="my alt" title="my title">
{/imoTag}

{imoTag '/files/original/image1.jpg', 300, 150, 'h'}
<img src="%url%" width="%width%" height="%height%" alt="my alt" title="my title">
{/imoTag}

{imoTag '/files/original/image1.jpg', 300, 150, 'c'}
<img src="%url%" width="%width%" height="%height%" alt="my alt" title="my title">
{/imoTag}

{imoTag '/files/original/image1.jpg', 300, 150, 'c', 5}
<img src="%url%" width="%width%" height="%height%" alt="my alt" title="my title">
{/imoTag}
```

# Standalone library

'''
composer require skachcz/imokutr3
'''


```
<?php

require_once(__DIR__ . "/vendor/autoload.php");

use SkachCz\Imokutr3\Imokutr;
use SkachCz\Imokutr3\ImokutrConfig;
use SkachCz\Imokutr3\Html;

$config = new ImokutrConfig();

$config->setConfig(
   __DIR__ . "/img/imokutr/original",
   __DIR__ . "/img/imokutr/thumbs",
   "/eso-tools/imokutr3/img/imokutr/thumbs",
   "default.jpg"
   );

$kutr = new Imokutr($config);

<?php
// original image is in /images/original/img1.jpg
$img = $kutr->getThumbnail("img1.jpg", 200, 100, 'w'); ?>

<img src="<?php echo $img['url'] ?>" width="<?php echo $img['width'] ?>" height="<?php echo $img['height'] ?>">

or

<?php echo Html::img($img) ?>

<?php  $img = $kutr->getThumbnail("img1.jpg", 300, 300, 'c', 5); ?>

<?php echo Html::img($img, "Photo", "My photo", ["data-text" => "My text", "onclick" => 'alert("box")', "class" => "red"] ) ?>
```