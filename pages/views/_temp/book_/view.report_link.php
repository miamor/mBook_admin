<h3>Báo cáo link hỏng</h3>
<div class="r-des">Chọn link báo lỗi</div>
<div class="r-select-link">
	<?php foreach ($bView['download'] as $k => $oneLink)
		echo '<label class="checkbox">
				<input type="checkbox" value="'.$k.'" name="link[]"/> Link '.($k+1).' <a href="'.$oneLink.'">'.$oneLink.'</a>
			</label>'; ?>
</div>