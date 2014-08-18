<?php 
/**
* Class which will handle your errors and exceptions
*/
class SP_Errorhandler
{	
	public static function handleErrors($errno, $errmsg, $filename, $linenum, $vars)
	{
		$reporting = error_reporting();
		if(empty($reporting)) return false;

		$errortype = array (
			E_ERROR				=> "Error",
			E_WARNING			=> "Warning",
			E_PARSE				=> "Parsing Error",
			E_NOTICE			=> "Notice",
			E_CORE_ERROR		=> "Core Error",
			E_CORE_WARNING		=> "Core Warning",
			E_COMPILE_ERROR		=> "Compile Error",
			E_COMPILE_WARNING	=> "Compile Warning",
			E_USER_ERROR		=> "User Error",
			E_USER_WARNING		=> "User Warning",
			E_USER_NOTICE		=> "User Notice",
			E_STRICT			=> "Runtime Notice",
			E_RECOVERABLE_ERROR	=> "Recoverable error",
			@E_DEPRECATED		=> 'Deprecated',
			@E_USER_DEPRECATED	=> 'Deprecated',
			1					=> 'Fatal',
			64					=> 'Fatal',
		);

		if (SP_ENVIRONMENT == 'development') {
			self::addCss();
			echo '<div class="alert-box '.strtolower($errortype[$errno]).'"><span>'.$errortype[$errno].'</span><br>'.$errmsg.'<br>FILE: '.$filename.'<br>LINE: '.$linenum.'</div>';

		}

		$log_path = SP_Config::get('log_file');
		if (empty($log_path)) {
			return false;
		}

		$error = $errortype[$errno].": \t ".date('Y-m-d H:i:s').';'.PHP_EOL;
		$error.= "Message: \t ".$errmsg  .';'.PHP_EOL;
		$error.= "File: \t\t " .$filename.';'.PHP_EOL;
		$error.= "Line: \t\t " .$linenum .";".PHP_EOL; 
		$error.= self::extendInfo();	

		$log_path = SP_LOGS.$log_path;
		file_put_contents($log_path, $error, FILE_APPEND);

		return true;
	}

	public static function handleExceptions($e)
	{
		if (SP_ENVIRONMENT == 'development') {
			self::addCss();
			echo '<div class="alert-box exception"><span>Exception</span><br>Message: '.$e->getMessage().'<br>File: '.$e->getFile().'<br>Line: '.$e->getLine().'</div>';
		}
		
		$log_path = SP_Config::get('log_file');
		if (empty($log_path)) {
			return false;
		}

		$log =	"Exception: \t ".date('Y-m-d H:i:s').';'.PHP_EOL;
		$log.=	"Message: \t ".$e->getMessage().';'.PHP_EOL;
		$log.=	"File: \t\t ".$e->getFile().';'.PHP_EOL;
		$log.=	"Line: \t\t ".$e->getLine().";".PHP_EOL; 
		$log.= self::extendInfo();	

		$log_path = SP_LOGS.$log_path;
		file_put_contents($log_path, $log, FILE_APPEND);

		return true;
	}

	public static function logError($errmsg, $file, $line)
	{
		$log_path = SP_Config::get('log_file');
		if (empty($log_path)) {
			return false;
		}

		$error = "USER ERROR: \t ".date('Y-m-d H:i:s').';'.PHP_EOL;
		$error.= "Message: \t ".$errmsg  .';'.PHP_EOL;
		$error.= "File: \t\t " .$file.';'.PHP_EOL;
		$error.= "Line: \t\t " .$line .";".PHP_EOL; 
		$error.= self::extendInfo();	

		$log_path = SP_LOGS.$log_path;
		file_put_contents($log_path, $error, FILE_APPEND);

		return true;
	}

	private static function extendInfo()
	{
		$error = '';

		if (!empty($_SERVER['HTTP_HOST'])) {
			$log_url = 'http://'.(!empty($_SERVER['HTTP_FRONTEND']) ? $_SERVER['HTTP_FRONTEND']:$_SERVER['HTTP_HOST']).$_SERVER['REQUEST_URI'];
			$error.= "URL: \t\t {$log_url}\n";
		}

		if(!empty($_SERVER['REMOTE_ADDR'])){
			$error.= "IP: \t\t ".$_SERVER['REMOTE_ADDR'];
			if(!empty($_SERVER['HTTP_USER_AGENT'])) $error.=	"\t Agent: ".substr($_SERVER['HTTP_USER_AGENT'],0,256);
			$error.= PHP_EOL;
		}

		if(!empty($_SERVER['HTTP_REFERER'])) $error.=	"Referer: \t ".$_SERVER['HTTP_REFERER'].PHP_EOL;
		$error.= "\n\t\t\t ~~~~~~~~~~~~ \t\t\t\t\t ~~~~~~~~~~~~\n\n";

		return $error;
	}

