<h3 class="menu-title">Books</h3>

<div class="menu-one-box">
    <h4 class="menu-one-box-header">Book</h4>
    <div class="menu-one-box-body">
        <a class="menu-one-item" href="<?php echo $config->bLink ?>">List all</a>
        <a class="menu-one-item" href="<?php echo $config->bLink.'?mode=new' ?>">Add new</a>
    </div>
</div>

<div class="menu-one-box">
    <h4 class="menu-one-box-header">Storage</h4>
    <div class="menu-one-box-body">
        <a class="menu-one-item" href="<?php echo $config->bLink.'?type=storage&mode=donations' ?>">Donations</a>
        <a class="menu-one-item" href="<?php echo $config->bLink.'?type=storage&mode=add' ?>">Add new donation</a>
        <a class="menu-one-item" href="<?php echo $config->bLink.'?type=borrow' ?>">Borrow list</a>
        <a class="menu-one-item" href="<?php echo $config->bLink.'?type=borrow&mode=add' ?>">Add new borrow request</a>
    </div>
</div>
