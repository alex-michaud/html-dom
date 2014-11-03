# html-dom #

Fast and easy to use html dom parser written in PHP. It's build on top of php DOMDocument

Require PHP 5.3+


## Usage ##

Simply include the class in this classic way : 
```php
require_once('Html_dom.php');
```


Then load a dom document like this : 
```php
$html_dom = file_get_html('example.html');
```

You can also load a html string directly :
```php
$html_dom = str_get_html('<ul><li>item 1</li><li>item 3</li><li>item 3</li></ul>');
```

Once you have the document loaded you can parse it, modify it and output the modified version. 

### Output ###

You can output the document using the **save()** method : 
```php
echo $html_dom->save();
```

You can also save the output in a file directly if you specify the file path :
```php
$html_dom->save('/path/to/file.html');
```

### Parse and retrieve data ###

```php
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
```

### Modify document ###
	