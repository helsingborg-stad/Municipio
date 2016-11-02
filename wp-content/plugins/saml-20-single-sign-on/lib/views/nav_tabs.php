<?php
$tab = $_GET['page'];
?>

<link rel="stylesheet" href="<?php echo constant('SAMLAUTH_URL') . '/lib/css/sso.css';?>" />
<link href="//netdna.bootstrapcdn.com/font-awesome/3.1.1/css/font-awesome.css" rel="stylesheet">
<div class="wrap">
  <h1>Single Sign-On</h1>
  <h2 class="nav-tab-wrapper">
    <?php if(!is_network_admin()): ?>
      <a href="?page=sso_general.php" class="nav-tab<?php if($tab == 'sso_general.php'){echo ' nav-tab-active';}?>">General <span class="badge badge-important" id="sso_errors"><?php if($status->num_errors != 0) echo $status->num_errors; ?></span></a>
    <?php endif; ?>

    <?php if(!is_multisite() || is_network_admin()): ?>
      <a href="?page=sso_idp.php" class="nav-tab<?php if($tab == 'sso_idp.php'){echo ' nav-tab-active';}?>">Identity Provider</a>
    <?php endif; ?>

    <?php if(!is_network_admin()): ?>
      <a href="?page=sso_sp.php" class="nav-tab<?php if($tab == 'sso_sp.php'){echo ' nav-tab-active';}?>">Service Provider</a>
    <?php endif; ?>
    <a href="?page=sso_help.php" class="nav-tab<?php if($tab == 'sso_help.php'){echo ' nav-tab-active';}?>">Help</a>
  </h2>
</div>
