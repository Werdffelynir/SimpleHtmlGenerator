<?php
/**
 * Simple HTML Generator
 *
 * Class HtmlGen
 * @date 07.15.2014
 */
class HtmlGen
{
    /** @var string $title, $lang, $charset Значения елементов HTML старицы */
    public $title = 'Title Page';
    public $lang = 'en';
    public $charset = 'UTF-8';

    public $debug = true;

    public $stylesheet = null;
    public $javascript = null;
    private $constructorData = array();
    private $contentData = array();
    private $_bodyPrepend = '';
    private $_bodyAppend = '';
    private $_output = '';
    private $_stylesData = array();
    private $_scriptsData = array();
    const NL = "\n";
    const TB = "\t";


    /**
     * @param array $data   конструктора сруктуры дерева. см constructor()
     */
    public function __construct(array $data = null)
	{
		if($data != null)
			$this->constructorData = $data;
	}


    /**
     * Назначает на создания дерева HTML страницы.
     * Елементы генерируются в div элементы, их строковые ключи для массивов и строковые значения становятся идинтификаторами
     * своего нода.
     *
     * <pre>
     * Пример:
     * $html->constructor(array
     * (
     *      'wrapper' => array
     *      (
     *          'header',
     *          'topmenu',
     *          'content' => array
     *          (
     *              'side-left',
     *              'side-right',
     *          ),
     *          'footer',
     *      )
     * ));
     * </pre>
     *
     * @param array $data   конструктора сруктуры дерева
     */
    public function constructor(array $data)
	{
		$this->constructorData = $data;	
	}


    /**
     * Назначение параметров для конструктора сруктуры дерева переданого методом constructor() или аргументом при
     * создании экземпляра класса
     *
     * <pre>
     * Пример 1:
     *
     * $html->add(array(
     *              'id'=>'header',
     *              'data'=>$data,
     *              'attr'=>array('class'=>'grid clear', 'другие'=>'атрибуты'),
     * ));
     *
     * Пример 2:
     * $html->inner('header', $data, array('class'=>'grid clear', 'другие'=>'атрибуты'));
     * </pre>
     *
     * Аргументы указываются не явным способом:<br>
     *
     * @param string|array      имя id нода в конструкторе сруктуры или массив с тремя этими параметрами
     * @param string            данные
     * @param array             массив атрибутов для елемента нода
     */
    public function inner()
	{
		$attributes = func_get_args();
		$attributesNum = func_num_args();
		if($attributesNum==1){
			if(is_array($attributes[0])){
				$id = (isset($attributes[0]['id']))?$attributes[0]['id']:$this->error('Ошибка. Необходимо указать идентификатор в массив "id"');
				$this->contentData[$id] = $attributes[0];
			} else {
                if($this->debug)
                    $this->error("Ошибка. И один в поле воин коль по-русски скроен!, но это всего лишь нода.");
            }

		}else if($attributesNum==3){
			$this->contentData[$attributes[0]] = array('id'=>$attributes[0],'data'=>$attributes[1],'attr'=>$attributes[2]);
		}else{
			$this->error('Ошибка. Не верное количество аргументов!');
		}
	}


    /**
     * Добавляет данные в указаный нод $name конструктора сруктуры, data или attr
     *
     * @param string $node      Имя нода в конструкторе сруктуры
     * @param string $name      Идинтивикатор элемента массива в ноде data или attr
     * @param string $append    Данные что добавляем
     */
    public function innerAppend($node, $name, $append)
    {
        $_names = array('data','attr');
        if(!in_array($name, $_names))
            $this->error('Ошибка. Второй аргумент может быть data или attr!');

        if(isset($this->contentData[$node])) {
            $this->contentData[$node][$name] .= self::NL.$append;
        }
    }

    /**
     * Возвращает елемент style c данными. Метод не валидирует css код
     *
     * @param null $data
     * @param bool $append
     * @return string
     */
    public function stylesheet($data=null, $append=true)
    {
        if($data != null){
            if($append) $this->stylesheet .= $data;
            else $this->stylesheet = $data;
        }else{
            if(!empty($this->stylesheet))
                return '<style type="text/css">'.$this->stylesheet.'</style>';
        }
    }


