<?php
function __autoload($className)
{
	$fileName = preg_replace('#_#', '/', $className);
	require_once 'classes/' . $fileName . '.php';
}
