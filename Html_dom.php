<?php
/*******************************************************************************
Version: 0.7.0
Date : 2015-10-01
Website: https://github.com/alex-michaud/html-dom
Author: alex michaud <alex.michaud@gmail.com>
Licensed under The MIT License
Redistribution of file must retain the above copyright notice.
 *******************************************************************************/

/**
 * Load a html_dom object from a html string
 * @param $str
 * @param string $encoding
 * @return Html_dom
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
 * @return Html_dom
 */
function file_get_html($file_path, $encoding = 'UTF-8')
{
	$html_dom = new Html_dom();
	$html_dom->loadHTMLFile($file_path, $encoding);
	return $html_dom;
}

function setDomDocumentProperties(&$dom, $encoding = 'UTF-8')
{
	$dom->formatOutput = true;
	$dom->substituteEntities = false;
	$dom->recover = true;
	$dom->strictErrorChecking = false;
	$dom->encoding = $encoding;
}

/**
 * Class Html_dom
 *
 * @property DOMDocument $dom
 * @property bool $lowercase
 */
class Html_dom
{
	public $dom;
	public $lowercase = false;
	private $context = null;

	public function __construct(DOMDocument $dom = null)
	{
		if(!is_null($dom))
			$this->dom = $dom;
	}

	/**
	 * Convert a CSS selector (similar to jQuery) to a valid xpath selector
	 * @param string $q
	 * @return string Xpath selector
	 */
	public static function cssSelectorToXPath($q)
	{
		$patterns = array();
		$patterns[0] = '/^([a-z\-:_\.]+)/i';
		$patterns[1] = '/^#/i';
		$patterns[2] = '/^\./i';
		$patterns[3] = '/^\[/i';
		$patterns[4] = '/\s+\>\s+/i';
		$patterns[5] = '/\s+/i';
		$patterns[6] = '/(#)([\w\-:_\.]+)/i';
		$patterns[7] = '/(\.)([\w\-:_\.]+)/i';
		$patterns[8] = '/\[([\w\-]+)\=([\w\"\-:_\.]+)\]/i';
		// $patterns[8] = '/^([a-z]+)\s+([a-z+)/';
		$replacements = array();
		$replacements[0] = '\1';
		$replacements[1] = '*#';
		$replacements[2] = '*.';
		$replacements[3] = '*[';
		$replacements[4] = '/';
		$replacements[5] = '//';
		$replacements[6] = '[@id="\2"]';
		$replacements[7] = '[contains(@class,"\2")]';
		$replacements[8] = '[contains(@\1,\2)]';
		// $replacements[8] = '\1\/\/\2';
		$a = preg_replace($patterns, $replacements, $q);
		// echo $a." | <br />\n";
		return $a;
	}

	/**
	 * Load a html_dom object from a html string
	 * @param string $str
	 * @param string $encoding
	 * @return void
	 */
	public function loadHTML($str, $encoding = 'UTF-8')
	{
		libxml_use_internal_errors(true);
		$this->dom = new DOMDocument('1.0', $encoding);
		setDomDocumentProperties($this->dom, $encoding);

		$str = mb_convert_encoding($str, 'HTML-ENTITIES', $encoding);// need this to fix encoding problem
		$this->dom->loadHTML('<?xml encoding="'.$encoding.'">' .$str);
		foreach ($this->dom->childNodes as $item)
			if ($item->nodeType == XML_PI_NODE)
				$this->dom->removeChild($item);
	}

	/**
	 * Load a html_dom object from a html file
	 * @param string $file_path
	 * @param string $encoding [optional]
	 */
	public function loadHTMLFile($file_path, $encoding = 'UTF-8')
	{
		$this->loadHTML(file_get_contents($file_path, false, $this->context), $encoding);
	}

	public function setBasicAuth($username, $password)
	{
		$cred = sprintf('Authorization: Basic %s', base64_encode("$username:$password"));
		$opts = array(
			'http' => array(
				'method' => 'GET',
				'header' => $cred
			)
		);
		$this->context = stream_context_create($opts);
	}

