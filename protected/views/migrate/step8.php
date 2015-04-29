<?php $this->pageTitle = $step->title . ' - ' . Yii::app()->name; ?>

<h1 class="page-header"> Step <?=$step->sorder?>: <?=$step->title?> </h1>

<?php
$migrated_object_ids = isset(Yii::app()->session['migrated_object_ids']) ? Yii::app()->session['migrated_object_ids'] : array();
?>

<form role="form" method="post" action="<?php echo Yii::app()->createUrl("migrate/step{$step->sorder}"); ?>">
    <div id="step-content">
        <blockquote> <p class="tip"> <?php echo Yii::t('frontend', $step->descriptions); ?> </p> </blockquote>

        <!--  Form Buttons-->
        <div class="step-controls">
            <?php if ($step->status == MigrateSteps::STATUS_NOT_DONE): ?>
                <button type="submit" class="btn btn-primary"><?php echo Yii::t('frontend', 'Start'); ?></button>
            <?php else: ?>
                <input type="hidden" id="reset" name="reset" value="0" />
                <button type="submit" class="btn btn-danger reset"><?php echo Yii::t('frontend', 'Reset'); ?></button>
<!--                <a href="--><?php //echo Yii::app()->createUrl("migrate/step" . ++$step->sorder); ?><!--" class="btn btn-primary">--><?php //echo Yii::t('frontend', 'Next Step'); ?><!--</a>-->
            <?php endif; ?>
        </div>
        <!--// Form Buttons-->

        <ul class="list-group">
            <li class="list-group-item">
                <h3 class="list-group-item-heading">
                    <input type="checkbox" id="select-all" style="visibility: hidden;" name="select_all_object" title="<?php echo Yii::t('frontend', 'Click here to select all.')?>" />
                    <?php echo Yii::t('frontend', 'Reviews and Ratings Data'); ?>
                </h3>
                <?php if (isset($objects) && $objects): ?>
                <ul class="list-group">
                    <?php foreach ($objects as $id => $label): ?>
                        <li class="list-group-item">
                            <h4 class="list-group-item-heading">
                                <?php if ($checked = in_array($id, $migrated_object_ids)): ?>
                                <span class="glyphicon glyphicon-ok-sign text-success"></span>
                                <?php endif; ?>
                                <input type="checkbox" style="visibility: hidden;" <?php echo ($checked) ? "checked" : ''; ?> id="object_<?php echo $id; ?>" name="selected_objects[]" value="<?php echo $id; ?>" />
                                <?php
                                    $total = 0;
                                    if ($id == 'review'){
                                        $total = Mage1Review::model()->count();
                                    }elseif($id == 'rating'){
                                        $total = Mage1RatingOptionVote::model()->count();
                                    }
                                ?>
                                <span> <?php echo $label . " (". $total .")"; ?> </span>
                            </h4>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </li>
        </ul>
    </div>

    <!--  Form Buttons-->
    <div class="step-controls">
        <?php if ($step->status == MigrateSteps::STATUS_NOT_DONE): ?>
            <button type="submit" class="btn btn-primary"><?php echo Yii::t('frontend', 'Start'); ?></button>
        <?php else: ?>
            <input type="hidden" id="reset" name="reset" value="0" />
            <button type="submit" class="btn btn-danger reset"><?php echo Yii::t('frontend', 'Reset'); ?></button>
<!--            <a href="--><?php //echo Yii::app()->createUrl("migrate/step" . ++$step->sorder); ?><!--" class="btn btn-primary">--><?php //echo Yii::t('frontend', 'Next Step'); ?><!--</a>-->
        <?php endif; ?>
    </div>
    <!--// Form Buttons-->
</form>