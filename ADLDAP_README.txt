Using OpenEMR with Active Directory or LDAP authentication is not hard.

One thing to note is that you will need configure user permissions for 
all users in the Database. The only exception to that you can define
1 or more admin users to have permissions from the ad_sync script, 
this prevents lockouts and allows initial configuration.

Basically there are 3 steps to add Active Directory or another LDAP
server for authentication to an already configured system.

1.) In sites/<your-site-name>/config.php 
        set $GLOBALS['oer_config']['authProvider'] = 'adldap';

2.) Configure your LDAP server settings in: 
        sites/<your-site-name>/adldap_conf.php
        
3.) Run library/authentication/adldap_sync.php

NOTES:
In order to use SSL or TLS with Active Directory, you have to have an
X509 certificate installed. This can be done with CA services. Setting
this up is beyond the scope of this README, but you should be able to
find lots of information on the Internet about it.