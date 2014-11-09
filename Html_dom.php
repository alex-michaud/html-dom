<?php
/*******************************************************************************
Version: 0.6.2
Date : 2014-11-03
Website: http://www.ecliptik.net/html-dom.html
Website doc: http://www.ecliptik.net/html-dom.html
Author: alex michaud <alex.michaud@gmail.com>
Licensed under The MIT License
Redistribution of file must retain the above copyright notice.
*******************************************************************************/

/**
 * Load a html_dom object from a html string
 * @param string $str
 * @return html_dom object
 */
function str_get_html($str, $encoding = 'UTF-8')
{
	$html_dom = new Html_dom();
	$html_dom->loadHTML($str, $encoding);
	return $html_dom;
}

/**
 * Load a html_dom object from a html file
 * @param $file_path
 * @return html_dom object
 */
function file_get_html($file_path)
{
	$html_dom = new Html_dom();
	$html_dom->loadHTMLFile($file_path);
	return $html_dom;
}


class Html_dom 
{
	public $dom;
	public $lowercase = false;
	
	public function __construct($dom = null)
	{
		if(!is_null($dom))
			$this->dom = $dom;
	}
	
	/**
	 * Convert a CSS selector (similar to jQuery) to a valid xpath selector
	 * @param string $q
	 * @return 
	 */
	public static function cssSelectorToXPath($q)
	{
		$patterns = array();
		$patterns[0] = '/^([a-z\-:_\.]+)/';
		$patterns[1] = '/^#/';
		$patterns[2] = '/^\./';
		$patterns[3] = '/\s+\>\s+/';
		$patterns[4] = '/\s+/';
		$patterns[5] = '/(#)([\w\-:_\.]+)/';
		$patterns[6] = '/(\.)([\w\-:_\.]+)/';
		// $patterns[6] = '/^([a-z]+)\s+([a-z+)/';
		$replacements = array();
		$replacements[0] = '\1';
		$replacements[1] = '*#';
		$replacements[2] = '*.';
		$replacements[3] = '/';
		$replacements[4] = '//';
		$replacements[5] = '[@id="\2"]';
		$replacements[6] = '[contains(@class,"\2")]';
		// $replacements[6] = '\1\/\/\2';
		$a = preg_replace($patterns, $replacements, $q);
		// echo $a."<br />\n";
		return $a;
	}
	
	/**
	 * Load a html_dom object from a html string
	 * @param string $str
	 * @param string $encoding [optional]
	 * @return 
	 */
	public function loadHTML($str, $encoding = 'UTF-8')
	{
		libxml_use_internal_errors(true);
		$this->dom = new DOMDocument('1.0', $encoding);
		$this->dom->formatOutput = true;
		$this->dom->substituteEntities = false;
		$this->dom->recover = true;
		$this->dom->strictErrorChecking = false;
		$this->dom->encoding = $encoding;
		
		$str = mb_convert_encoding($str, 'HTML-ENTITIES', $encoding);// need this to fix encoding problem
		$this->dom->loadHTML('<?xml encoding="'.$encoding.'">' .$str);
		foreach ($this->dom->childNodes as $item)
	    	if ($item->nodeType == XML_PI_NODE)
	        	$this->dom->removeChild($item);
	}
	
	/**
	 * Load a html_dom object from a html file
	 * @param $file_path
	 */
	public function loadHTMLFile($file_path)
	{
		$this->loadHTML(file_get_contents($file_path));
	}
	
	/**
	 * Output HTML file to the screen, and save it to a file if a file path is specified
	 * @param $file_path [optional]
	 * @return HTML file
	 */
	public function save($file_path = '')
	{
		if(!empty($file_path))
			$this->dom->saveHTMLFile($file_path);
		
		return $this->dom->saveHTML();
	}
	
	/**
	 * Find 1 or more dom element matching the css selector
	 * @param string $selector
	 * @param int $index [optional]
	 * @return 1 dom element if index is specified or array of dom element if index is null
	 */
	public function find($selector, $index = null)
	{
		$aElements = array();
		
		$dom_xpath = new Html_dom_xpath($this->dom);
		
		$xpathSelector = Html_dom::cssSelectorToXPath($selector);
		$aElements = $dom_xpath->select($xpathSelector);
		
		if($index<0) 
			$index = $aElements->count() + $index;
		
		if(is_null($index))
			return $aElements;
		else
			return ($aElements->offsetExists($index))?$aElements->offsetGet($index):array();
	}
}

