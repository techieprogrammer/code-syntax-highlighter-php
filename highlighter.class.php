<?php
include_once("keywords.php");
class Highlighter {
	private $fileName;
	private $fileExtension;
	private $showFileName;
	
	public function __construct() {
		$this->fileName = "";
		$this->fileExtension = "";
		$this->showFileName = true;
	}
	
	public function showfilename($value) {
		$this->showFileName = $value;
	}

	public function applycolor($fileLocation = "") {
		if($fileLocation == "") {
			return;
		}
		else
		{
			if(file_exists($fileLocation)) {
				$temp = explode("/",$fileLocation);
				$this->fileName = trim(end($temp));
				$temp = explode(".",$this->fileName);
				$this->fileExtension = trim(end($temp));
				$fileContent = trim(file_get_contents($fileLocation, true));
				$fileContent = htmlentities($fileContent,ENT_NOQUOTES);
				if($fileContent == "") {	
					return;	
				}
			}
			else
			{	
				return; 
			}
			
			$parenthesisFound = 0;
			$bracketFound = 0;
			$foundCharacter = "";

			$line = 1;
			$counter = 0;
			$contentSize = strlen($fileContent);

			$content = "<font class='lI'>".$line."</font> ";
			while($counter < $contentSize) {
				$character = $fileContent[$counter];
				$code = intval(ord($character));
				if(($code >= 97 && $code <= 122) || ($code >= 65 && $code <= 90)) { //Identify only alphabet(Capital/Small) characters
					$characterBuffer .= $character;	
				}
				else
				{
					if($characterBuffer != "") {	
						$content .= $this->checker($characterBuffer);
						$characterBuffer = "";
					}

					if($character == "/" && (isset($fileContent[$counter+1]) && ($fileContent[$counter+1] == "*" || $fileContent[$counter+1] == "/"))) { //Identify single comment or multiple comments
						$content .= "<font class='cC'>".$fileContent[$counter].$fileContent[$counter+1];
						if($fileContent[$counter+1] == "*") {
							$counter += 2;
							while($counter < $contentSize) {
								$character = $fileContent[$counter];
								$code = intval(ord($character));
								if($code != 10) { //Identify not '\n' character
									if($character == "*" && (isset($fileContent[$counter+1]) && ($fileContent[$counter+1] == "/"))) {
										$counter++;
										$content .= $character.$fileContent[$counter]."</font>";
										break;
									}
									else
									{	
										$content .= $character;	
									}
								}
								else
								{
									$line++;
									$content .= "</font>".$character."<font class='lI'>".$line."</font> <font class='cC'>";
								}
								$counter++;
							}
						}
						else
						{
							$counter += 2;
							while($counter < $contentSize) {
								$character = $fileContent[$counter];
								$code = intval(ord($character));
								if($code == 10) { //Identify '\n' character
									$content .= "</font>";
									$counter--;
									break;
								}
								$content .= $character;
								$counter++;
							}
						}
					}
					else if($character == "'" || $character == "\"") { //Identify sigle quote or double quote character
						$foundCharacter = $character;
						$content .= "<font class='qC'>".$foundCharacter;
						$counter++;
						while($counter < $contentSize) {
							$character = $fileContent[$counter];
							$code = intval(ord($character));
							if($foundCharacter == $character) {
								if($foundCharacter == "\"") {
									if($fileContent[$counter-1] != "\\") {
										$content .= $foundCharacter."</font>";
										break;
									}
									else if($fileContent[$counter-2] == "\\" && $fileContent[$counter-1] == "\\") {
										$content .= $foundCharacter."</font>";
										break;
									}
									else
									{
										$content .= $character;
									}
								}
								else
								{
									$content .= $foundCharacter."</font>";
									break;
								}
							}
							else if($code == 10) { //Identify '\n' character
								$line++;
								$content .= $character;
								$content .= "<font class='lI'>".$line."</font> ";
							}
							else
							{
								$content .= $character;
							}
							$counter++;
						}
					}
					else if($character == "(" || $character == ")") { //Identify parenthesis character
						if($parenthesisFound == 0) {
							$content .= "<font class='pC'>".$character."</font><font class='iPC'>";
						}
						if($character == "(") {
							$parenthesisFound++;
						}
						else if($character == ")") {
							$parenthesisFound--;
						}
						if($parenthesisFound == 0) {
							$content .= "</font><font class='pC'>".$character."</font>";
						}
					}
					else if($character == "[" || $character == "]") { //Identify bracket character
						if($bracketFound == 0) {
							$content .= "<font class='bC'>".$character."</font><font class='iBC'>";
						}
						if($character == "[") {
							$bracketFound++;
						}
						else if($character == "]") {
							$bracketFound--;
						}
						if($bracketFound == 0) {
							$content .= "</font><font class='bC'>".$character."</font>";
						}
					}
					else if($code == 10) { //Identify '\n' character
						$line++;
						$content .= $character;
						$content .= "<font class='lI'>".$line."</font> ";
					}
					else
					{
						$content .= $character;
					}
				}
				$counter++;
			}
			
			$output = "<div class='codediv'>";
			if($this->showFileName == true) {
				$output .= "<div class='fN'>".$this->fileName."</div>";
			}
			$output .= "<div class='code'><pre><code>".$content."</code></pre></div>";
			$output .= "</div>";
			return $output;
		}
	}

	private function checker($value) {
		global $languageKeywords;		
		$value = trim($value);
		if(isset($languageKeywords[$this->fileExtension])) { //Identify file type extension			
			if(in_array($value,$languageKeywords[$this->fileExtension])) { //Identify keywords				
				$value = "<font class='kC'>".$value."</font>";	
			}
		}	
		return $value;
	}
}
?>