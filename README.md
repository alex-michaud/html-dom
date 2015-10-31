# html-dom #

Fast and easy to use html dom parser written in PHP. It's build on top of php DOMDocument

Require PHP 5.3+


## Usage ##

Simply include the class in this classic way : 
``` php
require_once('Html_dom.php');
```

Then load a dom document like this : 
``` php
$html_dom = file_get_html('index.html');
```

You can also load a html string directly :
``` php
$html_dom = str_get_html('<ul><li>item 1</li><li>item 2</li><li>item 3</li></ul>');
```

Once you have the document loaded you can parse it, modify it and output the modified version. 

### Output ###

You can output the document using the **save()** method : 
``` php
echo $html_dom->save();
```

You can also save the output in a file directly if you specify the file path :
``` php
$html_dom->save('/path/to/file.html');
```

### Parse document ###

Parsing a document can be done with diffrent methods. The fastest one, if you have the element id, is _getElementById()_.
The second fastest one is probably _getElementsByTagName()_.
Finally, the general one where you can pass all kinds of selector is _find()_. 

#### _Html_dom_node_ getElementById(string $elementId)

``` php
$contentElement = $html_dom->getElementById('content');
```

#### _Html_dom_node_collection_ getElementsByTagName(string $tagName)
#### _Html_dom_node_ getElementsByTagName(string $tagName, int $index)

``` php
$liElementCollection = $html_dom->getElementsByTagName('li');
$secondLiElement = $html_dom->getElementsByTagName('li', 1);
```

#### _Html_dom_node_collection_ find(string $cssSelector)
#### _Html_dom_node_ find(string $cssSelector, int $index)

``` php
$pElementCollection = $html_dom->find('p'); // array of all the "<p>" elements
$pElement = $html_dom->find('p', 0); // first "<p>" element
$pElement = $html_dom->find('p', 1); // second "<p>" element
$elementCollection = $html_dom->find('div.promo'); // array of DOM element "<div>" with attribute class="promo"
$element = $html_dom->find('#login', 0); // DOM element with attribute id="login"
$element = $html_dom->find('meta[name="description"]', 0); // DOM meta element with attribute name="description"
$element = $html_dom->find('ul', 0)->first_child(); // first child element under "<ul>" (sould be the first "<li>" element)
$element = $html_dom->find('ul', 0)->last_child(); // last child element under "<ul>" (sould be the last "<li>" element)
$liElementCollection = $html_dom->find('ul li'); // array of dom elements
$element = $html_dom->find('ul li')->offsetGet(2); // third element in the array
```

### Retrieve data ###

Once we have a **_Html_dom_node_** or a **_Html_dom_node_collection_**, we can retrieve some data.

``` php
$ul_content = $html_dom->find('ul', 0)->innertext; // content of first "<ul>" element
$li_content = $html_dom->find('ul li', 1)->innertext; // content of second "<li>" element
$attrValue = $html_dom->find('a', 0)->href; // value of "href" attribute
$attrValue = $html_dom->find('a', 0)->my_custom_attribute; // value of "my_custom_attribute" attribute (will work for any attribute)
```


### Modify document ###

You can modify the content of a Html_node or modify its attributes.

``` php
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

``` php
loadHTML(string $str)
```

``` php
loadHTMLFile(string $file_path)
```

``` php
setBasicAuth(string $username, string $password)

Example : 
$html_dom = new Html_dom();
$html_dom->setBasicAuth('username', 'secret_password');
$html_dom->loadHTMLFile('/path/to/file.html')
```

``` php
getElementById(string $elementId)
```

``` php
getElementsByTagName(string $tagName[, int $index])
```

``` php
save(string $file_path)
```

``` php
find(string $selector[, int $index])
```


### Class Html_dom_node ###

**Let's assume that we have a code that start with this**
``` php
$html_dom = file_get_html('index.html');
$html_dom_node = $html_dom->getElementById('content');
```

#### _string_ getTag()
``` php
$html_dom_node->getTag();
OR
$html_dom_node->tag;
```

#### _string_ getInnerText()
``` php
$html_dom_node->getInnerText();
OR
$html_dom_node->innertext;
```

#### _string_ getOuterText()
``` php
$html_dom_node->getOuterText();
OR
$html_dom_node->outertext;
```

#### _string_ getAttr(string $attributeName)
``` php
$html_dom_node->getAttr(string $attributeName)
OR
$html_dom_node->attribute_name;