	private static function addCss()
	{
		echo '<style>
				.alert-box {
				    color:#555;
				    font-family:Tahoma, Geneva, Arial, sans-serif;
				    font-size:14px;
				    padding:10px 36px;
				    margin:10px;
				    color:#fff;
				    -moz-border-radius: 5px;
				    -webkit-border-radius: 5px;
				    -khtml-border-radius: 5px;
				    border-radius: 5px;
				}
				.alert-box span {
				    font-weight:bold;
				    text-transform:uppercase;
				    letter-spacing: 1px;
				}
				.error {
				    background:#e47c68 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAC3UlEQVR4Xm2RX0iTXQDGn1c3N2uzbUwvwrpww9jcRW5qlvLdqNCHeKUGaxd+qFdeBUEiSYYYKJ9/SnCwm9puhjGD11ARTOFTIckMW15JGJVDy/xE9y7d9r7ndHZgMsQfPBfnvM/z8PIcnOW/7u6/t/4d9EcGh/27T0f9u89G/ZGhYf+XoSH/cnd3I84gIIMJt7vBeckQUtbWNFAIQCkoE0AAQYCmvFz+GI22zBqNQa/XyzOqdNhbU9Ngk+XQ8aSoobKM80js7KiKq6oCe5ubYATByAKj02ptKCUkRBYXNSeKgi9GIyjAleabyYRj9k1eWlJdTyQCj4uL7yJFk91+e9JecrJeUEDf5efT5UeP6PHREZ2rr6dhdk5pvq6OSvv7dHVkhK6y8wbTnM2WbHU47uC+zeZfY6YPZjN939hICSGUwUvesJL52loeTvOpre20uMdiEbPI4RFYBAoYi4vYCwTASpBz8SJuBYO4MT6OXIOB3/0/MQFlairl58qOxaCiiszDAsBX//rgAWRm1jc38w2gViMmSTiensb2vXsAIenn4yUqWZZBBAECtwvcsPr8OUoqK6HR6bhZjscRZneXFQUC86ZJUEB1QgHCw+AlEasVRQMDSL1GIhrl709Y8GpfHz53duLqxsZpSZwSZEmaHBCA68higbmnByeUIsrC6pUVqObnIUkx/E4mkf/wIQ4dDvDNUp7sbGRtm0yv17XaJAGQd3AAQyIBKSrBEA7jR1cXfvb24tLKW8TYYHmsxMg8CoDNCxfIlsEg8u2qTCZ3q14fKJIktdpshpENuOfzpQfjv2xub4c0M4N4JILvOh0JxGIdcbvdxws8Hg9+LSy4Pbm5gSuHh2qcD1/+h15PXsbjHerqat/LUAgCMvirsND9j1YbuJYqoZQPmMk2C79IJjtcHo/vSX8/zqXO6WoauFEp+ktLxSmXS5wtKxNfOZ2ir6JC9Ny82eIdG0MmfwCjX3/U2vu6zQAAAABJRU5ErkJggg==) no-repeat 10px 50%;
				}
				.error:hover {  background-color: #d46c57; }
				.success {
				    background:#4cbe83 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAmJJREFUeNqkk0toE0Ech3+T3aRJX7RpPNgSgzQljYXiC1FbUcFrL9WTqAe96NGce+hF8KA5eVHsSaQni1CR4kHEFwoVxNrW0iJtA9lqk1TJbnZ2d3bGnbWPDT124Fvm9f32v+wMEUJgL02VD/IkASjEQw5IJwiGvd6AR3JzX8HjAwQmIEQRrjdyBcTV0v+AQBuKqpFcpiuTTiWS8eaG5qisz7D0I8vrK4MLxcWLlmPlvanJugq25NaGltFzfWezKpQYsxl0W99aa0x3dDcm25Mdb+fejVZNf94PCW1u6GwIRXJnegeyds2K6boOSmkdz3oeg5lO7GT6RDZCwjnp7AQwMdyzvztNdRozDAOmadZxt3vE3zZ1eNwLYbFUPJmWTjDgdKIpEa9Wq7Asy0dWsfZ7DTejV9BWbkKhUMC1l7cwOzcLTnlcOsGAAwqUqOu6+Hx+ClpZw8qvFaRIF061H4eqqhhbfooXpVdwQg6oTaPSCQaAuQw3Dl7GzMwMpg6N42iiHw/77/ny69J7PCiOATH4MJX5zk6AI1ZLxjod+XYHiqIgHA7jUe99hNUwFms/cXt5BLyZe/8CPjaxqHSCFXxcW9cqSlzB4I8h/61bXFq8DrRhW5bQaq0inWDAxJ/V8lIIxCRdBMe+X/DlvulBYF+9zLlrWpq5JJ2dAC6KrsHy5U/avGDcJCmCvq+enML2d0u4w0x9ujLPa25eOvUnkYtJpln4+1zLRbJN6UimMa6oalQuuRuM2gu1ij1vLHFH5NGqeKeQ7DrKfggvsS/0zcawx+7LpJAJtCjFoEL2ep3/CTAAj+gy+4Yc2yMAAAAASUVORK5CYII=) no-repeat 10px 50%;
				}
				.success:hover { background-color: #36ad6f; }
				.warning {
				    background:#feb742 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABqklEQVR4XqWTvWsUURTFf+/tx7DmA5sUmyB+EGQDCkFRxCFosYWCFgELm2ApCBYW/gOCFpYSrUMsBIv4BwTSCSqaWgsTEDRV2EVBZWffvXIYwhZOEdgLhzmcc+7h3WKCuzPOhI+P80rDzE7WwmAHIHnzVIxxl4qJVaKbkYrBxvyVZQRxaYcq0EmehvePzp5YnD67hCAuzd0PUWB2JNQazzo377D7+auAuDR51QWjZWxYvD2e34DsJw+fbwviSJOnTHWBO5aGt6fa84szF67CzguCIYgjTZ4yuP9fYGqO2avO8j348hSKff4OkiAuDXnKKDsqGD1989jSLWJvA/58g+YUv34Xgrg0eSij7MEpsXx66k62O932wjT030NjAuotXj/YE8SlyUMZZbWj3ejmEFubp69fg711yCYha0GWcXftjCAuTZ4yKKsd7dbNfHXuUk6jeAPNCSBCAJpGb78PiGel7gCmLHMXc76/21oNn57kfm5lFg0W0KBPDag7GoYBEuCUE0uy/fIH4cOjy27J0SlI56DEiSVFFi4dEUUIMRBrQZTzjDFj/87/ACmm3+QFX8sKAAAAAElFTkSuQmCC) no-repeat 10px 50%;
				}
				.warning:hover { background-color: #eda93b; }
				.notice {
				    background:#5A95E9 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAMAAAAoLQ9TAAAAwFBMVEX///8AVq0CYcADbNEDaMoDa88Das0EcdkFfe0CZMPv9fu/1esFe+kBXbkEdd8Uf+QEb9VRkdAGf/AFeecRacAEc9wBW7QBX7zs6dwFeudRjswGgvUFd+MCZscDbtTl4tIBWbFEo/rh3s7A2/QAVasCZcYEcNcBYL3n5NUFfOsWhe5UqPVCkNvj4NDd2swyhNMGgPIBXLbq59nB4Pw1m/lToOgCY8RBi9Lv9/8FduHm49QxfMfo5dcQYrNTnuVBhMhJU/nRAAAAAXRSTlMAQObYZgAAAMpJREFUeF4lzdVyRTEIQFGI57i7XXet+///VZN2vy0YBrA9HPZeF34v4L/XWXtVdRdIenT+/NheE2W8puxiJ7O2TZSq7jLiTMfm3u53YVXhIHriu3BIEuXVdYBI2Yr4Dex3npd22893KnpdFl9Qp2n3FgTbEZkm/oSQGuVSjiOagwJ9CPNcrodhPCEpb9MygycZDZTz0xyNsYhhkXMhGJuf0XhJXIBj1K/02YTGDQA4F042G7SRDwfs5EVo828qn3+sbW6cEZI1rsUvrDkTPAFMyQwAAAAASUVORK5CYII=) no-repeat 10px 50%;
				}
				.notice:hover { background-color: #3976CC; }
				.exception{
				    background:#828379 url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAABqklEQVR4XqWTvWsUURTFf+/tx7DmA5sUmyB+EGQDCkFRxCFosYWCFgELm2ApCBYW/gOCFpYSrUMsBIv4BwTSCSqaWgsTEDRV2EVBZWffvXIYwhZOEdgLhzmcc+7h3WKCuzPOhI+P80rDzE7WwmAHIHnzVIxxl4qJVaKbkYrBxvyVZQRxaYcq0EmehvePzp5YnD67hCAuzd0PUWB2JNQazzo377D7+auAuDR51QWjZWxYvD2e34DsJw+fbwviSJOnTHWBO5aGt6fa84szF67CzguCIYgjTZ4yuP9fYGqO2avO8j348hSKff4OkiAuDXnKKDsqGD1989jSLWJvA/58g+YUv34Xgrg0eSij7MEpsXx66k62O932wjT030NjAuotXj/YE8SlyUMZZbWj3ejmEFubp69fg711yCYha0GWcXftjCAuTZ4yKKsd7dbNfHXuUk6jeAPNCSBCAJpGb78PiGel7gCmLHMXc76/21oNn57kfm5lFg0W0KBPDag7GoYBEuCUE0uy/fIH4cOjy27J0SlI56DEiSVFFi4dEUUIMRBrQZTzjDFj/87/ACmm3+QFX8sKAAAAAElFTkSuQmCC) no-repeat 10px 50%;
				}
				.exception:hover{background-color:#A7AA94; }
			</style>';
	}

}