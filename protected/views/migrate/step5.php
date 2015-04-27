<?php $this->pageTitle = $step->title . ' - ' . Yii::app()->name; ?>

<h1 class="page-header"> Step <?=$step->sorder?>: <?=$step->title?> </h1>

<?php
//get migrated category ids
$migrated_category_ids = isset(Yii::app()->session['migrated_category_ids']) ? Yii::app()->session['migrated_category_ids'] : array();
$migrated_type_ids = isset(Yii::app()->session['migrated_product_type_ids']) ? Yii::app()->session['migrated_product_type_ids'] : array();

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
                <a href="<?php echo Yii::app()->createUrl("migrate/step" . ++$step->sorder); ?>" class="btn btn-primary"><?php echo Yii::t('frontend', 'Next Step'); ?></a>
            <?php endif; ?>
        </div>
        <!--// Form Buttons-->

        <ul class="list-group">
            <li class="list-group-item">
                <h3 class="list-group-item-heading">
                    <input type="checkbox" id="select-all" name="select_all_type" title="<?php echo Yii::t('frontend', 'Click here to select all product types.')?>" />
                    <?php echo Yii::t('frontend', 'Product Types'); ?>
                </h3>
                <?php if (isset($product_type_ids) && $product_type_ids): ?>
                <ul class="list-group">
                    <?php foreach ($product_type_ids as $type_id): ?>
                    <li class="list-group-item">
                        <h4 class="list-group-item-heading">
                            <?php if ($checked = in_array($type_id, $migrated_type_ids)): ?>
                                <span class="glyphicon glyphicon-ok-sign text-success"></span>
                            <?php endif; ?>

                            <?php
                            //We always migrate the simple products
                            $disabled = ($type_id == 'simple') ? 'disabled' : '';
                            $checked = ($type_id == 'simple') ? true : $checked;
                            ?>

                            <?php if ($type_id == 'simple'): ?>
                            <!-- We always migrated the simple products-->
                            <input type="hidden" name="product_type_ids[]" value="simple" />
                            <?php endif; ?>

                            <label for="product_type_<?php echo $type_id; ?>" class="checkbox-inline">
                                <input type="checkbox" <?php echo ($checked) ? "checked" : ''; ?> <?php echo $disabled; ?> id="product_type_<?php echo $type_id; ?>" name="product_type_ids[]" value="<?php echo $type_id; ?>" />
                                <?php echo Yii::t('frontend', '%s Products', array('%s'=> ucfirst($type_id))) . " (". MigrateSteps::getTotalProductsByType($type_id) .")"; ?>
                            </label>
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
            <a href="<?php echo Yii::app()->createUrl("migrate/step" . ++$step->sorder); ?>" class="btn btn-primary"><?php echo Yii::t('frontend', 'Next Step'); ?></a>
        <?php endif; ?>
    </div>
    <!--// Form Buttons-->
</form>