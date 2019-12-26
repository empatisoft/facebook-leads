<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

define('DIR', DIRECTORY_SEPARATOR);
define('ROOT', $_SERVER['DOCUMENT_ROOT'].DIR);

require_once ROOT.'vendor'.DIR.'autoload.php';
require_once ROOT.'FacebookMultipleAccounts.php';

/**
 * Sayfa listesi
 * SayfaID => Token
 */
$accounts = array(
    'page_id' => 'token',
    'page_id_2' => 'token'
);

$facebook = new FacebookMultipleAccounts($accounts);

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
$leads = $facebook->getLeads('form_id', $accounts['page_id']);
echo '<pre>';
print_r($leads);
echo '</pre>';