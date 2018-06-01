<?php
/*
 IM - Infrastructure Manager
 Copyright (C) 2011 - GRyCAP - Universitat Politecnica de Valencia

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

include_once('db.php');
$im_use_rest=false;
if (getenv('im_use_rest')) {
	$im_use_rest = filter_var(getenv('im_use_rest'), FILTER_VALIDATE_BOOLEAN);
}
$im_use_ssl=false;
if (getenv('im_use_ssl')) {
	$im_use_ssl = filter_var(getenv('im_use_ssl'), FILTER_VALIDATE_BOOLEAN);
}
$im_host="im";
if (getenv('im_host')) {
	$im_host = getenv('im_host');
}
$im_port=8899;
if (getenv('im_port')) {
	$im_port = intval(getenv('im_port'));
}
$im_method='http';
if (getenv('im_method')) {
	$im_method = intval(getenv('im_method'));
}
$im_db="/var/www/www-data/im.db";
if (getenv('im_db')) {
	$im_db = getenv('im_db');
}
# To use that feature the IM recipes file must accesible to the web server
#$recipes_db="/usr/local/im/contextualization/recipes_ansible.db";
# If not set ""
$recipes_db="";

# OpenID Issuer supported use "" to disable OpenID support
#$openid_issuer="https://iam-test.indigo-datacloud.eu/";
$openid_issuer="";
if (getenv('openid_issuer')) {
	$openid_issuer = getenv('openid_issuer');
}
# OpenID Issuer name
$openid_name="";
if (getenv('openid_name')) {
	$openid_name = getenv('openid_name');
}
# OpenID Client data
$CLIENT_ID = '';
if (getenv('client_id')) {
	$CLIENT_ID = getenv('client_id');
}
$CLIENT_SECRET = '';
if (getenv('client_secret')) {
	$CLIENT_SECRET = getenv('client_secret');
}
$REDIRECT_URI = 'https://server.com/im-web/openid_auth.php';
if (getenv('redirect_uri')) {
	$REDIRECT_URI = getenv('redirect_uri');
}
?>
