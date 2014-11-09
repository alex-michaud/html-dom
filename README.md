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
$arrDomElements = $html_dom->find("p"); // array of all the "<p>" elements
$domElement = $html_dom->find("p", 0); // first "<p>" element
$domElement = $html_dom->find("p", 1); // second "<p>" element
$arrDomElements = $html_dom->find("div.promo"); // array of DOM element "<div>" with attribute class="promo"
$domElement = $html_dom->find("#login", 0); // DOM element with attribute id="login"
$domElement = $html_dom->find("ul", 0)->first_child(); // first child element under "<ul>" (sould be the first "<li>" element)
$li_content = $html_dom->find("ul", 0)->first_child()->innertext; // content of first "<li>" element
$domElement = $html_dom->find("ul", 0)->last_child(); // last child element under "<ul>" (sould be the last "<li>" element)
$li_content = $html_dom->find("ul li", 1)->innertext; // content of second "<li>" element
$arrDomElements = $html_dom->find("ul li"); // array of dom elements
$domElement = $html_dom->find("ul li")->offsetGet(2); // third element in the array
$attrValue = $html_dom->find("a", 0)->href; // value of "href" attribute
$attrValue = $html_dom->find("a", 0)->my_custom_attribute; // value of "my_custom_attribute" attribute (-> will work for any attribute)
```

### Modify document ###

```php
<?php
$html_dom->find("h1", 0)->innertext = 'New H1 title'; // replace H1 title
$html_dom->find("h1", 0)->innertext .= '!!!'; // add exclamations mark to H1 title
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

```ruby
loadHTML(string $str)
```

```ruby
loadHTMLFile(string $file_path)
```

```ruby
save([string $file_path])
```

```ruby
find(string $selector[, int $index])
```


### Class Html_dom_node ###

##### Methods #####

```ruby
getTag()
```

```ruby
getInnerText()
```

```ruby
getOuterText()
```

```ruby
getAttr(string $attributeName)
```

if your manipulating a **Html_dom_node** object, you can also use the following shortcut methods
```ruby
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

```ruby
setInnerText(string $value)
```

```ruby
setOuterText(string $value)
```

```ruby
addClass(string $value)
```

```ruby
removeClass(string $value)
```

```ruby
hasClass(string $value)
```

```ruby
setAttr(string $attributeName, string $value)
```

```ruby
removeAttr(string $attributeName)
```

if your manipulating a **Html_dom_node** object, you can also use the following shortcut methods
```ruby
->innertext = $value // shortcut for ->setInnerText($value)
->outertext = $value // shortcut for ->setOuterText($value)
->class = $value // shortcut for ->setAttr('class', $value)
->href = $value // shortcut for ->setAttr('href', $value)
->id = $value // shortcut for ->setAttr('id', $value)
->title = $value // shortcut for ->setAttr('title', $value)
->my_custom_attribute = $value // shortcut for ->setAttr('my_custom_attribute', $value)
```

```ruby
first_child()
```

```ruby
last_child()
```

```ruby
previous_sibling()
```

```ruby
next_sibling()
```

```ruby
children()
```

```ruby
siblings()
```

```ruby
parent()
```

```ruby
find(string $selector[, int $index])
```

```ruby
remove()
```

```ruby
remove_childs()
```


### Class Html_dom_node_collection ###

##### Methods #####

```ruby

```