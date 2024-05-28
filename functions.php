<?php
/**
 * Global functions.
 *
 * @package Functions
 * @author  Pierre Lannoy <https://pierre.lannoy.fr/>.
 * @since   2.5.0
 */

if ( ! function_exists('decalog_get_psr_log_version') ) {
	/**
	 * Get the needed version of PSR-3.
	 *
	 * @return  int  The PSR-3 needed version.
	 * @since 4.0.0
	 */
	function decalog_get_psr_log_version() {
		$required = 1;
		if ( ! defined( 'DECALOG_PSR_LOG_VERSION') ) {
			define( 'DECALOG_PSR_LOG_VERSION', 'V1' );
		}
		switch ( strtolower( DECALOG_PSR_LOG_VERSION ) ) {
			case 'v3':
				$required = 3;
				break;
			case 'auto':
				if ( class_exists( '\Psr\Log\NullLogger') ) {
					$reflection = new \ReflectionMethod(\Psr\Log\NullLogger::class, 'log');
					foreach ( $reflection->getParameters() as $param ) {
						if ( 'message' === $param->getName() ) {
							if ( str_contains($param->getType() ?? '', '|') ) {
								$required = 3;
							}
						}
					}
				}
		}
		return $required;
	}
}

/**
 * Simulate an email, similar to PHP's mail function.
 *
 * It doesn't send mail!
 *
 * The default content type is `text/plain` which does not allow using HTML.
 * However, you can set the content type of the email by using the
 * {@see 'wp_mail_content_type'} filter.
 *
 * The default charset is based on the charset used on the blog. The charset can
 * be set using the {@see 'wp_mail_charset'} filter.
 *
 * @param string|string[] $to          Array or comma-separated list of email addresses to send message.
 * @param string          $subject     Email subject.
 * @param string          $message     Message contents.
 * @param string|string[] $headers     Optional. Additional headers.
 * @param string|string[] $attachments Optional. Paths to files to attach.
 * @return \PHPMailer\PHPMailer\PHPMailer   The instance ready to use.
 *
 * @since 2.5.0
 *
 */
