<h3 class="menu-title">Users</h3>

<div class="menu-one-box">
    <h4 class="menu-one-box-header">Users</h4>
    <div class="menu-one-box-body">
        <a class="menu-one-item" href="<?php echo $config->uLink ?>">List all</a>
        <a class="menu-one-item" href="<?php echo $config->uLink.'?mode=new' ?>">Add new</a>
    </div>
</div>

<div class="menu-one-box">
    <h4 class="menu-one-box-header">Coins</h4>
    <div class="menu-one-box-body">
        <a class="menu-one-item" href="<?php echo $config->uLink.'?type=coins&mode=history' ?>">History</a>
        <a class="menu-one-item" href="<?php echo $config->uLink.'?type=coins&mode=changecoin' ?>">Change coin</a>
    </div>
</div>

<div class="menu-one-box">
    <h4 class="menu-one-box-header">Membership</h4>
    <div class="menu-one-box-body">
        <a class="menu-one-item" href="<?php echo $config->bLink.'?type=storage&mode=donations' ?>">Coins</a>
        <a class="menu-one-item" href="<?php echo $config->uLink.'?type=history' ?>">History</a>
        <a class="menu-one-item" href="<?php echo $config->uLink.'?type=changecoin' ?>">Change coin</a>
    </div>
</div>
