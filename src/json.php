<?php
/*
   NUGH Loader - json.php
   
   2017:11:07 (yyyy.mm.dd)
 */

require_once("meta.php");


class NUGH_JsonLoader {
    public $stack = "";
    public $mode = array("start");

    public function _tokenize($content) {
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
		$stack = "";
	    }
	    $stack .= $content[$i];
	}
	array_push($tokens, $stack);
	#	print_r($tokens);
	return $tokens;
    }

    public function load($content) {
	#         $this->stack .= $content;

	$tokens = $this->_tokenize($content);
	$oldstack = $this->stack;
	#	echo "oldstack ".$oldstack."\n";
	$this->stack .= array_pop($tokens);
	#	echo "stack ".$this->stack."\n";

	if (false
	    || odd(substr_count($oldstack, '"'))
	    || strin($oldstack, ":")
	    || strin($oldstack, ",")
	    || strin($oldstack, "{")
	    || strin($oldstack, "}")
	    || strin($oldstack, "[")
	    || strin($oldstack, "]"))
	{
	    echo $oldstack."\n";
	    $this->stack = substr($this->stack, strlen($oldstack));
	    #	    $this->stack = "";
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
		)
		/* && (false
		   || strin($candidate, ",")
		   || strin($candidate, ":"))*/)
	    {
		echo $candidate." s\n";
		$this->stack = substr($this->stack, strlen($oldstack));
		unset($tokens[0]);
	    }
	}

	foreach ($tokens as $value) {
	    echo $value." v\n";
	}
	#	echo "stack ".$this->stack."\n";
    }
}

$content = file_get_contents('../examples/testdata.json');
$content = smoothing($content);

$jl = new NUGH_JsonLoader();
for ($i = 0; $i < strlen($content); $i += $inv) {
    $inv = rand(0, 10);
    $jl->load(substr($content, $i, $inv));
}
?>