    /**
     * Назначение файлов стилей, вывод производитсяпри при полном отображении  output(true) <br>
     * <br>
     * Аргументы указываются не явным способом:<br>
     * - нет аргументов возвращает все подключенные прежде стили в элементе link <br>
     * - один аргумент string путь к скрипту <br>
     * - два аргумента string путь к скрипту и number приоритет <br>
     * - N количество - массивы с одим элементом string адресом или двумя адресом и number приоритетом
     * <pre>
     *
     * Пример:
     * // три различных подхода:
     * $html->styles('css/main.css');
     * $html->styles('css/reset.css', 1);
     *
     * $html->styles(
     *          ['css/reset.css', 1],
     *          ['css/main.css']
     * );
     * </pre>
     *
     * @param string|array
     * @param number|array
     * @param array ...
     * @return string
     */
    public function styles ()
    {
        $_data = func_get_args();
        if(count($_data)>0){
            foreach($_data as $data){
                if(is_array($data)){
                    if(count($data)==1)
                        $this->_stylesData[$data[0]] = 50;
                    else if(count($data)>=2)
                        $this->_stylesData[$data[0]] = $data[1];
                }else{
                    if(count($_data)==2)
                        $this->_stylesData[$_data[0]] = $_data[1];
                    else
                        $this->error("Ошибка. Не верные данные аргументов!");
                }
            }
        }else{
            $_scripts = '';
            asort($this->_stylesData, SORT_NUMERIC);
            foreach($this->_stylesData as $sdKey=>$sdVal){
                $_scripts .= self::TB.'<link type="text/css" rel="stylesheet" href="'.$sdKey.'"/>'."\n";
            }
            return $_scripts;
        }
    }


    /**
     * Элемент script для вложеного javascript кода в первый аргумент. Метод не валидирует javascript код
     *
     * @param null      $data       javascript код
     * @param bool      $append     добавить код, true замена
     * @return string
     */
    public function javascript($data=null, $append=true)
    {
        if($data != null){
            if($append) $this->javascript .= $data;
            else $this->javascript = $data;
        }else{
            if(!empty($this->javascript))
                return '<script type="text/javascript">'.$this->javascript.'</script>';
        }
    }


    /**
     * Назначение ссылок на файлы javascript скриптов , вывод производитсяпри при полном отображении  output(true) <br>
     * <br>
     * Аргументы указываются не явным способом:<br>
     * - нет аргументов возвращает все подключенные прежде стили в элементе link <br>
     * - один аргумент string путь к скрипту <br>
     * - два аргумента string путь к скрипту и number приоритет <br>
     * - N количество - массивы с одим элементом string адресом или двумя адресом и number приоритетом
     * <pre>
     *
     * Пример:
     * // три различных подхода:
     * $html->scripts('js/jquery.js', 1);
     * $html->scripts('js/main.js');
     *
     * $html->scripts(
     *          ['js/main.js']
     *          ['js/jquery.js', 1],
     * );
     * </pre>
     *
     * @param string|array
     * @param number|array
     * @param array ...
     * @return string
     */
    public function scripts ()
    {
        $_data = func_get_args();
        if(count($_data)>0){
            foreach($_data as $data){
                if(is_array($data)){
                    if(count($data)==1)
                        $this->_scriptsData[$data[0]] = 50;
                    else if(count($data)>=2)
                        $this->_scriptsData[$data[0]] = $data[1];
                }else{
                    if(count($_data)==2)
                        $this->_scriptsData[$_data[0]] = $_data[1];
                    else die('ERROR');
                }
            }
        }else{
            $_scripts = '';
            asort($this->_scriptsData, SORT_NUMERIC);
            foreach($this->_scriptsData as $sdKey=>$sdVal){
                $_scripts .= self::TB.'<script type="text/javascript" src="'.$sdKey.'"></script>'."\n";
            }
            return $_scripts;
        }

    }


    /**
     * Обертка елементом div. Возвращает елемент HTML c данными вложеными в первый аргумент и атрибутами вложеных в
     * второй аогумент.
     *
     * @param bool  $data   строка данных размещается между тегами
     * @param array $attrs  массив атрибутов
     * @return string
     */
    public function wrap($data=true, array $attrs = null)
    {
        $attr = '';
        if (!empty($attrs)){
            $attr = $this->extractorAttr($attrs);
        }
        return '<div '.$attr.'>'.$data.'</div>'.self::NL;
    }


    public function element($elem, $data=true, array $attrs = null)
    {
        $attr = '';
        $monoElements = array('img','input','button','br','hr');

        if(in_array($elem, $monoElements)){

            if (!empty($data)){
                if(!is_array($data))
                    $this->error("Второй аргумент должен быть массивом атрибутов!");
                $attr = $this->extractorAttr($data);
            }
            return '<'.$elem.' '.$attr.'/>'.self::NL;
        }else{
            if (!empty($attrs)){
                $attr = $this->extractorAttr($attrs);
            }
            return '<'.$elem.' '.$attr.'>'.$data.'</'.$elem.'>'.self::NL;
        }
    }