function mailarchiver_wp_mail( $to, $subject, $message, $headers = '', $attachments = [] ) {
	// Compact the input, apply the filters, and extract them back out.

	/**
	 * Filters the wp_mail() arguments.
	 *
	 * @since 2.2.0
	 *
	 * @param array $args {
	 *     Array of the `wp_mail()` arguments.
	 *
	 *     @type string|string[] $to          Array or comma-separated list of email addresses to send message.
	 *     @type string          $subject     Email subject.
	 *     @type string          $message     Message contents.
	 *     @type string|string[] $headers     Additional headers.
	 *     @type string|string[] $attachments Paths to files to attach.
	 * }
	 */
	$atts = apply_filters( 'wp_mail', compact( 'to', 'subject', 'message', 'headers', 'attachments' ) );

	if ( isset( $atts['to'] ) ) {
		$to = $atts['to'];
	}

	if ( ! is_array( $to ) ) {
		$to = explode( ',', $to );
	}

	if ( isset( $atts['subject'] ) ) {
		$subject = $atts['subject'];
	}

	if ( isset( $atts['message'] ) ) {
		$message = $atts['message'];
	}

	if ( isset( $atts['headers'] ) ) {
		$headers = $atts['headers'];
	}

	if ( isset( $atts['attachments'] ) ) {
		$attachments = $atts['attachments'];
	}

	if ( ! is_array( $attachments ) ) {
		$attachments = explode( "\n", str_replace( "\r\n", "\n", $attachments ) );
	}

	$content_type = '';

	// Create the PHPMailer instance.
	require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
	require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
	require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
	$phpmailer             = new \PHPMailer\PHPMailer\PHPMailer( true );
	$phpmailer::$validator = static function ( $email ) {
		return (bool) is_email( $email );
	};

	// Headers.
	$cc       = array();
	$bcc      = array();
	$reply_to = array();

	if ( empty( $headers ) ) {
		$headers = array();
	} else {
		if ( ! is_array( $headers ) ) {
			// Explode the headers out, so this function can take
			// both string headers and an array of headers.
			$tempheaders = explode( "\n", str_replace( "\r\n", "\n", $headers ) );
		} else {
			$tempheaders = $headers;
		}
		$headers = array();

		// If it's actually got contents.
		if ( ! empty( $tempheaders ) ) {
			// Iterate through the raw headers.
			foreach ( (array) $tempheaders as $header ) {
				if ( strpos( $header, ':' ) === false ) {
					if ( false !== stripos( $header, 'boundary=' ) ) {
						$parts    = preg_split( '/boundary=/i', trim( $header ) );
						$boundary = trim( str_replace( array( "'", '"' ), '', $parts[1] ) );
					}
					continue;
				}
				// Explode them out.
				list( $name, $content ) = explode( ':', trim( $header ), 2 );

				// Cleanup crew.
				$name    = trim( $name );
				$content = trim( $content );

				switch ( strtolower( $name ) ) {
					// Mainly for legacy -- process a "From:" header if it's there.
					case 'from':
						$bracket_pos = strpos( $content, '<' );
						if ( false !== $bracket_pos ) {
							// Text before the bracketed email is the "From" name.
							if ( $bracket_pos > 0 ) {
								$from_name = substr( $content, 0, $bracket_pos - 1 );
								$from_name = str_replace( '"', '', $from_name );
								$from_name = trim( $from_name );
							}

							$from_email = substr( $content, $bracket_pos + 1 );
							$from_email = str_replace( '>', '', $from_email );
							$from_email = trim( $from_email );

							// Avoid setting an empty $from_email.
						} elseif ( '' !== trim( $content ) ) {
							$from_email = trim( $content );
						}
						break;
					case 'content-type':
						if ( strpos( $content, ';' ) !== false ) {
							list( $type, $charset_content ) = explode( ';', $content );
							$content_type                   = trim( $type );
							if ( false !== stripos( $charset_content, 'charset=' ) ) {
								$charset = trim( str_replace( array( 'charset=', '"' ), '', $charset_content ) );
							} elseif ( false !== stripos( $charset_content, 'boundary=' ) ) {
								$boundary = trim( str_replace( array( 'BOUNDARY=', 'boundary=', '"' ), '', $charset_content ) );
								$charset  = '';
							}

							// Avoid setting an empty $content_type.
						} elseif ( '' !== trim( $content ) ) {
							$content_type = trim( $content );
						}
						break;
					case 'cc':
						$cc = array_merge( (array) $cc, explode( ',', $content ) );
						break;
					case 'bcc':
						$bcc = array_merge( (array) $bcc, explode( ',', $content ) );
						break;
					case 'reply-to':
						$reply_to = array_merge( (array) $reply_to, explode( ',', $content ) );
						break;
					default:
						// Add it to our grand headers array.
						$headers[ trim( $name ) ] = trim( $content );
						break;
				}
			}
		}
	}

	// Empty out the values that may be set.
	$phpmailer->clearAllRecipients();
	$phpmailer->clearAttachments();
	$phpmailer->clearCustomHeaders();
	$phpmailer->clearReplyTos();

	// Set "From" name and email.

	// If we don't have a name from the input headers.
	if ( ! isset( $from_name ) ) {
		$from_name = 'WordPress';
	}

	/*
	 * If we don't have an email from the input headers, default to wordpress@$sitename
	 * Some hosts will block outgoing mail from this address if it doesn't exist,
	 * but there's no easy alternative. Defaulting to admin_email might appear to be
	 * another option, but some hosts may refuse to relay mail from an unknown domain.
	 * See https://core.trac.wordpress.org/ticket/5007.
	 */
	if ( ! isset( $from_email ) ) {
		// Get the site domain and get rid of www.
		$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}

		$from_email = 'wordpress@' . $sitename;
	}

	/**
	 * Filters the email address to send from.
	 *
	 * @since 2.2.0
	 *
	 * @param string $from_email Email address to send from.
	 */
	$from_email = apply_filters( 'wp_mail_from', $from_email );

	/**
	 * Filters the name to associate with the "from" email address.
	 *
	 * @since 2.3.0
	 *
	 * @param string $from_name Name associated with the "from" email address.
	 */
	$from_name = apply_filters( 'wp_mail_from_name', $from_name );

	if ( class_exists( 'PostmanOptions' ) ) {
		if ( \PostmanOptions::getInstance()->isPluginSenderEmailEnforced() ) {
			$from_email = \PostmanOptions::getInstance()->getMessageSenderEmail();
		}
		if ( \PostmanOptions::getInstance()->isPluginSenderNameEnforced() ) {
			$from_name = \PostmanOptions::getInstance()->getMessageSenderName();
		}
	}

	try {
		$phpmailer->setFrom( $from_email, $from_name, false );
	} catch ( \PHPMailer\PHPMailer\Exception $e ) {

	}

	// Set mail's subject and body.
	$phpmailer->Subject = $subject;
	if ( ( \PHPMailer\PHPMailer\PHPMailer::CONTENT_TYPE_MULTIPART_ALTERNATIVE === $content_type ) && ( false === stripos( $charset_content, 'boundary=' ) ) ) {
		$phpmailer->Body    = mailarchiver_get_body( $message );
		$phpmailer->AltBody = mailarchiver_get_altbody( $message );
	} else {
		$phpmailer->Body = $message;
	}

	// Set destination addresses, using appropriate methods for handling addresses.
	$address_headers = compact( 'to', 'cc', 'bcc', 'reply_to' );

	foreach ( $address_headers as $address_header => $addresses ) {
		if ( empty( $addresses ) ) {
			continue;
		}

		foreach ( (array) $addresses as $address ) {
			try {
				// Break $recipient into name and address parts if in the format "Foo <bar@baz.com>".
				$recipient_name = '';

				if ( preg_match( '/(.*)<(.+)>/', $address, $matches ) ) {
					if ( count( $matches ) == 3 ) {
						$recipient_name = $matches[1];
						$address        = $matches[2];
					}
				}

				switch ( $address_header ) {
					case 'to':
						$phpmailer->addAddress( $address, $recipient_name );
						break;
					case 'cc':
						$phpmailer->addCc( $address, $recipient_name );
						break;
					case 'bcc':
						$phpmailer->addBcc( $address, $recipient_name );
						break;
					case 'reply_to':
						$phpmailer->addReplyTo( $address, $recipient_name );
						break;
				}
			} catch ( \PHPMailer\PHPMailer\Exception $e ) {
				continue;
			}
		}
	}

	// Set to use PHP's mail().
	$phpmailer->isMail();

	// Set Content-Type and charset.

	// If we don't have a content-type from the input headers.
	if ( ! isset( $content_type ) ) {
		$content_type = 'text/plain';
	}

	/**
	 * Filters the wp_mail() content type.
	 *
	 * @since 2.3.0
	 *
	 * @param string $content_type Default wp_mail() content type.
	 */
	$content_type = apply_filters( 'wp_mail_content_type', $content_type );

	$phpmailer->ContentType = $content_type;

	// Set whether it's plaintext, depending on $content_type.
	if ( 'text/html' === $content_type ) {
		$phpmailer->isHTML( true );
	}

	// If we don't have a charset from the input headers.
	if ( ! isset( $charset ) ) {
		$charset = get_bloginfo( 'charset' );
	}

	/**
	 * Filters the default wp_mail() charset.
	 *
	 * @since 2.3.0
	 *
	 * @param string $charset Default email charset.
	 */
	$phpmailer->CharSet = apply_filters( 'wp_mail_charset', $charset );

	// Set custom headers.
	if ( ! empty( $headers ) ) {
		foreach ( (array) $headers as $name => $content ) {
			// Only add custom headers not added automatically by PHPMailer.
			if ( ! in_array( $name, array( 'MIME-Version', 'X-Mailer' ), true ) ) {
				try {
					$phpmailer->addCustomHeader( sprintf( '%1$s: %2$s', $name, $content ) );
				} catch ( \PHPMailer\PHPMailer\Exception $e ) {
					continue;
				}
			}
		}

		if ( false !== stripos( $content_type, 'multipart' ) && ! empty( $boundary ) ) {
			$phpmailer->addCustomHeader( sprintf( 'Content-Type: %s; boundary="%s"', $content_type, $boundary ) );
		}
	}

	if ( ! empty( $attachments ) ) {
		foreach ( $attachments as $attachment ) {
			try {
				$phpmailer->addAttachment( $attachment );
			} catch ( \PHPMailer\PHPMailer\Exception $e ) {
				continue;
			}
		}
	}

	return $phpmailer;
}

