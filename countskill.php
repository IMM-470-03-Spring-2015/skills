<?php
header('Content-Type: application/json');
// original source: http://www.ourace.com/145-amazon-echo-alexa-with-php-hello-world

$EchoJArray = json_decode(file_get_contents('php://input'));
$RequestType = $EchoJArray->request->type;

$JsonOut 	= GetJsonMessageResponse($RequestType,$EchoJArray);
$size 		= strlen($JsonOut);

header("Content-length: $size");
echo $JsonOut;

//-----------------------------------------------------------------------------------------//
//					     Some functions
//-----------------------------------------------------------------------------------------//

//This function returns a json blob for output
function GetJsonMessageResponse($RequestMessageType,$EchoJArray){

	$RequestId = $EchoJArray->request->requestId;
	$ReturnValue = "";
	
	if( $RequestMessageType == "LaunchRequest" ) {
		$ReturnValue= '
		{
		  "version": "1.0",
		  "sessionAttributes": {
			"countActionList": {
			  "read": true,
			  "category": true,
			  "currentTask": "none",
			  "currentStep": 0
			}
		  },
		  "response": {
			"outputSpeech": {
			  "type": "PlainText",
			  "text": "Hello, welcome to the count example, thanks to Our Ace, dot com. I can count to three. Say next, or, next item, to count."
			},
			"card": {
			  "type": "Simple",
			  "title": "Our Ace count example",
			  "content": "I can count to three."
			},
			"reprompt": {
			  "outputSpeech": {
				"type": "PlainText",
				"text": "Can I help you with anything else?"
			  }
			},
			"shouldEndSession": false
		  }
		}';
	}
	
	if( $RequestMessageType == "SessionEndedRequest" ) {
		$ReturnValue = '{
		  "type": "SessionEndedRequest",
		  "requestId": "$RequestId",
		  "timestamp": "' . date("c") . '",
		  "reason": "USER_INITIATED "
		}';	
	}
	
	if( $RequestMessageType == "IntentRequest" ){
	
		$NextNumber = 0;
		$EndSession = "false";
		$SpeakPhrase = "";
		if( $EchoJArray->request->intent->name == "next" ) {
			$NextNumber = $EchoJArray->session->attributes->countActionList->currentStep + 1;
			$SpeakPhrase = "The next number is ". $NextNumber;
			
			if( $EchoJArray->session->attributes->countActionList->currentStep == 2){
				$EndSession = "true";
				$SpeakPhrase = "The last number is ". $NextNumber. ". Thank you for counting and good bye";
			}
		}
		
	
		$ReturnValue= '
		{
		  "version": "1.0",
		  "sessionAttributes": {
			"countActionList": {
			  "read": true,
			  "category": true,
			  "currentTask": "none",
			  "currentStep": '.$NextNumber.'
			}
		  },
		  "response": {
			"outputSpeech": {
			  "type": "PlainText",
			  "text": "' . $SpeakPhrase . '"
			},
			"card": {
			  "type": "Simple",
			  "title": "Hello Alexa count example",
			  "content": "' . $SpeakPhrase . '"
			},
			"reprompt": {
			  "outputSpeech": {
				"type": "PlainText",
				"text": "Say next item to continue."
			  }
			},
			"shouldEndSession": ' . $EndSession . '
		  }
		}';
	}
	return $ReturnValue;
}// end function GetJsonMessageResponse
