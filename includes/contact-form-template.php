<?php

class WPCF7_ContactFormTemplate {

	public static function get_default( $prop = 'form' ) {
		if ( 'form' == $prop ) {
			$template = self::form();
		} elseif ( 'mail' == $prop ) {
			$template = self::mail();
		} elseif ( 'mail_2' == $prop ) {
			$template = self::mail_2();
		} elseif ( 'messages' == $prop ) {
			$template = self::messages();
		} else {
			$template = null;
		}

		return apply_filters( 'wpcf7_default_template', $template, $prop );
	}

	public static function form() {
		$template = sprintf(
			'
<label> %2$s
    [text* your-name] </label>

<label> %3$s
    [email* your-email] </label>

<label> %4$s
    [text* your-subject] </label>

<label> %5$s %1$s
    [textarea your-message] </label>

[submit "%6$s"]',
			__( '(optional)', 'lc_studio_contact_form' ),
			__( 'Your name', 'lc_studio_contact_form' ),
			__( 'Your email', 'lc_studio_contact_form' ),
			__( 'Subject', 'lc_studio_contact_form' ),
			__( 'Your message', 'lc_studio_contact_form' ),
			__( 'Submit', 'lc_studio_contact_form' )
		);

		return trim( $template );
	}

	public static function mail() {
		$template = array(
			'subject' => sprintf(
				/* translators: 1: blog name, 2: [your-subject] */
				_x( '%1$s "%2$s"', 'mail subject', 'lc_studio_contact_form' ),
				'[_site_title]',
				'[your-subject]'
			),
			'sender' => sprintf(
				'%s <%s>',
				'[_site_title]',
				self::from_email()
			),
			'body' =>
				sprintf(
					/* translators: %s: [your-name] <[your-email]> */
					__( 'From: %s', 'lc_studio_contact_form' ),
					'[your-name] <[your-email]>'
				) . "\n"
				. sprintf(
					/* translators: %s: [your-subject] */
					__( 'Subject: %s', 'lc_studio_contact_form' ),
					'[your-subject]'
				) . "\n\n"
				. __( 'Message Body:', 'lc_studio_contact_form' )
				. "\n" . '[your-message]' . "\n\n"
				. '-- ' . "\n"
				. sprintf(
					/* translators: 1: blog name, 2: blog URL */
					__( 'This e-mail was sent from a contact form on %1$s (%2$s)', 'lc_studio_contact_form' ),
					'[_site_title]',
					'[_site_url]'
				),
			'recipient' => '[_site_admin_email]',
			'additional_headers' => 'Reply-To: [your-email]',
			'attachments' => '',
			'use_html' => 0,
			'exclude_blank' => 0,
		);

		return $template;
	}

	public static function mail_2() {
		$template = array(
			'active' => false,
			'subject' => sprintf(
				/* translators: 1: blog name, 2: [your-subject] */
				_x( '%1$s "%2$s"', 'mail subject', 'lc_studio_contact_form' ),
				'[_site_title]',
				'[your-subject]'
			),
			'sender' => sprintf(
				'%s <%s>',
				'[_site_title]',
				self::from_email()
			),
			'body' =>
				__( 'Message Body:', 'lc_studio_contact_form' )
				. "\n" . '[your-message]' . "\n\n"
				. '-- ' . "\n"
				. sprintf(
					/* translators: 1: blog name, 2: blog URL */
					__( 'This e-mail was sent from a contact form on %1$s (%2$s)', 'lc_studio_contact_form' ),
					'[_site_title]',
					'[_site_url]'
				),
			'recipient' => '[your-email]',
			'additional_headers' => sprintf(
				'Reply-To: %s',
				'[_site_admin_email]'
			),
			'attachments' => '',
			'use_html' => 0,
			'exclude_blank' => 0,
		);

		return $template;
	}

	public static function from_email() {
		$admin_email = get_option( 'admin_email' );

		if ( wpcf7_is_localhost() ) {
			return $admin_email;
		}

		$sitename = wp_parse_url( network_home_url(), PHP_URL_HOST );
		$sitename = strtolower( $sitename );

		if ( 'www.' === substr( $sitename, 0, 4 ) ) {
			$sitename = substr( $sitename, 4 );
		}

		if ( strpbrk( $admin_email, '@' ) === '@' . $sitename ) {
			return $admin_email;
		}

		return 'wordpress@' . $sitename;
	}

	public static function messages() {
		$messages = array();

		foreach ( wpcf7_messages() as $key => $arr ) {
			$messages[$key] = $arr['default'];
		}

		return $messages;
	}
}

function wpcf7_messages() {
	$messages = array(
		'mail_sent_ok' => array(
			'description'
				=> __( "Sender's message was sent successfully", 'lc_studio_contact_form' ),
			'default'
				=> __( "Thank you for your message. It has been sent.", 'lc_studio_contact_form' ),
		),

		'mail_sent_ng' => array(
			'description'
				=> __( "Sender's message failed to send", 'lc_studio_contact_form' ),
			'default'
				=> __( "There was an error trying to send your message. Please try again later.", 'lc_studio_contact_form' ),
		),

		'validation_error' => array(
			'description'
				=> __( "Validation errors occurred", 'lc_studio_contact_form' ),
			'default'
				=> __( "One or more fields have an error. Please check and try again.", 'lc_studio_contact_form' ),
		),

		'spam' => array(
			'description'
				=> __( "Submission was referred to as spam", 'lc_studio_contact_form' ),
			'default'
				=> __( "There was an error trying to send your message. Please try again later.", 'lc_studio_contact_form' ),
		),

		'accept_terms' => array(
			'description'
				=> __( "There are terms that the sender must accept", 'lc_studio_contact_form' ),
			'default'
				=> __( "You must accept the terms and conditions before sending your message.", 'lc_studio_contact_form' ),
		),

		'invalid_required' => array(
			'description'
				=> __( "There is a field that the sender must fill in", 'lc_studio_contact_form' ),
			'default'
				=> __( "Please fill out this field.", 'lc_studio_contact_form' ),
		),

		'invalid_too_long' => array(
			'description'
				=> __( "There is a field with input that is longer than the maximum allowed length", 'lc_studio_contact_form' ),
			'default'
				=> __( "This field has a too long input.", 'lc_studio_contact_form' ),
		),

		'invalid_too_short' => array(
			'description'
				=> __( "There is a field with input that is shorter than the minimum allowed length", 'lc_studio_contact_form' ),
			'default'
				=> __( "This field has a too short input.", 'lc_studio_contact_form' ),
		),
	);

	return apply_filters( 'wpcf7_messages', $messages );
}
