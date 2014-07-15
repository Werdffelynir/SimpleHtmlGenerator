<?php

/**
 * Simple HTML Generator
 * SimpleHtmlGenerator
 */

class HtmlGen
{
    public $title = 'Title Page';
    public $constructorData = array();
    public $contentData = array();
    public $stylesheet = null;
    public $javascript = null;


	const NL = "\n";
	const TB = "\t";

    private $_timerStart = null;
    private $_extractorData = '';
    private $_stylesData = array();
    private $_scriptsData = array();


	public function __construct(array $data = null)
	{
        $this->_timerStart = microtime(true);
		if($data != null)
			$this->constructorData = $data;

	}

	public function constructor(array $data)
	{
		$this->constructorData = $data;	
	}

    public function timer($float=4)
    {
        return round(microtime(true) - $this->_timerStart, $float);
    }

	public function add()
	{
		$attributes = func_get_args();
		$attributesNum = func_num_args();
		if($attributesNum==1){
			if(is_array($attributes[0])){
				$id = (isset($attributes[0]['id']))?$attributes[0]['id']:$this->error('Необходимо указать идентификатор в массив "id"');
				$this->contentData[$id] = $attributes[0];
			}else 
				$this->error();
		}else if($attributesNum==3){
			$this->contentData[$attributes[0]] = array('id'=>$attributes[0],'data'=>$attributes[1],'attr'=>$attributes[2]);
		}else{
			$this->error('Не верное количество аргументов!');
		}
	}

    public function append($data, $append)
    {
        if(isset($this->contentData[$data])) {
            $this->contentData[$data]['data'] .= self::NL.$append;
        }
    }

    public function stylesheet($data=null, $append=false)
    {
        if($data != null){
            if($append) $this->stylesheet .= self::NL.$data;
            else $this->stylesheet = $data;
        }else{
            return '<style type="text/css">'.$this->stylesheet.'</style>';
        }
    }


    public function javascript($data=null, $append=false)
    {
        if($data != null){
            if($append) $this->javascript .= self::NL.$data;
            else $this->javascript = $data;
        }else{
            return '<script type="text/javascript">'.$this->javascript.'</script>';
        }
    }


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
                    else die('ERROR');
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


    public function wrap($data=true, array $attr = null)
    {
        if ($_attr = $attrData){
            $attr = $this->extractorAttr($_attr);
        }
        return '<div '.$attr.'>'.$data.'</div>'.self::NL;
    }


    public function output($withHtmlDoctype=true, $return = false)
	{
		$mainBlock = key($this->constructorData);
		$htmlData = $this->constructorData[$mainBlock];

        $mainAttr = '';
        if ($_mainAttr = $this->contentData[$mainBlock]['attr']){
            $mainAttr = $this->extractorAttr($_mainAttr);
        }

		$html = '<div id="'.$mainBlock.'" '.$mainAttr.'>'.self::NL;

		foreach ($htmlData as $keyData => $valueData) 
		{
			if(is_array($valueData)){

                $attrInner = '';
                if ($_attrInner = $this->contentData[$keyData]['attr']){
                    $attrInner = $this->extractorAttr($_attrInner);
                }
				$html .= self::TB.'<div id="'.$keyData.'" '.$attrInner.'>'.self::NL;
                $html .= $this->extractor($valueData);
				$html .= self::TB.'</div>'.self::NL;

                $this->_extractorData = null;

			}else if(is_string($valueData)){
				if($idData = $this->contentData[$valueData]){
					$id = $idData['id'];
					$data = $idData['data'];
					$attr = $this->extractorAttr($idData['attr']);
                    $html .= self::TB.'<div id="'.$id.'" '.$attr.'>'.$data.'</div>'.self::NL;
				}
			}
		}

		$html .= '</div>'.self::NL;

		if($withHtmlDoctype){
            $html = '<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>'.$this->title.'</title>
'.$this->styles().'
'.$this->stylesheet().'
'.$this->scripts().'
</head>
<body>
'.$html.'
</body>
</html>';
		}

        if($return) return $html;
        else echo $html;
	}

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

	public function extractorAttr($data)
	{
		$result = '';
		foreach ($data as $key => $value)
			$result .= $key.'="'.$value.'" ';
		
		return $result;
	}

	public function error($text='Error')
	{
		try {
		    throw new Exception($text);
		} catch (Exception $e) {
		    echo 'Поймано исключение: ',  $e->getMessage(), "\n";
		}
		exit;
	}
}

?>
