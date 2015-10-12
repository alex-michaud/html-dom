<?php

include_once 'Html_dom.php';

$html_dom = file_get_html('index.html');

//$start_time = microtime(true);

//for($i=0; $i<1000; $i++)
//{
//    $html_dom->find('#content',0);
//    $html_dom->getElementById('content');
//    $html_dom->find('body', 0);
//    $html_dom->getElementsByTagName('body', 0);
//}

//$end_time = microtime(true);

//echo 'time : '.($end_time-$start_time).' seconds';
//exit;

// echo $html_dom->find('meta',2)->content;
// echo $html_dom->find('li.menu_item', 0)->innertext;
// echo $html_dom->find('meta[http-equiv="Content-Language"]',0)->content;
//echo $html_dom->find('meta[http-equiv="Content-Type"]',0)->content;
//exit;

?>

<h1>Data extracted from index.html </h1>
<p>Number of paragraphs in "id='content'" : <b><?php echo count($html_dom->find('#content p')); ?></b></p>
<p>Number of paragraphs in body : <b><?php echo count($html_dom->find('body p')); ?></b></p>
<p>Get the page title : <b><?php echo $title = $html_dom->find('title', 0)->innertext; ?></b></p>
<p>Number of dom elements with class 'menu_item' : <b><?php echo count($html_dom->find('.menu_item')); ?></b></p>
<p>Content of the first child in the list of items : <b><?php echo $html_dom->find('#content ol', 0)->first_child()->innertext; ?></b></p>
<p>Content of the third child in the list of items : <b><?php echo $html_dom->getElementById('content')->find('ol li')->offsetGet(2)->innertext; ?></b></p>
<p>How many siblings for the second element in the list of items : <b><?php echo $html_dom->find('#content ol li')->offsetGet(1)->siblings()->count(); ?></b></p>
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