	/**
	 * Output HTML file to the screen, and save it to a file if a file path is specified
	 * @param string $file_path [optional]
	 * @return string
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
	 * @return Html_dom_node_collection|Html_dom_node 1 dom element if index is specified or Html_dom_node_collection if index is null
	 */
	public function find($selector, $index = null)
	{
		/** @var Html_dom_xpath $dom_xpath */
		$dom_xpath = new Html_dom_xpath($this->dom);

		$xpathSelector = Html_dom::cssSelectorToXPath($selector);
		$aElements = $dom_xpath->select($xpathSelector);

		if($index < 0)
			$index = $aElements->count() + $index;

		if(is_null($index))
			return $aElements;
		else
			return ($aElements->offsetExists($index)) ? $aElements->offsetGet($index) : array();
	}

	/**
	 * @param string $id
	 * @return Html_dom_node
	 */
	public function getElementById($id)
	{
		return ($element = $this->dom->getElementById($id)) ? new Html_dom_node($element) : null;
	}

	/**
	 * @param $tag
	 * @param null $index
	 * @return Html_dom_node|Html_dom_node_collection|null
	 */
	public function getElementsByTagName($tag, $index = null)
	{
		$items = $this->dom->getElementsByTagName($tag);
		if($items->length)
		{
			if(!is_null($index) && is_numeric($index))
				return ($element = $items->item($index)) ? new Html_dom_node($element) : null;
			else
			{
				$a = array();
				foreach($items as $element)
				{
					$a[] = new Html_dom_node($element);
				}
				return new Html_dom_node_collection($a);
			}
		}
		return null;
	}
}

/**
 * Class Html_dom_xpath
 *
 * @property DOMXpath $xpath
 * @property DOMDocument $dom
 */
class Html_dom_xpath
{
	private $xpath;
	private $dom;

	function __construct(&$dom)
	{
		$this->dom = $dom;
		$this->xpath = new DOMXpath($this->dom);
		$this->xpath->registerNamespace('html','http://www.w3.org/1999/xhtml');
	}

	/**
	 * Perform a xpath query
	 * @param string $q
	 * @param null $relatedNode
	 * @return Html_dom_node_collection
	 * @throws Exception
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
			/** @var DOMElement $element */
			foreach($nodeList as $element)
				$a[] = new Html_dom_node($element);
		}
		else
		{
			if(function_exists('log_message'))
				log_message("debug", "xpath selector is not valid : {$q} | Is related:{$isRelated}");
			else
				throw new Exception("xpath selector is not valid : {$q} | Is related:{$isRelated}", 1);

		}

		return new Html_dom_node_collection($a);
	}

}

/**
 * Class Html_dom_node
 *
 * @property DOMElement $DOMElement
 */
class Html_dom_node
{
	private $DOMElement;

	function __construct(DOMElement $element)
	{
		$this->DOMElement = $element;
	}

	/**
	 * Get the tag name of a dom element
	 * @return string tag name
	 */
	public function getTag()
	{
		return $this->DOMElement->tagName;
	}

	/**
	 * Get the inner content of a dom element
	 * @return string
	 */
	public function getInnerText()
	{
		$innerHTML= '';
		$children = $this->DOMElement->childNodes;
		if(!is_null($children))
		{
			/** @var DOMNode $child */
			foreach ($children as $child)
				$innerHTML .= $child->ownerDocument->saveXML($child);
		}
		return str_replace(chr(13), '', $innerHTML);
	}

	/**
	 * Get the outer content of a dom element
	 * @return string
	 */
	public function getOuterText()
	{
		return str_replace(chr(13), '', $this->DOMElement->ownerDocument->saveXML($this->node));
	}

	/**
	 * Get the value of an attribute
	 * @param string $attributeName
	 * @return string value of the attribute
	 */
	public function getAttr($attributeName)
	{
		return $this->DOMElement->getAttribute($attributeName);
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
	 * @return void
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
		setDomDocumentProperties($newdoc, $encoding);
		$newdoc->loadHTML('<?xml encoding="'.$encoding.'">'.$value);

		foreach ($newdoc->childNodes as $item)
			if ($item->nodeType == XML_PI_NODE)
				$newdoc->removeChild($item);

		$list = $newdoc->getElementsByTagName('script');
		/** @var DOMElement $script */
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
		$this->remove_childs($this->DOMElement);

		// add new nodes
		if(!is_null($newdoc->getElementsByTagName('node')->item(0)))
		{
			/** @var DOMElement $n */
			foreach($newdoc->getElementsByTagName('node')->item(0)->childNodes as $n)
			{
				// The node we want to import to a new document
				$newnode = $this->DOMElement->ownerDocument->importNode($n, true);

				if($newnode !== false)
					$this->DOMElement->appendChild($newnode);
			}
		}
	}