Examples : 
$html_dom_node->class;
$html_dom_node->id;
$html_dom_node->href;
$html_dom_node->title;
$html_dom_node->my_custom_attribute;
```

#### _void_ setInnerText(string $value)
``` php
$html_dom_node->setInnerText($value);
OR
$html_dom_node->innertext = $value;
```

#### _void_ setOuterText(string $value)
``` php
$html_dom_node->setOuterText($value);
OR
$html_dom_node->outertext = $value;
```

#### _void_ append(string $value)
``` php
$html_dom_node->append($value);
```

#### _void_ prepend(string $value)
``` php
$html_dom_node->prepend($value);
```

#### _void_ addClass(string $class_name)
``` php
$html_dom_node->addClass($class_name);
```

#### _void_ removeClass(string $class_name)
``` php
$html_dom_node->removeClass($class_name);
```

#### _bool_ hasClass(string $class_name)
``` php
$html_dom_node->hasClass($class_name);
```

#### _void_ setAttr(string $attributeName, string $value)
``` php
$html_dom_node->setAttr($attributeName, $value);
OR
$html_dom_node->attribute_name = $value;

Examples : 
$html_dom_node->class = 'my_class';
$html_dom_node->id = 'element_id';
$html_dom_node->href = 'www.example.com';
$html_dom_node->title = 'My title';
$html_dom_node->my_custom_attribute = 'my_custom_value';
```

#### _boolean_ removeAttr(string $attributeName)
``` php
$html_dom_node->removeAttr($attributeName)
```

#### _Html_dom_node_ first_child()
``` php
$firstChildElement = $html_dom_node->first_child();
```

#### _Html_dom_node_ last_child()
``` php
$lastChildElement = $html_dom_node->last_child();
```

#### _Html_dom_node_ previous_sibling()
``` php
$previousElement = $html_dom_node->previous_sibling();
```

#### _Html_dom_node_ next_sibling()
``` php
$nextElement = $html_dom_node->next_sibling();
```

#### _Html_dom_node_collection_ children()
``` php
$elementCollection = $html_dom_node->children();
```

#### _Html_dom_node_collection_ siblings()
``` php
$elementCollection = $html_dom_node->siblings();
```

#### _Html_dom_node_ parent()
``` php
$parentElement = $html_dom_node->parent();
```

#### _mixed_ find(string $selector[, int $index])
``` php
$elementCollection = $html_dom_node->find('li');
$element = $html_dom_node->find('li', 0);
```

#### _Html_dom_node_ getElementById(string $elementId)
``` php
$element = $html_dom_node->getElementById('content');
```

#### _mixed_ getElementsByTagName(string $selector[, int $index])
``` php
$elementCollection = $html_dom_node->getElementsByTagName('li');
$element = $html_dom_node->getElementsByTagName('li', 0);
```

#### _mixed_ remove()
``` php
$html_dom_node->remove();
```

#### _void_ remove_childs()
``` php
$html_dom_node->remove_childs()
```


### Class Html_dom_node_collection ###

This Class extends ArrayObject, so all the methods available with ArrayObject can be used here.
[PHP ArrayObject](http://us3.php.net/manual/en/class.arrayobject.php)

Here is a list of the most common methods you might need.

##### Methods #####


#### _integer_ count()
``` php
$html_dom_node_collection->count();
```

#### _boolean_ offsetExists(mixed $index)
``` php
$html_dom_node_collection->offsetExists(mixed $index);
```

#### _Html_dom_node_ offsetGet(mixed $index)
``` php
$html_dom_node_collection->offsetGet($index);
```

#### _void_ offsetSet(mixed $index, mixed $value)
``` php
$html_dom_node_collection->offsetSet($index, $value);
```

#### _void_ offsetUnset(mixed $index)
``` php
$html_dom_node_collection->offsetUnset($index);
```

**You can also iterate in the array using the following methods**

``` php
seek()
$html_dom_node_collection->seek();
```

``` php
rewind()
$html_dom_node_collection->rewind();
```

``` php
next()
$html_dom_node_collection->next();
```

``` php
current() // return the current Html_node
$html_dom_node_collection->current();
```

``` php
valid() // return a boolean
$html_dom_node_collection->valid();
```

### You can also apply one the the _Html_dom_node_ method to all the items of a _Html_dom_node_collection_

_the examples below assume the we have loaded a document into $html_dom_

``` php
$html_dom->find('ul li')->addClass('li_class'); // Will add the class "li_class" to all the "<li>" items
```

``` php
$html_dom->find('ul li')->removeClass('li_class'); // Will remove the class "li_class" to all the "<li>" items
```

