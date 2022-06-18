<?php
$query = "";
if(isset($_POST['query'])){
	$query = $_POST['query'];
	require_once('functions.php');
	$data = Client::get_news($query);
	$output = array("data"=>json_decode($data,true));
}
?>
<html>
<head></head>

<body>
<form method="POST">
	<input type="text" name="query" placeholder="What do you want?" value="<?php echo $query;?>"/>
	<input type="Submit" value="Ask the Oracle"/> 
</form> 
<div id="output"> 
<?php 
/* sample structure 


array ( 'source' => array ( 'id' => 'engadget', 'name' => 'Engadget', ), 
'author' => 'Nicole Lee', 'title' => '[title]', 'description' => '[desc]', 
'url' => '[url]', 'urlToImage' => '[image]', 'publishedAt' => '2020-03-30T17:30:00Z', 'content' => '[content]', )
*/

?>
<?php
/* if(isset($output)){
	foreach($output["data"]["articles"] as $article){
		echo var_export($article, true);
		
	}
	echo "<pre>" . var_export($output["data"], true) . "</pre>";
}*/
?>
<?php  if(isset($output) && isset($output['data']['articles'])): ?>
	<?php foreach($output['data']['articles'] as $art): ?>
		<article>
			<header>
				<h3><?php echo $art['title'];?></h3>
				<h6><?php echo $art['author'];?></h6>
				<img height="100px" width="100px" src="<?php echo $art['urlToImage'];?>"/>
			</header>
			<section>
				<h4>Description</h4>
				<p><?php echo $art['description'];?></p>
			</section>
			<section>
				<h4>Content</h4>
				<p><?php echo $art['content'];?></p>
			</sections>
			<footer>
				<section>
					<h5>Published</h5>
					<time><?php echo $art['publishedAt'];?></time>
				</section>
				<section>
					<a href="<?php echo $art['url'];?>">Link to article</a>
				</section>
			</footer>
			
		</article>
	<?php endforeach; ?>
<?php else: ?>
<div>No articles found for your search terms</div>

<?php endif; ?>
</div>
</body>
</html>
