<?php
# CONSTANTS DEFINED HERE


# SETTINGS DIFFERENT FOR EACH TOURNAMENT

# Change this to show new tournament as shown on the main screen (otherwise past)
# tournament games will be displayed
define('DEFAULT_TOURNAMENT', 3);

# agents.php -> specify place UTC time +04:00, +02:00 etc

# END OF SETTINGS DIFFERENT FOR EACH TOURNAMENT


# OTHER CONSTANTS

define('MAX_REGISTRATIONS_PER_DAY', 10);

define('ADMIN_USER_ID', 1);
define('ADMIN_EMAIL', 'admin@example.com');  # TODO: env variable?

define('MANAGERS', serialize(array(2, 3)));
define('TEAM_MEMBERS', serialize(array(1, 2, 3, 14, 16, 22)));

define('MSG_USER_ID', -1);

define('STUPID_BOT_USER_ID', 6);
define('FIFA_BOT_USER_ID', 17);
define('SMART_BOT_USER_ID', 7);
define('NAME_BOT_USER_ID', 7);

define('MAX_GROUPS_TO_OWN', 5);

define('HOST', 'mysql');
define('DB', 'main');
define('USERNAME', 'main');
define('PW', '8f147a9619094d86');
define('WHATEVER', '9sduf98#o1@@23sdf24j32');
