<?php

namespace FormsVox\AI;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Batched Content Indexer & Ingestion Engine.
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

	public function get_progress() {
		return get_option( 'formsvox_ai_ingest_progress', array(
			'status'    => 'idle',
			'processed' => 0,
			'total'     => 0,
			'last_run'  => get_option( 'formsvox_ai_last_ingest', '' ),
		) );
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
			'posts_per_page' => 250,
		) );

		$chunks = array();
		foreach ( $posts as $p ) {
			$clean_content = wp_strip_all_tags( $p->post_content );
			$words         = explode( ' ', $clean_content );
			$word_count    = count( $words );

			if ( $word_count <= 500 ) {
				$chunks[] = array(
					'url'     => get_permalink( $p->ID ),
					'title'   => get_the_title( $p->ID ),
					'content' => $clean_content,
				);
			} else {
				// Split into ~500-word chunks
				$chunk_words = array_chunk( $words, 500 );
				foreach ( $chunk_words as $idx => $part ) {
					$chunks[] = array(
						'url'     => get_permalink( $p->ID ) . '#section-' . ( $idx + 1 ),
						'title'   => get_the_title( $p->ID ) . ' (Part ' . ( $idx + 1 ) . ')',
						'content' => implode( ' ', $part ),
					);
				}
			}
		}

		$total = count( $chunks );
		if ( 0 === $total ) {
			return 0;
		}

		update_option( 'formsvox_ai_ingest_progress', array(
			'status'    => 'indexing',
			'processed' => 0,
			'total'     => $total,
			'last_run'  => current_time( 'mysql' ),
		) );

		// Process in batches of 20
		$batch_size = 20;
		$batches    = array_chunk( $chunks, $batch_size );
		$processed  = 0;

		foreach ( $batches as $batch ) {
			$res = Client::request( '/v1/ingest', 'POST', array(
				'chunks'  => $batch,
				'product' => 'formsvox',
			) );

			if ( is_wp_error( $res ) ) {
				update_option( 'formsvox_ai_ingest_progress', array(
					'status'    => 'error',
					'processed' => $processed,
					'total'     => $total,
					'error'     => $res->get_error_message(),
				) );
				return false;
			}

			$processed += count( $batch );
			update_option( 'formsvox_ai_ingest_progress', array(
				'status'    => 'indexing',
				'processed' => $processed,
				'total'     => $total,
				'last_run'  => current_time( 'mysql' ),
			) );
		}

		update_option( 'formsvox_ai_ingest_progress', array(
			'status'    => 'completed',
			'processed' => $processed,
			'total'     => $total,
			'last_run'  => current_time( 'mysql' ),
		) );
		update_option( 'formsvox_ai_last_ingest', current_time( 'mysql' ) );

		return $processed;
	}
}
