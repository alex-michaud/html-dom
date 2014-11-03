# html-dom #

Fast and easy to use html dom parser written in PHP. It's build on top of php DOMDocument

Require PHP 5.3+


## Usage ##

Simply include the class in this classic way : 
` require_once('Html_dom.php'); `

Then load a dom document like this : 
` $html_dom = file_get_html('example.html'); `

You can also load a html string directly : 
` $html_dom = str_get_html('<ul><li>item 1</li><li>item 3</li><li>item 3</li></ul>'); `

Once you have the document loaded you can parse it, modify it and output the modified version. 

### Output ### 

You can output the document using the **save()** method : 
` echo $html_dom->save(); `

You can also save the output in a file directly if you specify the file path :
` $html_dom->save('/path/to/file.html'); `

### Parse and retrieve data ###	


### Modify document ###
	