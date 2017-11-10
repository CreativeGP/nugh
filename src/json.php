<?php
/*
   NUGH Loader - json.php
   
   2017:11:07 (yyyy.mm.dd)
 */

require_once("meta.php");


class NUGH_JsonLoader {
    public $stack = "";
    public $mode = array(array("start"));
    public $object = array();
    private $_iters = array();

    function __construct() {
	$this->_iters[] = &$this->object;
    }

    private function _tokenize($content) {
	$tokens = array();
	$stack = "";
	$faststack = "";
	for ($i = 0; $i < strlen($content); ++$i) {
	    $faststack .= $content[$i];
	    if (false
		#		|| odd(substr_count($stack, '"'))
		|| strin($stack, ":")
		|| strin($stack, ",")
		|| strin($stack, "{")
		|| strin($stack, "}")
		|| strin($stack, "[")
		|| strin($stack, "]"))
	    {
		array_push($tokens, $stack);
		$faststack = "";
		$stack = "";
	    }
	    $stack .= $content[$i];
	}
	array_push($tokens, $stack);
	#	print_r($tokens);
	return $tokens;
    }

    
    public function deal_with_token($token) {
	$splitted = str_split($token);
	if ((true
	  && strlen($token) > 1
	  && (false
	   || end($splitted) == ":"
	   || end($splitted) == ","
	   || end($splitted) == "{"
	   || end($splitted) == "}"
	   || end($splitted) == "["
	   || end($splitted) == "]")))
	{
	    $this->deal_with_token(substr($token, 0, strlen($token)-1));
	    $this->deal_with_token(substr($token, strlen($token)-1, 1));
	    return;
	}

	println($token);

	switch ($token) {
	    case "{":
		if (end($this->mode)[0] != "start") {
		    // NOTE: いま追加すべき配列の別名が$last_iterなので、
		    // それに新しい配列を入れる。そして新しく追加した配列の別名を
		    // また記憶しておく。
		    $last_iter = &$this->_iters[count($this->_iters)-1];
		    $last_iter[end($this->mode)[1]] = array();
		    print_r($this->object);
		    $this->_iters[] = &$last_iter[end($this->mode)[1]];

		    array_push($this->mode, array("map")); // モードに追加
		}
		print_r($this->mode);
		break;
	    case "}":
	    case "]":
		array_pop($this->mode);
		array_pop($this->mode);
		array_pop($this->_iters);
		break;
	    case ",":
		if (end($this->mode)[0] == "key")
		    array_pop($this->mode);
		break;
	    case "[":
		array_push($this->mode, array("list"));
		break;
	    default:
		if (preg_match('/^".*"/', $token)) {
		    $string = substr($token, 1, strlen($token)-2);
		    echo 'this->mode';
		    print_r($this->mode);
		    if (end($this->mode)[0] == "map" || end($this->mode)[0] == "start") {
			// key
			array_push($this->mode, array("key", $string));
		    }
		    else
		    {
			// value
			echo 'a';
			$last_iter = &$this->_iters[count($this->_iters)-1];
			$last_iter[end($this->mode)[1]] = $string;
		    }
		} else {
		    
		}
	}
    }

    public function load($content) {
	$tokens = $this->_tokenize($content);
	$oldstack = $this->stack;
	$this->stack .= array_pop($tokens);

	if (false
	    || odd(substr_count($oldstack, '"'))
	    || strin($oldstack, ":")
	    || strin($oldstack, ",")
	    || strin($oldstack, "{")
	    || strin($oldstack, "}")
	    || strin($oldstack, "[")
	    || strin($oldstack, "]"))
	{
	    $go = $this->_tokenize($oldstack);
	    foreach($go as $value): $this->deal_with_token($value); endforeach;
	    $this->stack = substr($this->stack, strlen($oldstack));
	}
	else {
	    if (count($tokens) == 0) return;
	    $candidate = $oldstack.$tokens[0];

	    if (true
		&& (false
		 || odd(substr_count($candidate, '"'))
		 || strin($candidate, ":")
		 || strin($candidate, ",")
		 || strin($candidate, "{")
		 || strin($candidate, "}")
		 || strin($candidate, "[")
		 || strin($candidate, "]")
		))
	    {
		$go = $this->_tokenize($candidate);
		foreach($go as $value): $this->deal_with_token($value); endforeach;
		$this->stack = substr($this->stack, strlen($oldstack));
		unset($tokens[0]);
	    }
	}

	foreach ($tokens as $value) {
	    $go = $this->_tokenize($value);
	    foreach($go as $v): $this->deal_with_token($v); endforeach;
	}
    }
}

$content = file_get_contents('../examples/testdata.json');

$content = <<<EOM
{
"Tom": {
"address": "Hokkaido",
"age": 32,
"friends": { "Amy":32, "Tomy":35 },
"friends2": { "friends3": { "Amy":32, "Tomy":35 },
"Amy":32, "Tomy":35 }
}
}
EOM;

$content = smoothing($content);

$jl = new NUGH_JsonLoader();
for ($i = 0; $i < strlen($content); $i += $inv) {
    $inv = rand(0, 10);
    $jl->load(substr($content, $i, $inv));
}
print_r($jl->object);
?>
