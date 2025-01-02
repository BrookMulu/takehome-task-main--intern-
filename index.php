<?php

// TODO A: Improve the readability of this file through refactoring and documentation.

// TODO B: Review the HTML structure and make sure that it is valid and contains
// required elements. Edit and re-organize the HTML as needed.

// TODO C: Review the index.php entrypoint for security and performance concerns
// and provide fixes. Note any issues you don't have time to fix.

// TODO D: The list of available articles is hardcoded. Add code to get a
// dynamically generated list.

use App\App;

require_once __DIR__ . '/vendor/autoload.php';

$app = new App();

/**
 * The HTML <head> section outputs the necessary stylesheets and scripts.
 */
function renderHead(){
	echo "<head>
	<link rel='stylesheet' href='http://design.wikimedia.org/style-guide/css/build/wmui-style-guide.min.css'>
	<link rel='stylesheet' href='styles.css'>
	<script src='main.js'></script>
	</head>";
}

/**
 * Get Requests to fetch article content.
 * @param App $app instance fof application to fetch articles.
 * @return array an array that contains the title and body of the article.
 */
function fetchArticleContent(App $app):array{
	$title = '';
	$body = '';
	if ( isset( $_GET['title'] ) ) {
		//changed htmlentities to htmlspecialchars due to xss vulnerability
		$title = htmlspecialchars( $_GET['title'] );
		$body = $app->fetch( $_GET );
		$body = file_get_contents( sprintf( 'articles/%s', $title ) );
	}
	return ['title' => $title, 'body' => $body];
}

/**
 * fetches count of all words in the article's directory.
 * @return a string with the total number of words found.
 */
function getWordCount() {
	global $wgBaseArticlePath;
	$wgBaseArticlePath = 'articles/';
	$wc = 0;
	$dir = new DirectoryIterator( $wgBaseArticlePath );
	foreach ( $dir as $fileinfo ) {
		if ( $fileinfo->isDot() ) {
			continue;
		}
		$c = file_get_contents( $wgBaseArticlePath . $fileinfo->getFilename() );
		$ch = explode( " ", $c );
		$wc += count( $ch );
	}
	return "$wc words written";
}

/**
 * handles saving article using a post request.
 * @param App $app instance of application to save article data.
 */
function saveArticle(App $app):void{
	if ( $_POST ) {
		$app->save( sprintf( "articles/%s", $_POST['title'] ), $_POST['body'] );
	}
}

/**
* Renders the main page content, including the article form, preview, and article list.
* @param string $title The title of the article.
* @param string $body The body of the article.
* @param string $wordCount The word count of all articles.
*/

function renderPage(string $title, string $body, string $wordCount): void {
	echo
	"<body>
		<div id='header' class='header'>
		<a href='/'>Article editor</a><div> $wordCount </div>
		</div>
		<div class='page'>
		<div class='main'>
			<h2>Create/Edit Article</h2>
			<p>Create a new article by filling out the fields below. Edit an article by typing the beginning of the title in the title field, selecting the title from the auto-complete list, and changing the text in the textfield.</p>
			<form action='index.php' method='post'>
			<input name='title' type='text' placeholder='Article title...' value='$title'>
			<br />
			<textarea name='body' placeholder='Article body...'>$body</textarea>
			<br />
			<button type='submit' class='submit-button'>Submit</button>
			</form>
			<h2>Preview</h2>
			<div> $title</div>
			<div> $body</div>
			<h2>Articles</h2>
			<ul>
				<li><a href='index.php?title=Foo'>Foo</a></li>
			</ul>
		</div>
		</div>
	</body>";
}

// Main Execution
renderHead();
$wordCount = getWordCount();
$articleData = fetchArticleContent($app);
saveArticle($app);
renderPage($articleData['title'], $articleData['body'], $wordCount);

