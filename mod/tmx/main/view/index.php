<div id="menu">
	<div class="wrap">
	<? foreach ((tmx\Menu::getLeft()) as $k => $v): //don't insert whites
		?><a href="<?= $k ?>"<?= $k == $subpage ? ' id="sel"' : '' ?>><?= $v['name'] ?></a><?
	endforeach; ?>
	</div>
<? foreach (tmx\Menu::getRight() as $k => $v): ?>
	<a href="<?= $k ?>"<?= $k == $subpage ? ' id="sel"' : '' ?> class="button"><?= $v['name'] ?></a>
<? endforeach; ?>
</div>
<? if ($view_content): ?>
<div class="clear"></div>
<div class="content">
	<?= $view_content ?>
</div>
<? endif; ?>
