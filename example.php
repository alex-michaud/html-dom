<?php

include_once 'Html_dom.php';

// $html_dom = new Html_dom();

$html_dom = file_get_html('example.html');

// echo $html_dom->find('meta',2)->content;
// echo $html_dom->find('li.menu_item', 0)->innertext;
// echo $html_dom->find('meta[http-equiv="Content-Language"]',0)->content;
// exit;

// $html_dom->find('.menu_item')->addClass('class_test');
// $html_dom->find('.menu_item')->class = 'class_test';
// 
// echo $html_dom->find('nav', 0)->outertext;exit;

// $html_dom->find('#content ol li')->addClass('strange_class');
// echo $html_dom->save(); exit;

// $arrItems = $html_dom->find('#content ol li');
// $arrItems->rewind();
// var_dump($arrItems->current());
// var_dump($arrItems->current()->innertext);
// $arrItems->next();
// var_dump($arrItems->next()->current()->innertext);exit;
?>

<h1>Data extracted from example.html </h1>
<p>Number of paragraphs in "id='content'" : <b><?php echo count($html_dom->find('#content p')); ?></b></p>
<p>Number of paragraphs in body : <b><?php echo count($html_dom->find('body p')); ?></b></p>
<p>Get the page title : <b><?php echo $title = $html_dom->find('title', 0)->innertext; ?></b></p>
<p>Number of dom elements with class 'menu_item' : <b><?php echo count($html_dom->find('.menu_item')); ?></b></p>
<p>Content of the first child in the list of items : <b><?php echo $html_dom->find('#content ol', 0)->first_child()->innertext; ?></b></p>
<p>Content of the third child in the list of items : <b><?php echo $html_dom->find('#content ol li')->offsetGet(2)->innertext; ?></b></p>
<p>Value of first menu item href attribute : <b><?php echo $html_dom->find('nav a', 0)->href; ?></b></p>
<p>Value of the content attribute of the head meta element with attribute name="description" : <b><?php echo $html_dom->find('meta[name="description"]', 0)->content; ?></b></p>
<p>Add an attribute to a list of node : <b><?php $html_dom->find('.menu_item')->addClass('class_test'); ?></b></p>
<hr />
<h3>Content of nav</h3>
<div><?php echo $html_dom->find('nav', 0)->innertext?></div>
<h3>Same nav but we add 1 item</h3>
<div>
<?php
$html_dom->find('nav ul', 0)->innertext .= '<li class="menu_item"><a href="#/new-item">New item</a></li>';
echo $html_dom->find('nav', 0)->innertext;
?>
</div>
