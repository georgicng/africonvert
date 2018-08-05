<?php

class Schedule_Email extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'send_email';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		if (empty($item['attempt'])){
			$item['attempt'] = 1;
		} else {
			$item['attempt'] += 1;
		}
		
		$res = sendMail($item['meta'], $item['template'], $item['data']);
		if ($res === false && $item['attempt'] <= 5){
			return $item;
		} elseif ($res === false && $item['attempt'] > 5) {
			$emails = get_option( 'gai_failed_emails' );
			if ( $emails  !== false ) {
				$emails[] = $item;
				// The option already exists, so we just update it.
				update_option( 'gai_failed_emails', $emails);
				
			} else {
				$emails[] = $item;
				update_option( 'gai_failed_emails', $emails, "no" );
			}
			return false;
		} else {
			return false;
		}
		  
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}