<?php
/**
 * @package zapier
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/zapiersubscriptions.class.php');
class ZapierSubscriptions_mysql extends ZapierSubscriptions {}
?>