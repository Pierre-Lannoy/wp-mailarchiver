<?php
/**
 * Autoload for MailArchiver.
 *
 * @package Bootstrap
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   1.0.0
 */

spl_autoload_register(
	function ( $class ) {
		$classname = $class;
		$filepath  = __DIR__ . '/';
		if ( strpos( $classname, 'Decalog\\' ) === 0 ) {
			while ( strpos( $classname, '\\' ) !== false ) {
				$classname = substr( $classname, strpos( $classname, '\\' ) + 1, 1000 );
			}
			$filename = 'class-' . str_replace( '_', '-', strtolower( $classname ) ) . '.php';
			if ( strpos( $class, 'Decalog\System\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'system/';
			} elseif ( strpos( $class, 'Decalog\Plugin\Feature\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'features/';
			} elseif ( strpos( $class, 'Decalog\Plugin\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'plugin/';
			} elseif ( strpos( $class, 'Decalog\Processor\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'processors/';
			} elseif ( strpos( $class, 'Decalog\Handler\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'handlers/';
			} elseif ( strpos( $class, 'Decalog\Formatter\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'formatters/';
			} elseif ( strpos( $class, 'Decalog\Listener\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'listeners/';
			} elseif ( strpos( $class, 'Decalog\Library\\' ) === 0 ) {
				$filepath = MAILARCHIVER_VENDOR_DIR;
			} elseif ( strpos( $class, 'Decalog\Integration\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'integrations/';
			} elseif ( strpos( $class, 'Decalog\API\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'api/';
			} elseif ( strpos( $class, 'Decalog\\' ) === 0 ) {
				$filepath = MAILARCHIVER_INCLUDES_DIR . 'api/';
			}
			if ( strpos( $filename, '-public' ) !== false ) {
				$filepath = MAILARCHIVER_PUBLIC_DIR;
			}
			if ( strpos( $filename, '-admin' ) !== false ) {
				$filepath = MAILARCHIVER_ADMIN_DIR;
			}
			$file = $filepath . $filename;
			if ( file_exists( $file ) ) {
				include_once $file;
			}
		}
	}
);
