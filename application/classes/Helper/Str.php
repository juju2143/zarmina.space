<?php defined('SYSPATH') or die('No direct script access.');
 
abstract class Helper_Str{
	// Removing any special characters
	public static function strip_accent($str){
		$str = htmlentities($str, ENT_NOQUOTES, 'utf-8');
		$str = preg_replace('#\&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring)\;#', '\1', $str);
		$str = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $str);
		$str = preg_replace('#\&[^;]+\;#', '', $str);
		return $str;
	}
	
	//Lowering the case of an utf8 string
	public static function strtolower($string) {
		return utf8_encode(strtolower(utf8_decode($string)));
	}
	
	//Limiting the length of a string
	public static function wordlimit($str, $length){
		$str = Helper_Str::striptags($str);
		$str = explode(" ", $str);
		$str = implode(" " , array_slice($str, 0, $length));
		return $str;
	}
	
	public static function make_accents($string){
		//$string = "<p>Angoulême</p>";
		$trans = get_html_translation_table(HTML_ENTITIES);
		//$encoded = "&lt;p&gt;Angoul&ecirc;me&lt;/p&gt;";
		$encoded = strtr($string, $trans);
		//Next two lines put back the < & > tags
		$noHTML = str_replace("&lt;", "<", $encoded);
		$encoded = str_replace("&gt;", ">", $noHTML);
		$noHTML = str_replace("&quot;", '"', $encoded);
		$encoded = str_replace("&gt;", ">", $noHTML);
		return $encoded;
	}
	
	//Removing HTML tags
    public static function strip_word_html($str, $allowed_tag = "") {
        mb_regex_encoding('UTF-8');
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $str = preg_replace($search, $replace, $str);
        $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
        if(mb_stripos($str, '/*') !== FALSE){
            $str = mb_eregi_replace('#/\*.*?\*/#s', '', $str, 'm');
        }
        $str = preg_replace(array('/<([0-9]+)/'), array('< $1'), $str);
        $str = Helper_Str::striptags($str, $allowed_tag);
        $str = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $str);
        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        $str = preg_replace($search, $replace, $str);
        $num_match = preg_match_all("/\<!--/u", $str, $match);
        if($num_match){
              $str = preg_replace('/\<!--(.)*--\>/isu', '', $str);
        }
		
        return $str;
    }
	
	/**
	*
	* Cuts a string to the length of $length and replaces the last characters
	* with the ending if the text is longer than length.
	*
	* @param string  $text String to truncate.
	* @param integer $length Length of returned string, including ellipsis.
	* @param string  $ending Ending to be appended to the trimmed string.
	* @param boolean $exact If false, $text will not be cut mid-word
	* @param boolean $considerHtml If true, HTML tag would be handled correctly
	* @return string Trimmed string.
	*/
	public static function sub_str_html($text, $start, $length = 100, $ending = ' […]', $exact = false, $considerHtml = true) {
        if ($considerHtml) {
            // if the plain text is shorter than the maximum length, return the whole text
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
           
            // splits all html-tag to scanable lines
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
   
            $total_length = strlen($ending);
            $open_tag = array();
            $truncate = '';
           
            foreach ($lines as $line_matching) {
                // if there is any html-tag in this line, handle it and add it (uncounted) to the output
                if (!empty($line_matching[1])) {
                    // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matching[1])) {
                        // do nothing
                    // if tag is a closing tag (f.e. </b>)
                    } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matching[1], $tag_matching)) {
                        // delete tag from $open_tag list
                        $pos = array_search($tag_matching[1], $open_tag);
                        if ($pos !== false) {
                            unset($open_tag[$pos]);
                        }
                    // if tag is an opening tag (f.e. <b>)
                    } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matching[1], $tag_matching)) {
                        // add tag to the beginning of $open_tag list
                        array_unshift($open_tag, strtolower($tag_matching[1]));
                    }
                    // add html-tag to $truncate'd text
                    $truncate .= $line_matching[1];
                }
               
                // calculate the length of the plain text part of the line; handle entities as one character
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matching[2]));
                if ($total_length+$content_length> $length) {
                    // the number of characters which are left
                    $left = $length - $total_length;
                    $entity_length = 0;
                    // search for html entities
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matching[2], $entities, PREG_OFFSET_CAPTURE)) {
                        // calculate the real length of all entities in the legal range
                        foreach ($entities[0] as $entity) {
                            if ($entity[1]+1-$entity_length <= $left) {
                                $left--;
                                $entity_length += strlen($entity[0]);
                            } else {
                                // no more characters left
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_matching[2], 0, $left+$entity_length);
                    // maximum lenght is reached, so get off the loop
                    break;
                } else {
                    $truncate .= $line_matching[2];
                    $total_length += $content_length;
                }
               
                // if the maximum length is reached, get off the loop
                if($total_length>= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = substr($text, 0, $length - strlen($ending));
            }
        }
       
        // if the words shouldn't be cut in the middle...
        if (!$exact) {
            // ...search the last occurance of a space...
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                // ...and cut the text in this position
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
       
        // add the defined ending to the text
        $truncate .= $ending;
       
        if($considerHtml) {
            // close all unclosed html-tag
            foreach ($open_tag as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }
		
		//We make sure no tags were partially truncated.
		$truncate = str_replace('<br /' . $ending, '', $truncate);
		$truncate = str_replace('<br ' . $ending, '', $truncate);
		$truncate = str_replace('<br' . $ending, '', $truncate);
		$truncate = str_replace('<b' . $ending, '', $truncate);
		$truncate = str_replace('<hr /' . $ending, '', $truncate);
		$truncate = str_replace('<hr ' . $ending, '', $truncate);
		$truncate = str_replace('<h ' . $ending, '', $truncate);
		$truncate = str_replace('<' . $ending, '', $truncate);
       
        return $truncate;
       
    }
	
	//Truncate to caracter limit
	public static function truncate($str, $limit, $break = ".", $pad = "[…]"){ 
		if(strlen($str) <= $limit){
			return $str;
		} 
		
		if(false !== ($breakpoint = strpos($str, $break, $limit))){ 
			if($breakpoint < strlen($string) - 1){ 
				$str = substr($str, 0, $breakpoint) . $pad; 
			} 
		} 
		
		return $str; 
	}
	
	//Truncate to word limit
	public static function truncate_word($str, $numword, $pad = ""){ 
		if (strlen($str) < $numword){
			return $str;
		}
		
		$words = explode(' ', preg_replace("/\s+/", ' ', preg_replace("/(\r\n|\r|\n)/", " ", $str)));
		
		if (count($words) <= $numword){
			return $str;
		}
				
		$str = '';
		for ($i = 0; $i < $numword; $i++){
			$str .= $words[$i].' ';
		}
	
		return trim($str) . $pad;
	
	} 
	
	public static function html_entity_decode($str){
		return html_entity_decode($str, ENT_COMPAT, 'UTF-8');
	}
	
	public static function br2nl($str){
		$br = array('<br>','<br/>','<br />','<BR>','<BR/>','<BR />');
		return str_replace($br, "\n",$str); 
	}
	
	public static function remove_accent($str){
		$a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
  		$b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
		
		return str_replace($a, $b, $str);
	}
	
	public static function limit_p($str, $limit = 4, $ending = ' […]'){
		//Splitting the text by paragraphs
		$a_str = explode('</p>', $str);
		
		//Looping through the paragraph within the allowed limit
		$a_return = array();
		foreach($a_str as $key => $value){
			if($key < $limit){
				$a_return[] = $value;
			} else {
				// if we reach the limit, it's of no use to continue looping
				break 1; 
			}

		}
		
		//Appending the ending symbol if we had more paragraph than the limit.
		if(count($a_str) > $limit){
			$a_return[] = '<p>' . $ending;	
		}
		
		$return = implode('</p>', $a_return);
		
		return $return;
	}
	
	public static function ultimate_trim($str){
		$str = trim($str);
		$str = str_replace("\t", " ", $str);
		$str = mb_ereg_replace("[ ]+", " ", $str);
	
		return $str;
	}
	
	public static function convert_filesize($value){
		if(is_numeric($value)){
			return $value;
		}else{
			$length = strlen( $value );
			
			$qty = substr( $value, 0, $length - 1 );
			
			$unit = strtolower( substr( $value, $length - 1 ) );
			
			switch ( $unit ) {
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}
			return $qty;
		}
	}
	
	public static function encode_url($title_uri = ''){
		if(isset($title_uri) && !empty($title_uri)){
			$title_uri = Helper_Str::striptags($title_uri); 
			$title_uri = Helper_Str::strip_accent($title_uri);
			$title_uri = htmlspecialchars($title_uri);
			$title_uri = URL::title($title_uri);
			
			return $title_uri;
		}else{
			return '';
		}
	}
	
	public static function title($str){
		$str = Helper_Sanitize::htmLawed($str);
		$str = Helper_Str::striptags($str);
		
		return $str;
	}
	
	public static function content($str){
		$str = Helper_Sanitize::htmLawed($str);
		
		return $str;
	}
	
	public static function striptags($str){
		$spaceString = str_replace( '<', ' <', $str);
		$doubleSpace = strip_tags($spaceString);
		$singleSpace = str_replace( '  ', ' ', $doubleSpace);

		return $singleSpace;
	}
	
	public static function mb_ucfirst($string, $encoding = 'UTF-8'){
		$strlen = mb_strlen($string, $encoding);
		$firstChar = mb_substr($string, 0, 1, $encoding);
		$then = mb_substr($string, 1, $strlen - 1, $encoding);
		return mb_strtoupper($firstChar, $encoding) . $then;
	}
	
	public static function uuid(){
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); 
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); 
      
	    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}
}
?>