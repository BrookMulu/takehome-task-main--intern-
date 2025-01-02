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

// Content Security Policy (CSP) to enhance security:

header("Content-Security-Policy: default-src 'self'; style-src 'self' http://design.wikimedia.org; script-src 'self';");


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
function fetchArticleContent(App $app): array {
	$title = '';
	$body = '';
	if (isset($_GET['title'])) {
	//Validating and sanitizing title input
		$title = htmlspecialchars(basename($_GET['title']), ENT_QUOTES, 'UTF-8');
		$filePath = sprintf('articles/%s.txt', $title);
		if (file_exists($filePath)) {
			$body = htmlspecialchars(file_get_contents($filePath), ENT_QUOTES, 'UTF-8');
		} 
		else {
			die("Article not found.");
		}
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

/**
 * getsa list of all the articles
 * @return array an array of all articles
 */
function getArticleList(): array {
    $articlesPath = 'articles/';
    $articles = [];

    if (is_dir($articlesPath)) {
        $dir = new DirectoryIterator($articlesPath);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot() || !$fileinfo->isFile()) {
                continue;
            }
            $articles[] = htmlspecialchars($fileinfo->getFilename(), ENT_QUOTES, 'UTF-8');
        }
    }

    return $articles;
}


function renderPage(string $title, string $body, string $wordCount): void {
    // Dynamically get the list of articles
    $articles = getArticleList();
    echo
    "<body>
        <header id='header' class='header'>
            <a href='/'>Article editor</a><div>" . htmlspecialchars($wordCount, ENT_QUOTES, 'UTF-8') . "</div>
        </header>
        <div class='page'>
            <main class='main'>
                <h2>Create/Edit Article</h2>
                <p>Create a new article by filling out the fields below. Edit an article by typing the beginning of the title in the title field, selecting the title from the auto-complete list, and changing the text in the textfield.</p>
                <form action='index.php' method='post'>
                    <input name='title' type='text' placeholder='Article title...' value='" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "'>
                    <br />
                    <textarea name='body' placeholder='Article body...'>" . htmlspecialchars($body, ENT_QUOTES, 'UTF-8') . "</textarea>
                    <br />
                    <button type='submit' class='submit-button'>Submit</button>
                </form>
                <h2>Preview</h2>
                <div>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</div>
                <div>" . htmlspecialchars($body, ENT_QUOTES, 'UTF-8') . "</div>
                <h2>Articles</h2>
                <ul>";
				foreach ($articles as $article) {
					$safeTitle = htmlspecialchars(basename($article, '.txt'), ENT_QUOTES, 'UTF-8');
					echo "<li><a href='index.php?title=" . urlencode($safeTitle) . "'>$safeTitle</a></li>";
				}
    			echo "</ul>
            </main>
        </div>
    </body>";
}


// Main Execution
renderHead();
$wordCount = getWordCount();
$articleData = fetchArticleContent($app);
saveArticle($app);
renderPage($articleData['title'], $articleData['body'], $wordCount);

