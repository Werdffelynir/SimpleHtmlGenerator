<?php
define('TIMER', microtime(true));




include 'source/HtmlGet.php';

$html = new HtmlGen();
$html->debug = false;


/*  */
$html->constructor(array(
    'wrapper'=>array
    (
        'header',
        'topmenu',
        'content'=>array(
            'side-left',
            'side-right',
        ),
        'footer',
    )
));


/*  */
$html->scripts('demo/js/jquery.js', 1);
$html->scripts('demo/js/main.js', 2);


/*  */
$html->styles('demo/css/reset.css', 1);
$html->styles('demo/css/main.css', 2);


/* Test data array */
$data = include 'demo/database/dataIndex.php';
$getRequest = (isset($_GET['page']))?$_GET['page']:'0';


/**/
$img = $html->wrap('<img src="http://werd.id1945.com/theme/werdfolio/images/wordspop.png" />');

/*  */
$html->inner('header',  $img.$data['header'], array('class'=>'grid clear'));
$html->inner('topmenu', $data['topmenu'], array('class'=>'grid clear'));
$html->inner('content', null, array('class'=>'grid clear'));
$html->inner('footer',  $data['footer'], array('class'=>'grid clear'));
$html->inner('side-left',$data['side-left'],array('class'=>'grid-3 first'));
$html->inner('side-right',$data['side-right'][$getRequest],array('class'=>'grid-9 '));

/**/
$html->innerAppend('footer','data','<p style="text-align:center;">Время генерациии составило '.round(microtime(true)-TIMER,4).' сек.</p>');

/**/
$html->output();