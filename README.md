# html-dom #

Fast and easy to use html dom parser written in PHP. It's build on top of php DOMDocument

Require PHP 5.3+


## Usage ##

Simply include the class in this classic way : 
```php
<?php
require_once('Html_dom.php');
```


Then load a dom document like this : 
```php
<?php
$html_dom = file_get_html('example.html');
```

You can also load a html string directly :
```php
<?php
$html_dom = str_get_html('<ul><li>item 1</li><li>item 3</li><li>item 3</li></ul>');
```

Once you have the document loaded you can parse it, modify it and output the modified version. 

### Output ###

You can output the document using the **save()** method : 
```php
<?php
echo $html_dom->save();
```

You can also save the output in a file directly if you specify the file path :
```php
<?php
$html_dom->save('/path/to/file.html');
```

### Parse and retrieve data ###

```php
<?php
$arrDomElements = $html_dom->find('p'); // array of all the "<p>" elements
$domElement = $html_dom->find('p', 0); // first "<p>" element
$domElement = $html_dom->find('p', 1); // second "<p>" element
$arrDomElements = $html_dom->find('div.promo'); // array of DOM element "<div>" with attribute class="promo"
$domElement = $html_dom->find('#login', 0); // DOM element with attribute id="login"
$domElement = $html_dom->find('meta[name="description"]', 0); // DOM meta element with attribute name="description"
$domElement = $html_dom->find('ul', 0)->first_child(); // first child element under "<ul>" (sould be the first "<li>" element)
$li_content = $html_dom->find('ul', 0)->first_child()->innertext; // content of first "<li>" element
$domElement = $html_dom->find('ul', 0)->last_child(); // last child element under "<ul>" (sould be the last "<li>" element)
$li_content = $html_dom->find('ul li', 1)->innertext; // content of second "<li>" element
$arrDomElements = $html_dom->find('ul li'); // array of dom elements
$domElement = $html_dom->find('ul li')->offsetGet(2); // third element in the array
$attrValue = $html_dom->find('a', 0)->href; // value of "href" attribute
$attrValue = $html_dom->find('a', 0)->my_custom_attribute; // value of "my_custom_attribute" attribute (-> will work for any attribute)
```

### Modify document ###

```php
<?php
$html_dom->find('h1', 0)->innertext = 'New H1 title'; // replace H1 title
$html_dom->find('h1', 0)->innertext .= '!!!'; // add exclamations mark to H1 title
$html_dom->find('.menu_item')->addClass('class_test'); // find all the elements with class "menu_item" and add the class "class_test"
$html_dom->find('.menu_item')->class = 'class_test'; // find all the elements with class "menu_item" and replace the class by "class_test"
$html_dom->find('ul li')->removeClass('menu_item'); // find all the "<li>" elements under "<ul>" and remove the class "menu_item"
$html_dom->find('ul li', 0)->hasClass('menu_item'); // find the first "<li>" element under "<ul>" and verify if it has the class "menu_item" (return true or false)

// once you made some modifications, don't forget to output the results
echo $html_dom->save();
```

## API ##

### Class Html_dom ###

##### Methods #####

```php
<?php
loadHTML(string $str)
```

```php
<?php
loadHTMLFile(string $file_path)
```

```php
<?php
save([string $file_path])
```

```php
<?php
find(string $selector[, int $index])
```


### Class Html_dom_node ###

##### Methods #####

```php
<?php
getTag()
```

```php
<?php
getInnerText()
```

```php
<?php
getOuterText()
```

```php
<?php
getAttr(string $attributeName)
```

if you are manipulating a **Html_dom_node** object, you can also use the following shortcut methods
```php
<?php
->innertext // shortcut for ->getInnerText()
->outertext // shortcut for ->getOuterText()
->tag // shortcut for ->getTag()
->class // shortcut for ->getAttr('class')
->href // shortcut for ->getAttr('href')
->id // shortcut for ->getAttr('id')
->title // shortcut for ->getAttr('title')
->my_custom_attribute // shortcut for ->getAttr('my_custom_attribute')
...
```

```php
<?php
setInnerText(string $value)
```

```php
<?php
setOuterText(string $value)
```

```php
<?php
addClass(string $value)
```

```php
<?php
removeClass(string $value)
```

```php
<?php
hasClass(string $value)
```

```php
<?php
setAttr(string $attributeName, string $value)
```

```php
<?php
removeAttr(string $attributeName)
```

if your manipulating a **Html_dom_node** object, you can also use the following shortcut methods
```php
<?php
->innertext = $value // shortcut for ->setInnerText($value)
->outertext = $value // shortcut for ->setOuterText($value)
->class = $value // shortcut for ->setAttr('class', $value)
->href = $value // shortcut for ->setAttr('href', $value)
->id = $value // shortcut for ->setAttr('id', $value)
->title = $value // shortcut for ->setAttr('title', $value)
->my_custom_attribute = $value // shortcut for ->setAttr('my_custom_attribute', $value)
```

```php
<?php
first_child()
```

```php
<?php
last_child()
```

```php
<?php
previous_sibling()
```

```php
<?php
next_sibling()
```

```php
<?php
children()
```

```php
<?php
siblings()
```

```php
<?php
parent()
```

```php
<?php
find(string $selector[, int $index])
```

```php
<?php
remove()
```

```php
<?php
remove_childs()
```


### Class Html_dom_node_collection ###

This Class extends ArrayObject, so all the methods available with ArrayObject can be used here.
[PHP ArrayObject](http://us3.php.net/manual/en/class.arrayobject.php)

Here is a list of the most common methods you might need.

##### Methods #####

```php
<?php
count()
```

```php
<?php
offsetExists(mixed $index)
```

```php
<?php
offsetGet(mixed $index)
```

```php
<?php
offsetSet(mixed $index, mixed $value)
```

```php
<?php
offsetUnset(mixed $index)
```

**You can also iterate in the array using the following methods**

```php
<?php
seek()
```

```php
<?php
rewind()
```

```php
<?php
next()
```

```php
<?php
current() // return the current Html_node
```

```php
<?php
valid() // return a boolean
```

**You can also apply one the the Html_node to all the items in the collections**

_the examples below assume the we have loaded a document into $html_dom_

```php
<?php
$html_dom->find('ul li')->addClass('li_class'); // Will add the class "li_class" to all the "<li>" items
```

```php
<?php
$html_dom->find('ul li')->removeClass('li_class'); // Will remove the class "li_class" to all the "<li>" items
```