class Html_dom_xpath
{
	private $xpath;
	
	function __construct(&$dom)
	{
		$this->dom = $dom;
		$this->xpath = new DOMXpath($this->dom);
		$this->xpath->registerNamespace('html','http://www.w3.org/1999/xhtml');
	}
	
	/**
	 * Perform a xpath query
	 * @param string $q
	 * @param $relatedNode [optional]
	 * @return array of html dom element
	 */
	public function select($q, &$relatedNode = null)
	{
		if(is_null($relatedNode))
		{
			$nodeList = $this->xpath->query('//'.$q);
			$isRelated = 'no';
		}
		else
		{
			$nodeList = $this->xpath->query('.//'.$q, $relatedNode);
			$isRelated = 'yes';
		}
		
		$a = array();
		if($nodeList !== false)
		{
			foreach($nodeList as $node)
				$a[] = new Html_dom_node($node, $this->dom);
			
			return new Html_dom_node_collection($a);
		}
		else
		{
			if(function_exists('log_message'))
				log_message("debug", "xpath selector is not valid : {$q} | Is related:{$isRelated}");
			else
				throw new Exception("xpath selector is not valid : {$q} | Is related:{$isRelated}", 1);
			
		}
	}
	
}

class Html_dom_node
{
	private $node;
	private $dom;
	
	function __construct($nodeItem, &$dom)
	{
		$this->node = $nodeItem;
		$this->dom = $dom;
	}
	
	/**
	 * Get the tag name of a dom element
	 * @return string tag name
	 */
	public function getTag()
	{
		return $this->node->nodeName;
	}

	/**
	 * Get the inner content of a dom element
	 * @return html string
	 */
	public function getInnerText()
	{
		$innerHTML= '';
		$children = $this->node->childNodes;
		if(!is_null($children))
		{
			foreach ($children as $child)
		 	   $innerHTML .= $child->ownerDocument->saveXML($child);
		}
		return $innerHTML; 
	}
	
	/**
	 * Get the outer content of a dom element
	 * @return 
	 */
	public function getOuterText()
	{
		return $this->node->ownerDocument->saveXML($this->node); 
	}
	
	/**
	 * Get the value of an attribute
	 * @param string $attributeName
	 * @return value of the attribute
	 */
	public function getAttr($attributeName)
	{
		return $this->node->getAttribute($attributeName);
	}
	
	public function __get($name) 
	{
        switch($name) 
		{
            case 'innertext': return $this->getInnerText();
			case 'outertext': return $this->getOuterText();
			case 'tag': return $this->getTag();
            default: return $this->getAttr($name);
        }
    }
	
	/**
	 * Set the inner content of a dom element
	 * @param string $value (html or text)
	 * @param string $encoding [optional]
	 * @return 
	 */
	public function setInnerText($value, $encoding= 'UTF-8')
	{
		// Create a new document
		$newdoc = new DOMDocument('1.0');
		libxml_use_internal_errors(true);
		if(empty($value))
			return;
		
		$value = mb_convert_encoding($value, 'HTML-ENTITIES', $encoding);// need this to fix encoding problem
		// make sure the content is utf8
		$value = '<html><head><meta http-equiv="content-type" content="text/html; charset='.$encoding.'" /></head><body><node>'.$value.'</node></body></html>';
		$value = preg_replace_callback("@(<script\b[^>]*>)(.*?)(</script>)@is",array(&$this,'_escapeClosingTagInJavascript'),$value);
		//libxml_use_internal_errors(true);
		$newdoc = new DOMDocument('1.0');
		$newdoc->formatOutput = true;
		$newdoc->substituteEntities = false;
		$newdoc->recover = true;
		$newdoc->strictErrorChecking = false;
		$newdoc->encoding = $encoding;
		$newdoc->loadHTML('<?xml encoding="'.$encoding.'">'.$value);
		
		foreach ($newdoc->childNodes as $item)
	    	if ($item->nodeType == XML_PI_NODE)
	        	$newdoc->removeChild($item);
		
		$list = $newdoc->getElementsByTagName('script');
		foreach ($list as $script) {
		    if ($script->childNodes->length && $script->firstChild->nodeType == 4) {
				$textnode = $script->ownerDocument->createTextNode("\n//");
		        $cdata = $script->ownerDocument->createCDATASection("\n" . $script->firstChild->nodeValue . "\n//");
				$script->removeChild($script->firstChild);
		        $script->appendChild($textnode);
		        $script->appendChild($cdata);
		    }
		}

		// Remove the previous child nodes
		$this->remove_childs($this->node);
		
		// add new nodes
		if(!is_null($newdoc->getElementsByTagName('node')->item(0)))
		{
			foreach($newdoc->getElementsByTagName('node')->item(0)->childNodes as $n)
			{
				$newnode = $this->dom->importNode($n, true);
				
				if($newnode !== false)
				{
					$this->node->appendChild($newnode);
				}
			}
		}
	}
	