    /**
     * Накрутка дерева
     *
     * @param $tree
     * @param int $iter
     */
    private function builderTree($tree, $iter = 0) {

        if ($iter == 0) {
            $id = key($tree);
            $tree = $tree[$id];
            $bu = $this->builderData($id);
            $this->_output .= '<div id="'.$id.'" '.$bu['attr'].'>'.self::NL;
            $this->_output .= $bu['data'].self::NL;
            $bu = null;
        }

            foreach ($tree as $key => $val) {
                $iter++;
                if (is_array($val)) {

                    $bu = $this->builderData($key);

                    $this->_output .= '<div id="'.$key.'" '.$bu['attr'].'>'.self::NL;
                    $this->_output .= $bu['data'];

                    $this->builderTree($val, $iter);
                } else {

                    $bu = $this->builderData($val);

                    $this->_output .= '<div id="'.$val.'" '.$bu['attr'].'>'.self::NL;
                    $this->_output .= $bu['data'];
                    $this->_output .= '</div>'.self::NL;
                }
                $bu = null;
            }

        $this->_output .= '</div>'.self::NL;

    }


    /**
     * Накрутка на дерево данных
     * @param $key
     * @return array
     */
    private function builderData($key)
    {
        $attr = '';
        $data = '';

        if (isset($this->contentData[$key]))
        {
            $attr = $this->extractorAttr($this->contentData[$key]['attr']);
            $data = $this->contentData[$key]['data'];
        }

        return array(
            'attr' => $attr,
            'data' => $data
        );
    }


    /**
     * Добавляют переданные данные $data в налало всего содержимого между body.
     * Тоесть после открывающего тега body.
     *
     * @param string $data
     */
    public function bodyPrepend($data)
    {
        if(!is_string($data))
            $this->error("Ошибка. Передаваемый аргумент должен быть строкой!");
        $this->_bodyPrepend .= self::NL.$data.self::NL;
    }


    /**
     * Добавляют переданные данные $data в конец всего содержимого между body, иными словами перед
     * закрывающим тегом body;
     *
     * @param string $data
     */
    public function bodyAppend($data)
    {
        if(!is_string($data))
            $this->error("Ошибка. Передаваемый аргумент должен быть строкой!");
        $this->_bodyAppend .= self::NL.$data.self::NL;
    }


    /**
     * Вывод результата
     *
     * @param bool $withHtmlDoctype Полная сруктура или внутреняя между body
     * @param bool $return          возвратить результат
     * @return string
     */
    public function output($withHtmlDoctype=true, $return = false)
    {
        $this->builderTree($this->constructorData);

        $html = $this->_output;

        if($withHtmlDoctype){
            $html = '<!DOCTYPE html>
<html lang="'.$this->lang.'">
<head>
	<meta charset="'.$this->charset.'">
	<title>'.$this->title.'</title>
'.$this->styles().'
'.$this->stylesheet().'
'.$this->scripts().'
'.$this->javascript().'
</head>
<body>
'.$html.'
</body>
</html>';
        }

        if($return) return $html;
        else echo $html;
    }

/*
    public $_iterExt = 0;
    public function extractor($htmlData)
    {
        foreach ($htmlData as $keyDataNew => $valueData)
        {
            if(is_array($valueData)){
                $this->extractor($valueData);
            }else if(is_string($valueData)){
                if($idData = $this->contentData[$valueData]){
                    $id = $idData['id'];
                    $data = $idData['data'];
                    $attr = $this->extractorAttr($idData['attr']);
                    $this->_extractorData .= self::TB.self::TB.'<div id="'.$id.'" '.$attr.'>'.$data.'</div>'.self::NL;
                }
            }
        }
        return $this->_extractorData;
    }
*/

    /**
     * Формирует строку аргументов для подстановки в html элементы
     * @param  $data
     * @return string
     */
    private function extractorAttr($data)
	{
		$result = '';
		foreach ($data as $key => $value)
			$result .= $key.'="'.$value.'" ';
		
		return $result;
	}

    /**
     * Ошибка
     * @param string $text
     */
    private function error($text='Error')
	{
		try {
		    throw new Exception($text);
		} catch (Exception $e) {
		    echo 'Поймано исключение: ',  $e->getMessage(), "\n";
		}
		exit;
	}
}