	/**
	 * Script tag can cause some problems with html parser, we have to make sure we escape the closing tag
	 *
	 * @param array $matches
	 * @return string
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
		setDomDocumentProperties($newdoc, $encoding);
		$newdoc->loadHTML('<?xml encoding="'.$encoding.'">'.$value);

		foreach ($newdoc->childNodes as $item)
			if ($item->nodeType == XML_PI_NODE)
				$newdoc->removeChild($item);

		// add new nodes
		if(!is_null($newdoc->getElementsByTagName('node')->item(0)))
		{
			/** @var DOMElement $n we might have more than one new node, we have to loop through the list */
			foreach($newdoc->getElementsByTagName('node')->item(0)->childNodes as $n)
			{
				// The node we want to import to a new document
				$newnode = $this->DOMElement->ownerDocument->importNode($n, true);

				if($newnode !== false)
				{
					// insert the new node before the current one
					$this->DOMElement->parentNode->insertBefore($newnode, $this->DOMElement);
				}
			}
			// we are done inserting the new nodes, we can delete the current one
			$this->DOMElement->parentNode->removeChild($this->DOMElement);
		}
	}

	/**
	 * @param $value
	 * @return void
	 */
	public function addClass($value)
	{
		if(!$this->hasClass($value))
			$this->class = trim(trim($this->class).' '.$value);
	}

	/**
	 * @param $value
	 * @return void
	 */
	public function removeClass($value)
	{
		$this->class = trim(str_ireplace($value, '', $this->class));
	}

	/**
	 * @param $value
	 * @return bool
	 */
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
	 * @return void
	 */
	public function setAttr($attributeName, $value)
	{
		$this->DOMElement->setAttribute($attributeName, $value);
	}

	/**
	 * Set the value of a dom element attribute
	 * @param $attributeName
	 * @return bool
	 */
	public function removeAttr($attributeName)
	{
		return $this->DOMElement->removeAttribute($attributeName);
	}

	public function __set($name, $value)
	{
		switch($name)
		{
			case 'innertext':
				$this->setInnerText($value);
				break;
			case 'outertext':
				$this->setOuterText($value);
				break;
			case 'addClass':
				$this->addClass($value);
				break;
			case 'removeClass':
				$this->removeClass($value);
				break;
			default:
				$this->setAttr($name, $value);
				break;
		}
	}

	/**
	 * Find the first child a dom element
	 * @return Html_dom_node|null
	 */
	public function first_child()
	{
		$children = $this->children();
		return ($children->offsetExists(0)) ? $children->offsetGet(0) : null;
	}

	/**
	 * Find the last child a dom element
	 * @return Html_dom_node
	 */
	public function last_child()
	{
		$children = $this->children();
		return ($count = $children->count()) ? $children->offsetGet($count-1) : null;
	}

	/**
	 * Find the immediate previous sibling
	 * @return Html_dom_node|null
	 */
	public function previous_sibling()
	{
		/** @var DOMElement $previousSibling */
		$previousSibling = $this->_move_prev_element($this->DOMElement);
		if(!is_null($previousSibling))
			return new Html_dom_node($previousSibling);
		else
			return NULL;
	}

	/**
	 * Find the immediate next sibling
	 * @return Html_dom_node|null
	 */
	public function next_sibling()
	{
		/** @var DOMElement $nextSibling */
		$nextSibling = $this->_move_next_element($this->DOMElement);
		if(!is_null($nextSibling))
			return new Html_dom_node($nextSibling);
		else
			return NULL;
	}

	/**
	 * @param $node
	 * @return DOMElement|null
	 */
	private function _move_prev_element(DOMNode $node)
	{
		$previousSibling = $node->previousSibling;
		if(is_null($previousSibling))
			return null;
		elseif($previousSibling->nodeType == XML_ELEMENT_NODE)
			return $previousSibling;
		else
			return $this->_move_prev_element($node->previousSibling);
	}

	/**
	 * @param $node
	 * @return DOMElement|null
	 */
	private function _move_next_element(DOMNode $node)
	{
		$nextSibling = $node->nextSibling;
		if(is_null($nextSibling))
			return null;
		elseif($nextSibling->nodeType == XML_ELEMENT_NODE)
			return $nextSibling;
		else
			return $this->_move_next_element($node->nextSibling);
	}

	/**
	 * Find all the children of a dom element
	 * @return Html_dom_node_collection array of dom element
	 */
	public function children()
	{
		$a = array();
		if($this->DOMElement->childNodes->length)
		{
			foreach($this->DOMElement->childNodes as $node)
			{
				if($node->nodeType == XML_ELEMENT_NODE)
					$a[] = new Html_dom_node($node);
			}
		}

		return new Html_dom_node_collection($a);
	}

	/**
	 * Find all the siblings of a dom element
	 * @return Html_dom_node_collection array of dom elements
	 */
	public function siblings()
	{
		$a = array();
		if($this->DOMElement->parentNode->childNodes->length)
		{
			foreach($this->DOMElement->parentNode->childNodes as $node)
			{
				if($node->nodeType == 1 && !$this->DOMElement->isSameNode($node))
					$a[] = new Html_dom_node($node);
			}
		}

		return new Html_dom_node_collection($a);
	}

	/**
	 * Find the parent node of a dom element
	 * @return Html_dom_node
	 */
	public function parent()
	{
		return new Html_dom_node($this->DOMElement->parentNode);
	}

	/**
	 * Perform a search inside a dom element
	 * @param string $selector
	 * @param int $index [optional]
	 * @return Html_dom_node_collection|Html_dom_node 1 dom element if index is specified or Html_dom_node_collection if index is null
	 */
	public function find($selector, $index = null)
	{
		$dom_xpath = new Html_dom_xpath($this->DOMElement->ownerDocument);

		$xpathSelector = Html_dom::cssSelectorToXPath($selector);
		$aElements = $dom_xpath->select($xpathSelector, $this->DOMElement);

		if($index < 0)
			$index = $aElements->count() + $index;

		if(is_null($index))
			return $aElements;
		else
			return ($aElements->offsetExists($index)) ? $aElements->offsetGet($index) : array();
	}

	/**
	 * @param string $id
	 * @return Html_dom_node
	 */
	public function getElementById($id)
	{
		return ($element = $this->DOMElement->ownerDocument->getElementById($id)) ? new Html_dom_node($element) : null;
	}

	/**
	 * @param $tag
	 * @param null $index
	 * @return Html_dom_node|Html_dom_node_collection|null
	 */
	public function getElementsByTagName($tag, $index = null)
	{
		$items = $this->DOMElement->ownerDocument->getElementsByTagName($tag);
		if($items->length)
		{
			if(!is_null($index) && is_numeric($index))
				return ($element = $items->item($index)) ? new Html_dom_node($element) : null;
			else
			{
				$a = array();
				foreach($items as $element)
				{
					$a[] = new Html_dom_node($element);
				}
				return new Html_dom_node_collection($a);
			}
		}
		return array();
	}

	/**
	 * Remove the current node and all children
	 * @return DOMNode|bool
	 */
	public function remove()
	{
		return $this->DOMElement->parentNode->removeChild($this->DOMElement);
	}

	/**
	 * Remove all childs of a dom element
	 * @param $node [optional]
	 * @return void
	 */
	public function remove_childs(&$node = NULL)
	{
		// if no node specified, use the current node
		if(is_null($node))
			$node = $this->DOMElement;

		while($node->firstChild)
		{
			while ($node->firstChild->firstChild)
				$this->remove_childs($node->firstChild);

			$node->removeChild($node->firstChild);
		}
	}
}

/**
 * Class Html_dom_node_collection
 *
 * @property ArrayIterator $iterator
 * @method seek()
 * @method rewind()
 * @method next()
 * @method current()
 * @method valid()
 */
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
		$value = (!is_null($arguments) && isset($arguments[0])) ? $arguments[0] : null;

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
				break;
		}
	}

	public function __get($name)
	{
		$out = array();
		while($this->iterator->valid())
		{
			$out[] = $this->iterator->current()->$name;
			$this->iterator->next();
		}
		return $out;
	}

	public function __set($name, $value)
	{
		while($this->iterator->valid())
		{
			$this->iterator->current()->$name = $value;
			$this->iterator->next();
		}
	}
}
