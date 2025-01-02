<?php

use App\App;

// TODO A: Improve the readability of this file through refactoring and documentation.

require_once __DIR__ . '/vendor/autoload.php';

$app = new App();
// TODO B: Clean up the following code so that it's easier to see the different
// routes and handlers for the API, and simpler to add new ones.
// TODO C: Address performance concerns in the current code.
// that you would address during refactoring.
// TODO D: Identify any potential security vulnerabilities in this code.

header( 'Content-Type: application/json' );
if ( !isset( $_GET['title'] ) && !isset( $_GET['prefixsearch'] ) ) {
	echo json_encode( [ 'content' => $app->getListOfArticles() ] );
} elseif ( isset( $_GET['prefixsearch'] ) ) {
	$list = $app->getListOfArticles();
	$ma = [];
	foreach ( $list as $ar ) {
		if ( strpos( strtolower( $ar ), strtolower( $_GET['prefixsearch'] ) ) === 0 ) {
			$ma[] = $ar;
		}
	}
	echo json_encode( [ 'content' => $ma ] );
} else {
	echo json_encode( [ 'content' => $app->fetch( $_GET ) ] );
}

/**
 * * If I had more time for the remaining todo's:
 * TODO A: I would have improved the readability by refracroring the file
 * into methods and documentating the methods, the methods I would have probably implemented are
 * hadle requests, handle prefix search and handle title fetch.
 * TODO B: I would have centralized the route handling using a switch case statement
 * based on parameters and defining separate methods for each route.
 * TODO C: I would have cached the results in get list of articles if the results got too bug
 * I would have used functions like stripos for better string matching. I would have also tried to 
 * remove loop filtering with array functions for better performance.
 * TODO D: I would have made sure to input validation and sanitization and prevented directory transversal
 * like I did in index.php and also set up content security policy for the header like I did in
 * index.php.
 */
