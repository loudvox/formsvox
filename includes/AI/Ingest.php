<?php

namespace FormsVox\AI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Content Indexer & Ingestion Manager.
 */
class Ingest {
	private static $instance = null;

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'formsvox_daily_ingest_cron', array( $this, 'sync_content' ) );
		if ( ! wp_next_scheduled( 'formsvox_daily_ingest_cron' ) ) {
			wp_schedule_event( time(), 'daily', 'formsvox_daily_ingest_cron' );
		}
	}

	public function sync_content() {
		if ( ! Connection::is_connected() ) {
			return false;
		}

		$post_types = array( 'page', 'post' );
		if ( class_exists( 'WooCommerce' ) ) {
			$post_types[] = 'product';
		}

		$posts = get_posts( array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => 100,
		) );

		$chunks = array();
		foreach ( $posts as $p ) {
			$chunks[] = array(
				'url'     => get_permalink( $p->ID ),
				'title'   => get_the_title( $p->ID ),
				'content' => wp_strip_all_tags( $p->post_content ),
			);
		}

		if ( empty( $chunks ) ) {
			return true;
		}

		$res = Client::request( '/v1/ingest', 'POST', array(
			'chunks'  => $chunks,
			'product' => 'formsvox',
		) );

		if ( ! is_wp_error( $res ) ) {
			update_option( 'formsvox_ai_last_ingest', current_time( 'mysql' ) );
			return count( $chunks );
		}

		return false;
	}
}
