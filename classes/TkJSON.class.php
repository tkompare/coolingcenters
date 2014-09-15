<?php

/**
 * Created by PhpStorm.
 * User: tom
 * Date: 5/15/14
 * Time: 8:39 AM
 */
abstract class TkJSON implements JsonSerializable
{

	private function is_valid_callback($subject)
	{
		$identifier_syntax = '/^[$_\p{L}][$_\p{L}\p{Mn}\p{Mc}\p{Nd}\p{Pc}\x{200C}\x{200D}]*+$/u';

		$reserved_words = array(
				'break',
				'do',
				'instanceof',
				'typeof',
				'case',
				'else',
				'new',
				'var',
				'catch',
				'finally',
				'return',
				'void',
				'continue',
				'for',
				'switch',
				'while',
				'debugger',
				'function',
				'this',
				'with',
				'default',
				'if',
				'throw',
				'delete',
				'in',
				'try',
				'class',
				'enum',
				'extends',
				'super',
				'const',
				'export',
				'import',
				'implements',
				'let',
				'private',
				'public',
				'yield',
				'interface',
				'package',
				'protected',
				'static',
				'null',
				'true',
				'false');

		return preg_match($identifier_syntax, $subject) && !in_array(mb_strtolower($subject, 'UTF-8'), $reserved_words);
	}

	public function returnJSON()
	{
		header('content-type: application/json; charset=utf-8');
		exit(json_encode($this, JSON_HEX_TAG));
	}

	public function returnJSONP($callback)
	{
		header('content-type: application/json; charset=utf-8');

		// Let's make sure the user knows what she's doing or isn't being a jerk.
		if($this->is_valid_callback($callback))
		{
			exit('{'.$callback.'}('.json_encode($this, JSON_HEX_TAG).')');
		}
		else
		{
			header('status: 400 Bad Request', TRUE, 400);
			exit;
		}
	}

} 