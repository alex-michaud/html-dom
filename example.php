<?php

include_once 'Html_dom.php';

// $html_dom = new Html_dom();

$html_dom = file_get_html('example.html');

// $html_dom->find('.menu_item')->addClass('class_test');
// $html_dom->find('.menu_item')->class = 'class_test';
// 
// echo $html_dom->find('nav', 0)->outertext;exit;

// $arrItems = $html_dom->find('#content ol li');
// $arrItems->rewind();
// var_dump($arrItems->current());
// var_dump($arrItems->current()->innertext);
// $arrItems->next();
// var_dump($arrItems->next()->current()->innertext);exit;
?>

<h1>Data extracted from example.html </h1>
<p>Number of paragraphs in "id='content'" : <?php echo count($html_dom->find('#content p')); ?></p>
<p>Number of paragraphs in body : <?php echo count($html_dom->find('body p')); ?></p>
<p>Get the page title : <?php echo $title = $html_dom->find('title', 0)->innertext; ?></p>
<p>Number of dom elements with class 'menu_item' : <?php echo count($html_dom->find('.menu_item')); ?></p>
<p>Content of the first child in the list of items : <?php echo $html_dom->find('#content ol', 0)->first_child()->innertext; ?></p>
<p>Content of the third child in the list of items : <?php echo $html_dom->find('#content ol li')->offsetGet(2)->innertext; ?></p>
<p>Value of first menu item href attribute : <?php echo $html_dom->find('nav a', 0)->href; ?></p>
<p>Add an attribute to a list of node : <?php $html_dom->find('.menu_item')->addClass('class_test'); ?></p>
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
