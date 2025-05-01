<?php

namespace fluXtore\Core\Admin\Providers;

use \AsyncAws\S3\S3Client;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Avoid defining the class twice.
if ( ! class_exists( 'fluXtoreS3Provider' ) ) {
	/**
	 * @property mixed $bucketName
	 */
	abstract class fluXtoreS3Provider extends S3Client {
		protected function __construct($configuration = []) {
			parent::__construct($configuration);
		}

		protected function _listFolders() {
			$response = $this->listObjectsV2( [
				'Bucket' => $this->bucketName
			] );

			foreach ( $response->getContents() as $value ) {
				// if equals zero means that is a folder
				if ( $value->getSize() === "0" ) {
					$result[] = $value->getKey();
				}
			}

			return $result ?? [];
		}

		protected function _listFiles( $template ) {
			$response = $this->listObjectsV2( [
				'Bucket' => $this->bucketName,
				'Prefix' => $template
			] );

			foreach ( $response->getContents() as $value ) {
				if ( string_ends_with( $value->getKey(), ".json" ) ) {
					$result[] = $value->getKey();
				}
			}

			return $result ?? [];
		}

		protected function _getContent( $key ) {
			$response = $this->getObject( [
				'Bucket' => $this->bucketName,
				'Key' => $key
			] );

			$result = (string) $response->getBody();
			return $result ?? "";
		}

		protected function _getPublicURI( $key ) {
			$uri = sprintf('/%s/%s', urlencode($this->bucketName), str_replace('%2F', '/', rawurlencode($key)));
			return $this->getEndpoint($uri, [], null);
		}
	}
}

