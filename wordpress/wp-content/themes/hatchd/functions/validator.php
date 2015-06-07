<?php
/*
 * THE VALIDATOR CLASS
 */

class validator {

	/*
	 * @desc - validate an email address
	 * @param $email(string) - the email to validate
	 * @return (bool) - whether the email is valid 
	 */
	public function validateEmail($email){
	 	
		$isValid = true;
		
		$atIndex = strrpos($email,"@");
	
		if(is_bool($atIndex) && !$atIndex){
			
			$isValid = false;
			
		}else{
			
			$domain 	= substr($email, $atIndex+1);
			$local 		= substr($email, 0, $atIndex);
			$localLen 	= strlen($local);
			$domainLen 	= strlen($domain);
			
			if($localLen < 1 || $localLen > 64){
				
				//local part length exceeded
				$isValid = false;
				
			}else if($domainLen < 1 || $domainLen > 255){
				
				//domain part length exceeded
				$isValid = false;
				
			}else if($local[0] == '.' || $local[$localLen-1] == '.'){
				
				//local part starts or ends with '.'
				$isValid = false;
				
			}else if(preg_match('/\\.\\./', $local)){
				
				//local part has two consecutive dots
				$isValid = false;
				
			}else if(!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
				
				//character not valid in domain part
				$isValid = false;
				
			}else if(preg_match('/\\.\\./', $domain)){
				
				//domain part has two consecutive dots
				$isValid = false;
				
			}else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/',str_replace("\\\\","",$local))){
				
				//character not valid in local part unless
				//local part is quoted
				if(!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))){
					$isValid = false;
				}
				
			}
			
			if($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){
				
				//domain not found in DNS
				$isValid = false;
				
			}
			
		}

		return $isValid;
		
	}

	/*
	 * @desc - validate a url
	 * @param $url(string) - the url to validate
	 * @return (bool) - whether the url is valid
	 */
	public function validateUrl($url){
	 	
		$valid = false;
		
		if((filter_var($url,FILTER_VALIDATE_URL) !== FALSE)){
			$valid = true;
		}
		
		return $valid;
		
	}
	
	/*
	 * @desc - validate a number
	 * @param $number(int/string) - the number to validate
	 * @return (bool) - whether the number is valid
	 */
	public function validateNumber($number){
	 	
		$valid = false;
		
		$number = str_replace(" ","",$number);
		if(is_numeric($number)){
			$valid = true;
		}
		
		return $valid;
		
	}
	
	/*
	 * @desc - check whether a required field is empty
	 * @param $val(string) - the string to validate
	 * @return (bool) - whether the field was empty
	 */
	public function validateRequired($val){
	 	
		$valid = false;
		
		if($val != ""){
			$valid = true;
		}
		
		return $valid;
		
	}
	
	/*
	 * @desc - validate a password
	 * @param $password(string) - the password to check
	 * @return (bool) - whether the password is valid
	 */
	public function validatePassword($password){
		
		$valid = true;
		
		if(strlen($password) < 8){
			$valid = false;
		}
		if(strcspn($password,"0123456789") == strlen($password)){
			$valid = false;
		}
		
		return $valid;
		
	}

}

?>