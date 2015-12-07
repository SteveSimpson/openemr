<?php
/* Change the settings to suit your Active Directory installation
 * 
 * account_suffix : this is the full domain name of your Active Directory
 * base_dn: Users is the standard windows CN, replace the DC stuff with your domain
 * domain_controllers: the IP address of your domain controller(s)
 * ad_username: a username with simple 'bind' access to Active Directory no special permissions needed
 * ad_password: the password for the user
 * real_primarygroup: leave alone or read adldap.sourceforge.net docs
 * use_ssl: leave alone or read adldap.sourceforge.net or https://github.com/adLDAP2/adLDAP2 docs
 * use_tls: leave alone or read adldap.sourceforge.net or https://github.com/adLDAP2/adLDAP2 docs
 * recursive_groups: leave alone or read adldap.sourceforge.net docs
 * 
 * adldap_require_group: put all users in this group that you want to have OpenEMR accounts
 * adldap_excluded_users: These users will never be added OpenEMR
 * adldap_admin_users: These users will be granted full administration privileges
 */
$adldap_options = array (
	"account_suffix" => "@mydomain.local",
	"base_dn" => "DC=mydomain,DC=local",
	"domain_controllers" => array ("192.168.1.2","192.168.1.3"),
	"ad_username" => "openemruser",
	"ad_password" => "secret",
	"real_primarygroup" => false,
	"use_tls" => true,
	"use_ssl" => false,
	"recursive_groups" => false,
);
$adldap_require_group="openemr";
$adldap_excluded_users=array("Administrator", "SQLServer", "SQLDebugger","TsInternetUser");
$adldap_admin_users=array("my_admin_account");