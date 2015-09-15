<!--  Form Buttons-->
<div class="step-controls">
    <?php if ($step->status == MigrateSteps::STATUS_NOT_DONE): ?>
        <button type="submit" class="btn btn-primary"><?php echo Yii::t('frontend', 'Start'); ?></button>
    <?php else: ?>
        <a href="<?php echo Yii::app()->createUrl("migrate/reset/step/" . $step->sorder); ?>" class="btn btn-danger"><?php echo Yii::t('frontend', 'Reset'); ?></a>
        <?php if ($step->sorder <= 8): ?>
            <a href="<?php echo Yii::app()->createUrl("migrate/step" . ($step->sorder+1)); ?>" class="btn btn-primary"><?php echo Yii::t('frontend', 'Next Step'); ?></a>
        <?php endif; ?>
    <?php endif; ?>
</div>
<!--// Form Buttons-->