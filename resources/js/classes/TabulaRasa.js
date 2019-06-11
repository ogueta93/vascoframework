/**
* TabulaRasa JS Class
**/

class TabulaRasa 
{
	constructor() 
	{

	}

	encode( target )
	{
		var encodedString = '';

		target = typeof target === "object" ? JSON.stringify( target ) : String( target );
		var target64Base = window.btoa( target );
		
		var length = target64Base.length;
		while( length-- ) 
		{
			var chart = target64Base.charAt( length );
			var codeChart = chart.charCodeAt();
			var transformChart = String.fromCharCode( codeChart + ( length + 1 ) );
			encodedString += transformChart;
		}

		return encodedString;
	}

	decode( target )
	{
		var decodeString = '';
		var stringDecoded = '';

		var length = target.length;
		while( length-- ) 
		{
			var chart = target.charAt( length );
			var codeChart = chart.charCodeAt();
			var transformChart = String.fromCharCode( codeChart - ( target.length - length ) );
			stringDecoded += transformChart;
		}
		stringDecoded = window.atob( stringDecoded );

		try 
		{
			decodeString = JSON.parse( stringDecoded );
		}
		catch( e )
		{
			decodeString = stringDecoded;
		}

		return decodeString;
	}
}

//Calling the class
var tabulaRasa = new TabulaRasa( 'hero' );