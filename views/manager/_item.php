<div class="thumbnail">
    <?php
    $sFileExtension = pathinfo($model->fileName, PATHINFO_EXTENSION);
    ?>
    <?php if(!in_array($sFileExtension, ['jpg', 'jpeg', 'png', 'gif', 'svg'])): ?>
        <div style="text-align: center;height: 80px;width: 100%;display:flex;align-items: center;justify-content: center;"><?= strtoupper($sFileExtension); ?></div>
    <?php else: ?>
        <img src="<?=\Yii::$app->imagemanager->getImagePath($model->id, 300, 300)?>" alt="<?=$model->fileName?>">
    <?php endif; ?>
    <?php
    $name = $model->fileName;
    if($model->alt){
        $name = $model->alt . " (" . $model->fileName . ")";
    }
    ?>
    <div class="filename" title="<?= $name; ?>"><?=$name?></div>
</div>
