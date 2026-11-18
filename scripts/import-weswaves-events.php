<?php
// Import upcoming events from a WesWAVES JSON export as drafts.
//   wp eval-file scripts/import-weswaves-events.php /path/to/ep-events.json
// Past events and events already imported from another source are skipped.
$file   = isset( $args[0] ) ? $args[0] : '';
$events = $file && is_readable( $file ) ? json_decode( (string) file_get_contents( $file ), true ) : null;
if ( ! is_array( $events ) ) {
	fwrite( STDERR, "Pass the path of a readable WesWAVES JSON export.\n" );
	exit( 1 );
}
foreach ( WesSci_Event_Sync::import_weswaves( $events ) as $label => $count ) {
	echo "$label: $count\n";
}
