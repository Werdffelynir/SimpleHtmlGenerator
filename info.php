<?php
include 'lib/HtmlGet.php';

$html = new HtmlGen();

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


/*
$html->scripts('js/jquery.js', 1);
$html->scripts('js/main.js', 2);
*/
$html->scripts(
    ['js/main.js'],
    ['js/jquery.js', 1],
    ['js/plugin.js',10]
);

/*
$html->styles('css/reset.css', 1);
$html->styles('css/main.css', 2);*/
$html->styles(
    ['css/reset.css', 1],
    ['css/main.css']
);


$html->stylesheet('/*some css*/');
$html->javascript('/*some javascript*/');

$wrapData = $html->wrap($data, array('class'=>'grid-8 '));

/*
$html->inner(array(
	'id'=>'header',
	'data'=>$data,
	'attr'=>array('class'=>'grid clear'),
	)); 
*/
$data = 'Some data';
$html->inner('wrapper',  null, array('style'=>'width:690px;margin:0 auto;background-color:#110055;color:#FFAAFF;padding:4px;'));
$html->inner('header',  $data, array('class'=>'grid clear'));
$html->inner('topmenu', $data, array('class'=>'grid clear'));
$html->inner('content', null, array('class'=>'grid clear'));
$html->inner('footer',  $data, array('class'=>'grid clear'));
$html->inner('side-left',$data,array('class'=>'grid-4 first'));
$html->inner('side-right',$data,array('class'=>'grid-8 '));



$html->output();
