<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/prerequisites.inc.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/triggers.admin.inc.php';

if (isset($_SESSION['mailcow_cc_role']) && $_SESSION['mailcow_cc_role'] == 'domainadmin') {
  header('Location: /domainadmin/mailbox');
  exit();
}
elseif (isset($_SESSION['mailcow_cc_role']) && $_SESSION['mailcow_cc_role'] == 'user') {
  header('Location: /user');
  exit();
}
elseif (!isset($_SESSION['mailcow_cc_role']) || $_SESSION['mailcow_cc_role'] != "admin") {
  header('Location: /admin');
  exit();
}

require_once $_SERVER['DOCUMENT_ROOT'] .  '/inc/header.inc.php';
$_SESSION['return_to'] = $_SERVER['REQUEST_URI'];



$js_minifier->add('/web/js/site/mailbox.js');
$js_minifier->add('/web/js/presets/sieveMailbox.js');
$js_minifier->add('/web/js/site/pwgen.js');

$role = "admin";
$is_dual = (!empty($_SESSION["dual-login"]["username"])) ? 'true' : 'false';
$allow_admin_email_login = (preg_match("/^([yY][eE][sS]|[yY])+$/", $_ENV["ALLOW_ADMIN_EMAIL_LOGIN"])) ? 'true' : 'false';

// domains
$domains = mailbox('get', 'domains');

// mailboxes
$mailboxes = [];
foreach ($domains as $domain) {
  foreach (mailbox('get', 'mailboxes', $domain) as $mailbox) {
    $mailboxes[] = $mailbox;
  }
}

$template = 'mailbox.twig';
$template_data = [
  'acl' => $_SESSION['acl'],
  'acl_json' => json_encode($_SESSION['acl']),
  'role' => $role,
  'is_dual' => $is_dual,
  'allow_admin_email_login' => $allow_admin_email_login,
  'global_filters' => mailbox('get', 'global_filter_details'),
  'domains' => $domains,
  'mailboxes' => $mailboxes,
  'lang_mailbox' => json_encode($lang['mailbox']),
  'lang_rl' => json_encode($lang['ratelimit']),
  'lang_edit' => json_encode($lang['edit']),
  'lang_datatables' => json_encode($lang['datatables']),
];

require_once $_SERVER['DOCUMENT_ROOT'] . '/inc/footer.inc.php';
