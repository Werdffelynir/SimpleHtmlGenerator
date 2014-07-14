<?php

/* Test data array */
$data = include 'lib/data.php';

/*  */
include 'lib/HtmlGet.php';

/*  */
$html = new HtmlGen();

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
$html->scripts('js/jquery.js', 1);
$html->scripts('js/main.js', 2);

/*  */
$html->styles('css/reset.css', 1);
$html->styles('css/main.css', 2);

/* Request test */
$getRequest = (isset($_GET['page']))?$_GET['page']:'0';

$img = $html->wrap('<img src="http://werd.id1945.com/theme/werdfolio/images/wordspop.png" />');

/*  */
$html->add('wrapper',  null, array('style'=>'width:690px;margin:25px auto;'));
$html->add('header',  $img.$data['header'], array('class'=>'grid clear'));
$html->add('topmenu', $data['topmenu'], array('class'=>'grid clear'));
$html->add('content', null, array('class'=>'grid clear'));
$html->add('footer',  $data['footer'], array('class'=>'grid clear'));
$html->add('side-left',$data['side-left'],array('class'=>'grid-3 first'));
$html->add('side-right',$data['side-right'][$getRequest],array('class'=>'grid-9 '));

$html->output();