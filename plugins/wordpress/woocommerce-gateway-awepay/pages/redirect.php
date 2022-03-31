<?php

$wp_did_header = true;
require_once dirname(__FILE__) . '/../../../../wp-load.php';

$order = get_post_meta($_GET['id'], '_awepay_redirect_id', true);
if ($order->status == 'REQ' || $order->status == 'UREQ') {
  $html = urldecode($order->required->embedhtml);
} elseif ($order->status == 'REDIRECT') {
  $html = urldecode($order->redirect->html);
}
$html = str_replace('<iframe', '<!--', $html);
$html = str_replace('</iframe>', '-->', $html);
$html = str_replace('target=', 'title=', $html);
echo $html;