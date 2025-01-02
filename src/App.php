<?php

namespace App;

// TODO: Improve the readability of this file through refactoring and documentation.

require_once dirname( __DIR__ ) . '/globals.php';
/**
 * This app class handles saving, updating, fetchng information
 * and retrieving list of articles.
 */
class App {
	/**
	 * saves a article to a file.
	 * @param string $filepath file path of article.
	 * @param string $content content to save into article.
	 */
	public function save( $filePath, $content):void {
		error_log( "Saving article $filePath, success!" );
		file_put_contents( $filePath, $content);
	}

	/**
	 * updates existing article information.
	 * @param string $filepath file path of article.
	 * @param string $content content to save into article. 
	 */
	public function update( $filePath, $content ) {
		$this->save( $filePath, $content );
	}

	/**
	 * fetches content of article based on the get request.
	 * @param array parameter to find the article
	 * @return string/null content of article and null if not found.
	 */
	public function fetch( array $params ): ?string {
		$title = $params['title'] ?? $_GET['title'] ?? null;
		if (!$title) {
			error_log("No title provided for fetching the article.");
			return null;
		}

		$filePath = sprintf('articles/%s', $title);
		if (file_exists($filePath)) {
			return file_get_contents($filePath);
		} else {
			error_log("Article file not found: $filePath");
			return null;
		}
	}
	/**
	 * retrieves a list of articles
	 * @return list of articles
	 */
	public function getListOfArticles() {
		global $wgBaseArticlePath;
		$files = scandir($wgBaseArticlePath);
		return array_diff($files, ['.', '..', '.DS_Store']);
	}
}
