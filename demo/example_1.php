<?php

include '../source/HtmlGet.php';


# Создание в конструкторе каркаса будущей страницы
$html = new HtmlGen(
['wrapper' =>
    [
        'header' =>
            [
                'logoImg',
                'logoTmg'
            ],
        'topmenu',
        'content' =>
            [
                'left',
                'right'
            ],
        'footer'
    ]
]);


# Подключаю файлы стилей и скриптов
$html->styles(['css/reset.css',1],['css/demo.css']);


# Добавляю стилей
$html->stylesheet('
body{background-color:#260032;}
#wrapper{margin: 0 auto; width: 600px; background-color:#FFF;}
#header{height:60px;}
#logoImg{height:60px;background-color:#7F79ED;}
#logoTmg{height:60px;background-color:#6450AB;}
#topmenu{height:20px;background-color:#4A0697;}
#content{height:600px;}
#left{height: 500px;background-color:#70EDB2;}
#right{height: 600px;background-color:#6AA7AB;}
#footer{height: 50px;background-color:#7F79ED;}
');


$html->inner('header',null,array('class'=>'grid clear'));
    $html->inner('logoImg',null,array('class'=>'grid-5 first'));
    $html->inner('logoTmg',null,array('class'=>'grid-7'));
$html->inner('topmenu',null,array('class'=>'grid clear'));
$html->inner('content',null,array('class'=>'grid clear'));
    $html->inner('left',null,array('class'=>'grid-4 first'));
    $html->inner('right',null,array('class'=>'grid-8'));
$html->inner('footer',null,array('class'=>'grid clear'));


$html->output();