	/**
	 * Script tag can cause some problems with html parser, we have to make sure we escape the closing tag
	 */
	private function _escapeClosingTagInJavascript($matches)
	{
		$escaped_string = preg_replace("@</@is","<\/",$matches[2]);
		return $matches[1].$escaped_string.$matches[3];
	}
	
	/**
	 * Set the outer value of a node element (replace the current node)
	 * @param $value
	 * @param string $encoding [optional]
	 */
	public function setOuterText($value, $encoding= 'UTF-8')
	{
		// Create a new document
		$newdoc = new DOMDocument('1.0');
		libxml_use_internal_errors(true);
		if(empty($value))
			return;
		
		$value = mb_convert_encoding($value, 'HTML-ENTITIES', $encoding);// need this to fix encoding problem
		// make sure the content is utf8
		$value = '<html><head><meta http-equiv="content-type" content="text/html; charset='.$encoding.'" /></head><body><node>'.$value.'</node></body></html>';
		$newdoc = new DOMDocument('1.0');
		$newdoc->formatOutput = true;
		$newdoc->substituteEntities = false;
		$newdoc->recover = true;
		$newdoc->strictErrorChecking = false;
		$newdoc->encoding = $encoding;
		$newdoc->loadHTML('<?xml encoding="'.$encoding.'">'.$value);
		
		foreach ($newdoc->childNodes as $item)
	    	if ($item->nodeType == XML_PI_NODE)
	        	$newdoc->removeChild($item);
		
		// The node we want to import to a new document
		$newnode = $this->dom->importNode($newdoc->getElementsByTagName('node')->item(0)->firstChild, true);
		
		// add new nodes
		if(!is_null($newdoc->getElementsByTagName('node')->item(0)))
		{
			// we might have more than one new node, we have to loop through the list
			foreach($newdoc->getElementsByTagName('node')->item(0)->childNodes as $n)
			{
				$newnode = $this->dom->importNode($n, true);
				
				if($newnode !== false)
				{
					// insert the new node before the current one
					$this->node->parentNode->insertBefore($newnode, $this->node);
				}
			}
			// we are done inserting the new nodes, we can delete the current one
			$this->node->parentNode->removeChild($this->node);
		}
	}
	
	public function addClass($value)
	{
		if(!$this->hasClass($value))
			$this->class = trim(trim($this->class).' '.$value);
	}
	
	public function removeClass($value)
	{
		$this->class = trim(str_ireplace($value, '', $this->class));
	}
	
	public function hasClass($value)
	{
		if(!$this->class)
			return false;
		$classArray = explode(' ', $this->class);
		return in_array($value, $classArray);
	}
	
	/**
	 * Set the value of a dom element attribute
	 * @param $attributeName
	 * @param $value
	 */
	public function setAttr($attributeName, $value)
	{
		$this->node->setAttribute($attributeName, $value);
	}
	
	/**
	 * Set the value of a dom element attribute
	 * @param $attributeName
	 * @param $value
	 */
	public function removeAttr($attributeName)
	{
		$this->node->removeAttribute($attributeName);
	}
	
	public function __set($name, $value) 
	{
        switch($name) 
		{
            case 'innertext': return $this->setInnerText($value);
			case 'outertext': return $this->setOuterText($value);
			case 'addClass': return $this->addClass($value);
			case 'removeClass': return $this->removeClass($value);
			default: return $this->setAttr($name, $value);
        }
    }
	
	/**
	 * Find the first child a dom element
	 * @return dom element
	 */
	public function first_child()
	{
		$childs = $this->children();
		return reset($childs);
	}
	
	/**
	 * Find the last child a dom element
	 * @return dom element
	 */
	public function last_child()
	{
		$childs = $this->children();
		return end($childs);
	}
	
	/**
	 * Find the immediate previous sibling
	 * @return dom element
	 */
	public function previous_sibling()
	{
		$previousSibling = $this->_move_prev_element($this->node);
		if(!is_null($previousSibling))
			return new Html_dom_node($previousSibling, $this->dom);
		else
			return NULL;
	}
	
