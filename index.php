<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

define('DIR', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DIR);

define('FACEBOOK_PAGE_ID', 'Facebook sayfa id bilgisi');
define('FACEBOOK_PAGE_ACCESS_TOKEN', 'Sayfaya ait "leads_retrieval" ve "ads_read" yetkileri olan bir token');

require_once ROOT.'vendor'.DIR.'autoload.php';
require_once ROOT.'Facebook.php';

$facebook = new Facebook();

/**
 * Sayfaya ait formları çekmek için;
 * Varsayılan olarak json format döner. Eğer nesne olarak dönmesini istiyorsanız: getForms('object') diyebilirsiniz.
 */
/*$forms = $facebook->getForms();
echo '<pre>';
print_r($forms);
echo '</pre>';*/

/**
 * Bir forma ait başvuruları çekmek için;
 * Varsayılan olarak json format döner. Eğer nesne olarak dönmesini istiyorsanız: getLeads('form_id', 'object') diyebilirsiniz.
 */
$leads = $facebook->getLeads('form id bilgisi');
echo '<pre>';
print_r($leads);
echo '</pre>';