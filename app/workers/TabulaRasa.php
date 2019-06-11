<?php
namespace App\Workers;

use Core\Worker;

/**
* Controller Class
**/
class TabulaRasa extends Worker
{
	/* Propierties */

	/* Objects Propierties */

	/**
	* Default Constructor
	*
	**/

	/**
	* Returns a string encoded by TabulaRasa
	*
	* @param string $target
	* @return string $encodedString
	**/
	public function encode( $target )
	{
		$encodedString = '';

		$target64Base = base64_encode( $target );

		$length = strlen( $target64Base );
		while( $length-- ) 
		{
			$chart = substr( $target64Base , $length , 1 );
			$codeChart = ord( $chart );
			$transformChart = chr( $codeChart + ( $length + 1 ) );
			$encodedString .= $transformChart;
		}

		return $encodedString;
	}

	/**
	* Returns a string decoded by TabulaRasa
	*
	* @param string $target
	* @return string $decodedString
	**/
	public function decode( $target )
	{
		$decodedString = '';

		$length = strlen( $target );
		while( $length-- ) 
		{
			$chart = substr( $target , $length , 1 );
			$codeChart = ord( $chart );
			$transformChart = chr( $codeChart - ( strlen( $target ) - $length ) );
			$decodedString .= $transformChart;
		}
		$decodedString = base64_decode( $decodedString );

		return $decodedString;
	}
}