	/**
	 * Find the immediate next sibling
	 * @return dom element
	 */
	public function next_sibling()
	{
		$nextSibling = $this->_move_next_element($this->node);
		if(!is_null($nextSibling))
			return new Html_dom_node($nextSibling, $this->dom);
		else
			return NULL;
	}
	
	private function _move_next_element($node)
	{
		$nextSibling = $node->nextSibling;
		if(is_null($nextSibling))
			return null;
		elseif($nextSibling->nodeType == 1)
			return $nextSibling;
		else
			return $this->_move_next_element($node->nextSibling);
	}
	
	private function _move_prev_element($node)
	{
		$previousSibling = $node->previousSibling;
		if(is_null($previousSibling))
			return null;
		elseif($previousSibling->nodeType == 1)
			return $previousSibling;
		else
			return $this->_move_prev_element($node->previousSibling);
	}
	
	/**
	 * Find all the children of a dom element
	 * @return array of dom element
	 */
	public function children()
	{
		$a = array();
		if($this->node->childNodes->length)
		{
			foreach($this->node->childNodes as $node)
			{
				if($node->nodeType == 1)
					$a[] = new Html_dom_node($node, $this->dom);
			}
		}
		return $a;
	}
	
	/**
	 * Find all the siblings of a dom element
	 * @return array of dom elements
	 */
	public function siblings()
	{
		$a = array();
		if($this->node->parentNode->childNodes->length)
		{
			foreach($this->node->parentNode->childNodes as $node)
			{
				if($node->nodeType == 1 && !$this->node->isSameNode($node))
					$a[] = new Html_dom_node($node, $this->dom);
			}
		}
		
		return $a;
	}
	
	/**
	 * Find the parent node of a dom element
	 * @return 
	 */
	public function parent()
	{
		$parentNode = new Html_dom_node($this->node->parentNode, $this->dom);
		return $parentNode;
	}
	
	/**
	 * Perform a search inside a dom element
	 * @param string $selector
	 * @param int $index [optional]
	 * @return 1 dom element if index is specified or array of dom element if index is null
	 */
	public function find($selector, $index = null)
	{
		$aElements = array();
		
		$dom_xpath = new Html_dom_xpath($this->dom);
		
		$xpathSelector = Html_dom::cssSelectorToXPath($selector);
		$aElements = $dom_xpath->select($xpathSelector, $this->node);
		
		if($index<0) 
			$index = $aElements->count() + $index;
		
		if(is_null($index))
			return $aElements;
		else
			return ($aElements->offsetExists($index))?$aElements->offsetGet($index):array();
	}
	
	/**
	 * Remove the current node and all children
	 * @return 
	 */
	public function remove()
	{
		$this->node->parentNode->removeChild($this->node);
	}
	
	/**
	 * Remove all childs of a dom element
	 * @param $node [optional] 
	 */
	public function remove_childs(&$node = NULL)
	{
		// if no node specified, use the current node
		if(is_null($node))
			$node = $this->node;
		
		while($node->firstChild)
		{
			while ($node->firstChild->firstChild)
			{
				$this->remove_childs($node->firstChild);
			}
			$node->removeChild($node->firstChild);
		}
	}
}

class Html_dom_node_collection extends ArrayObject 
{
	private $iterator = array();
	
	public function __construct($arrDomNode)
	{
		parent::__construct($arrDomNode);
		$this->iterator = $this->getIterator();
	}
	
	public function __call($name, $arguments = null)
	{
		$value = (!is_null($arguments) && isset($arguments[0]))?$arguments[0]:null;
		
		switch($name)
		{
			case 'seek' :
			case 'rewind' :
			case 'next' :
				$this->iterator->$name();
				return $this->iterator;
				break;
			case 'current' :
				return $this->iterator->current();
				break;
			case 'valid' :
				return $this->iterator->valid();
				break;
			default :
				while($this->iterator->valid())
				{
					$this->iterator->current()->$name($value);
			    	$this->iterator->next();
				}
		}
	}
	
	public function __get($name) 
	{
		$out = array();
		// $iterator = $this->getIterator();
		while($this->iterator->valid())
		{
			$out[] = $this->iterator->current()->$name;
		    $this->iterator->next();
		}
		return $out;
    }
	
	public function __set($name, $value) 
	{
		// $iterator = $this->getIterator();
		while($this->iterator->valid())
		{
			$this->iterator->current()->$name = $value;
	    	$this->iterator->next();
		}
    }
}