/**
 * Get the html body of an already formatted message.
 *
 * @param   string          $message     Message contents.
 * @return  string      The body.
 *
 * @since 2.5.0
 *
 */
function mailarchiver_get_body( $message ) {
	if ( preg_match( '/<!doctype.*<\/html>/uixs', $message, $matches ) ) {
		return $matches[0];
	}
	return '';
}

/**
 * Get the alternative body of an already formatted message.
 *
 * @param   string          $message     Message contents.
 * @return  string      The body.
 *
 * @since 2.5.0
 *
 */
function mailarchiver_get_altbody( $message ) {
	$message = preg_replace( '/<!doctype.*<\/html>/uixs', '', $message );
	$message = preg_replace( '/^.*: .*$\n|\r^$\n|\r/imu', '', $message );
	$message = preg_replace( '/^.*: .*$/imu', '', $message );
	return $message;
}

/**
 * Properly strips script tags including
 *
 * This differs from strip_tags() because it removes the contents of
 * the `<script>` and `<style>` tags. E.g. `strip_tags( '<script>something</script>' )`
 * will return 'something'. wp_strip_all_tags will return ''
 *
 * @since 2.11.0
 *
 * @param string $text          String containing HTML tags
 * @param bool   $remove_breaks Optional. Whether to remove left over line breaks and white space chars
 * @return string The processed string.
 */
function mailarchiver_strip_script_tags( $text, $remove_breaks = false ) {
	if ( is_null( $text ) ) {
		return '';
	}

	if ( ! is_scalar( $text ) ) {
		/*
		 * To maintain consistency with pre-PHP 8 error levels,
		 * trigger_error() is used to trigger an E_USER_WARNING,
		 * rather than _doing_it_wrong(), which triggers an E_USER_NOTICE.
		 */
		trigger_error(
			sprintf(
			/* translators: 1: The function name, 2: The argument number, 3: The argument name, 4: The expected type, 5: The provided type. */
				__( 'Warning: %1$s expects parameter %2$s (%3$s) to be a %4$s, %5$s given.' ),
				__FUNCTION__,
				'#1',
				'$text',
				'string',
				gettype( $text )
			),
			E_USER_WARNING
		);

		return '';
	}

	$text = preg_replace( '@<(script)[^>]*?>.*?</\\1>@si', '', $text );

	if ( $remove_breaks ) {
		$text = preg_replace( '/[\r\n\t ]+/', ' ', $text );
	}

	return trim( $text );
}
