<?php
function run_python($code){
	$filename=tempnam("/tmp","python");
	file_put_contents($filename,$code);
	ob_start();
	passthru("python ".$filename);
	$out=ob_get_contents();
	ob_end_clean();
	unlink($filename);
	return $out;
}
function run_php($code){
	$filename=tempnam("/tmp","php");
	file_put_contents($filename,$code);
	ob_start();
	passthru("php -f ".$filename);
	$out=ob_get_contents();
	ob_end_clean();
	return $out;
}
function phpython($filename){
	$codigo_misto=file_get_contents('php_python.php');
	preg_match_all(
		'/(?:<\?python((?:.*?\r?\n?)*)\?>)+/',
		$codigo_misto,
		$codigos_python
	);
	if(count($codigos_python[1])>=1){
		//executar códigos python e salvar o output
		foreach($codigos_python[1] as $key=>$code){
			$out=run_python($code);
			$codigos_python[3][$key]=$out;
		}
		//substituir o código python pelo output
		$codigo_php=null;
		foreach($codigos_python[0] as $key=>$code){
			$codigo_misto=str_replace($code,$codigos_python[3][$key],$codigo_misto);
		}
		$codigo_php=$codigo_misto;
		//executar o código php e retornar
		print run_php($codigo_php);
	}else{
		return null;
	}
}
phpython('php_python.php');
