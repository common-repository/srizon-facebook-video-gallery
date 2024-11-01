<?php

/**
 * Class SrizonFacebookVideo
 */
class SrizonFacebookVideo {
	/**
	 * @var array
	 */
	private $videos;
	/**
	 * @var \Facebook\Facebook
	 */
	private $fb;
	/**
	 * @var string
	 */
	private $cachepath;
	/**
	 * @var string
	 */
	private $cachepath1;
	/**
	 * @var string
	 */
	private $cachepath2;
	/**
	 * @var string
	 */
	private $endpoint;
	/**
	 * @var string
	 */
	private $sync_interval;

	public function __construct( $sync_interval ) {
		$this->videos        = array();
		$this->sync_interval = $sync_interval * 60; // convert minutes to seconds
		$this->setupCache();

	}


	/**
	 * @param string $id cache identifire (post id used)
	 * @param string $source_type valid options page/profile
	 * @param string $count total number of videos to get
	 * @param string $pageid specify
	 *
	 * @return array
	 */
	public function getVideos( $app_id, $app_secret, $app_token, $id, $source_type, $count, $pageid = '' ) {
		if ( ! $this->cacheRead( $id, $source_type, $pageid ) ) {
			try {
				$this->setupFB( $app_id, $app_secret, $app_token );
				$this->setFBEndpoint( $source_type, $pageid );
				$this->processFBRequests( $count );
				$this->cacheIt( $id, $source_type, $pageid );
			} catch ( Exception $e ) {
				//
			}
		}
		if ( ! count( $this->videos ) ) {
			$this->cacheReadBackup( $id, $source_type, $pageid );
		}

		return array_slice( $this->videos, 0, $count );
	}

	/**
	 * Just Check and Create Cache folder if possible
	 */
	private function setupCache() {
		$this->cachepath  = dirname( __FILE__ ) . '/../cache';
		$this->cachepath1 = dirname( __FILE__ ) . '/../cache/1';
		$this->cachepath2 = dirname( __FILE__ ) . '/../cache/2';
		if ( ! is_dir( $this->cachepath . '/1' ) ) {
			if ( is_writable( $this->cachepath ) ) {
				mkdir( $this->cachepath . '/1', 0777, true );
			}
		}
		if ( ! is_dir( $this->cachepath . '/2' ) ) {
			if ( is_writable( $this->cachepath ) ) {
				mkdir( $this->cachepath . '/2', 0777, true );
			}
		}
	}

	/**
	 * @param $app_id
	 * @param $app_secret
	 * @param $app_token
	 */
	private function setupFB( $app_id, $app_secret, $app_token ) {
		$this->fb = new \Facebook\Facebook( [
			'app_id'                => $app_id,
			'app_secret'            => $app_secret,
			'default_graph_version' => 'v2.5'
		] );
		$this->fb->setDefaultAccessToken( $app_token );
	}

	/**
	 * @param $source
	 * @param $pageid
	 */
	private function setFBEndpoint( $source, $pageid ) {
		if ( $source == 'page' ) {
			$this->endpoint = '/' . $pageid . '/videos';
		} else {
			$this->endpoint = '/me/videos/uploaded';
		}
//		$this->endpoint .= '?fields=id';
		$this->endpoint .= '?fields=id,title,description,picture';
	}

	/**
	 * @param $count
	 */
	private function processFBRequests( $count ) {
		$this->fb->get( $this->endpoint );
		$next = true;
		while ( count( $this->videos ) < $count and $next ) {
			$response     = $this->fb->getLastResponse();
			$this->videos = array_merge( $this->videos, $response->getDecodedBody()['data'] );
			$next         = $this->fb->next( $response->getGraphEdge() );
		}
	}

	/**
	 * @param string $id
	 * @param string $source_type
	 * @param string $pageid
	 * @param string $path
	 *
	 * @return string filename
	 */
	private function getCacheFileName( $id, $source_type, $pageid, $path ) {
		$filename_pre_md5 = $id . $source_type;
		if ( $source_type == 'page' ) {
			$filename_pre_md5 .= $pageid;
		}

		return $path . '/' . md5( $filename_pre_md5 );
	}

	/**
	 * @param $id
	 * @param $source_type
	 * @param $pageid
	 */
	private function cacheIt( $id, $source_type, $pageid ) {
		if ( ! count( $this->videos ) ) {
			return;
		}
		$json_data = json_encode( $this->videos );
		if ( is_writable( $this->cachepath1 ) ) {
			$filename1 = $this->getCacheFileName( $id, $source_type, $pageid, $this->cachepath1 );
			file_put_contents( $filename1, $json_data );
		}
		if ( is_writable( $this->cachepath2 ) ) {
			$filename2 = $this->getCacheFileName( $id, $source_type, $pageid, $this->cachepath2 );
			file_put_contents( $filename2, $json_data );
		}
	}

	/**
	 * Check if the cache is expired
	 *
	 * @param $id
	 * @param $source_type
	 * @param $pageid
	 *
	 * @return bool
	 */
	private function cacheExpired( $id, $source_type, $pageid ) {
		$filename = $this->getCacheFileName( $id, $source_type, $pageid, $this->cachepath1 );
		if ( is_file( $filename ) ) {
			$utime = filemtime( $filename );
			if ( ( time() - $utime ) > $this->sync_interval ) {
				return true;
			} else {
				return false;
			}
		}

		return true;
	}

	/**
	 * @param $id
	 * @param $source_type
	 * @param $pageid
	 *
	 * @return string
	 */
	private function cacheRead( $id, $source_type, $pageid ) {
		if ( $this->cacheExpired( $id, $source_type, $pageid ) ) {
			return false;
		}
		$filename = $this->getCacheFileName( $id, $source_type, $pageid, $this->cachepath1 );
		if ( is_file( $filename ) ) {
			$this->videos = json_decode( file_get_contents( $filename ) , true);

			return true;
		}

		return false;
	}

	/**
	 * @param $id
	 * @param $source_type
	 * @param $pageid
	 *
	 * @return string
	 */
	private function cacheReadBackup( $id, $source_type, $pageid ) {
		$filename = $this->getCacheFileName( $id, $source_type, $pageid, $this->cachepath2 );
		if ( is_file( $filename ) ) {
			$this->videos = json_decode( file_get_contents( $filename ) , true);

			return true;
		}

		return false;
	}

	/**
	 * @param $id
	 * @param $source_type
	 * @param $pageid
	 */
	public function cacheClean( $id, $source_type, $pageid ) {
		$filename1 = $this->getCacheFileName( $id, $source_type, $pageid, $this->cachepath1 );
		if ( is_file( $filename1 ) ) {
			unlink( $filename1 );
		}
	}
}