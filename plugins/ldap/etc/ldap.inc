<?php

// LDAP server(s) to query for more information on Tuleap users and 
// for authentication.
// You may use a comma-separated list if there are several servers available
// (leave blank to disable LDAP lookup). 
// To specify secure LDAP servers, use 'ldaps://servername' 
$sys_ldap_server = '%sys_ldap_server%';

// To enable LDAP information on Tuleap users, also define the DN 
// (distinguised name) to use in LDAP queries.
// The ldap filter is the filter to use to query the LDAP directory
// (%name% are substituted with the value from the user table)
$sys_ldap_dn     = 'dc=tuleap,dc=local';

// For LDAP systems that do not accept anonymous binding, define here
// a valid DN and password:
//$sys_ldap_bind_dn = "eduid=1234,ou=people,dc=tuleap,dc=com";
//$sys_ldap_bind_passwd = "xxxxxxxxx";

// LDAP authentication:
// Tuleap only supports authentication with a attempt to bind with LDAP server
// with a DN and a password.
// As the DN is usually long (eduid=1234,ou=people,dc=tuleap,dc=com) people 
// usually authenticate themself with a login. So we need to first look 
// for the DN that correspond to the given login and once found attempt to bind
// with the given password. 
// In order to autenticate successfully users you need to properly 

// User login (authentication 1st step)
$sys_ldap_uid   = 'uid';

// User unique identifier. It's probably not the uid (or login) because it
// may change. This is a value that never change whatever happens to the
// user (even after deletion). It correspond to ldap_id field in user table
// in database.
// (authentication 2st step)
$sys_ldap_eduid = 'employeeNumber';

// User common name   
$sys_ldap_cn    = 'cn';
// User email address
$sys_ldap_mail  = 'mail';

// Specific DN to look for people, used for user authentication.
// You may use more than one DN separated by ; if you want to use several branches.
// Example : 'ou=People, dc=st, dc=com ; ou=Extranet, dc=st, dc=com'
$sys_ldap_people_dn     = 'ou=people,dc=tuleap,dc=local';

// Filter used to look for user (autocomplete, daily sync between Tuleap and LDAP...) It should cover a wide selection of
// fields because it's aim to find a user whatever it's given (email, name,
// login, etc).
$sys_ldap_search_user='(|(uid=%words%)(cn=%words%)(mail=%words%))';

// By default tooltip search is using ($sys_ldap_cn=%words%*) search filter (Hardcoded)
// You can change for a more sophisticated search 
// $sys_ldap_tooltip_search_user='(&(|(sn=%words%*)(givenName=%words%*)(uid=%words%*))(!(givenName=BoiteVocale))(uid=*))';

// By default tooltip search attrs are $sys_ldap_cn and $sys_ldap_uid (Hardcoded)
// You can choose the attributes the search will retrieve
// $sys_ldap_tooltip_search_attrs='uid;sn;givenName';

// On account creation, what it the default user status (A, R, ...)
$sys_ldap_default_user_status = 'A';

// Enable LDAP based authentication for SVN by default for new projects 
$sys_ldap_svn_auth = 1;

// Enable LDAP daily synchronization
$sys_ldap_daily_sync = 1;

// This Period of retention (in days) added to the date on which the LDAP daily synchronization
// was ran in order to compute the forecast date of the cleanup of suspended users.
$sys_ldap_daily_sync_retention_period = '';

// Enable usage of LDAP for people management. For instance autocompletion on
// user list, automatic creation of users not already referenced in the forge.
$sys_ldap_user_management = 1;

// Enable ldap group management.
// This allows to mirror a LDAP group defined in LDAP directory within the forge
// Doesn't work yet with only works with OpenLDAP compatible directories yet.
$sys_ldap_grp_enabled   = 1;

// Specific DN where the user groups are
$sys_ldap_grp_dn        = 'ou=groups,dc=tuleap,dc=local';
// Field that reprsent group name
$sys_ldap_grp_cn        = 'cn';
// Field that indicate the membership of a user in a group
$sys_ldap_grp_member    = 'uniquemember';
// Threshold for users to be suspended
// On beyond of this value expressed in percentage no users will be suspended
$sys_ldap_threshold_users_suspension = '';
// Search depth for daily sync
// 'onelevel': search for users only into the level immediately below $sys_ldap_people_dn
// 'subtree' : search for users into all $sys_ldap_people_dn subtrees (default)
$search_depth = 'subtree';


//
// Write user accounts into a LDAP server
// /!\ DO NOT USE IT - EXPERIMENTAL /!\

// A server on which you can write so users are exported into it
/*
$sys_ldap_write_server    = '';

$sys_ldap_write_dn        = 'cn=Manager,dc=tuleap,dc=local';

$sys_ldap_write_password  = 'welcome0';

$sys_ldap_write_people_dn = 'ou=people,dc=tuleap,dc=local';

$sys_ldap_write_group_dn  = 'ou=groups,dc=tuleap,dc=local';
*/