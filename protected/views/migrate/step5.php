<?php $this->pageTitle = $step->title . ' - ' . Yii::app()->name; ?>

<h1 class="page-header"> Step <?=$step->sorder?>: <?=$step->title?> </h1>

<?php
//get migrated category ids
$migrated_category_ids = isset(Yii::app()->session['migrated_category_ids']) ? Yii::app()->session['migrated_category_ids'] : array();
$migrated_type_ids = isset(Yii::app()->session['migrated_product_type_ids']) ? Yii::app()->session['migrated_product_type_ids'] : array();
$migrated_customer_group_ids = isset(Yii::app()->session['migrated_customer_group_ids']) ? Yii::app()->session['migrated_customer_group_ids'] : array();

?>
<form role="form" method="post" action="<?php echo Yii::app()->createUrl("migrate/step{$step->sorder}"); ?>">
    <div id="step-content">
        <blockquote> <p class="tip"> <?php echo Yii::t('frontend', $step->descriptions); ?> </p> </blockquote>

        <!--    Form Buttons-->
        <?php if ($step->status == MigrateSteps::STATUS_NOT_DONE): ?>
            <div class="step-controls">
                <button type="submit" class="btn btn-primary"><?php echo Yii::t('frontend', 'Start'); ?></button>
            </div>
        <?php else: ?>
            <div class="step-controls">
                <input type="hidden" id="reset" name="reset" value="0" />
                <button type="submit" class="btn btn-danger reset"><?php echo Yii::t('frontend', 'Reset'); ?></button>
                <a href="<?php echo Yii::app()->createUrl("migrate/step" . ++$step->sorder); ?>" class="btn btn-primary"><?php echo Yii::t('frontend', 'Next Step'); ?></a>
            </div>
        <?php endif; ?>
        <!--//   Form Buttons-->

        <ul class="list-group">
            <li class="list-group-item">
                <h3 class="list-group-item-heading">
                    <input type="checkbox" id="select-all" name="select_all_customer_group" title="<?php echo Yii::t('frontend', 'Click here to select all customer groups.')?>" />
                    <?php echo Yii::t('frontend', 'Customer Groups'); ?>
                </h3>
                <?php if (isset($customer_groups) && $customer_groups): ?>
                <ul class="list-group">
                    <?php foreach ($customer_groups as $group): ?>
                    <li class="list-group-item">
                        <h4 class="list-group-item-heading">
                            <?php if ($checked = in_array($group->customer_group_id, $migrated_customer_group_ids)): ?>
                            <span class="glyphicon glyphicon-ok-sign text-success"></span>
                            <?php endif; ?>
                            <input type="checkbox" <?php echo ($checked) ? "checked" : ''; ?> id="customer_group_<?php echo $group->customer_group_id; ?>" name="customer_group_ids[]" value="<?php echo $group->customer_group_id; ?>" />
                            <span> <?php echo $group->customer_group_code . " (". MigrateSteps::getTotalCustomersByGroup($group->customer_group_id) .")"; ?> </span>
                        </h4>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </li>
        </ul>
    </div>

    <!--    Form Buttons-->
    <?php if ($step->status == MigrateSteps::STATUS_NOT_DONE): ?>
        <div class="step-controls">
            <button type="submit" class="btn btn-primary"><?php echo Yii::t('frontend', 'Start'); ?></button>
        </div>
    <?php else: ?>
        <div class="step-controls">
            <input type="hidden" id="reset" name="reset" value="0" />
            <button type="submit" class="btn btn-danger reset"><?php echo Yii::t('frontend', 'Reset'); ?></button>
            <a href="<?php echo Yii::app()->createUrl("migrate/step" . ++$step->sorder); ?>" class="btn btn-primary"><?php echo Yii::t('frontend', 'Next Step'); ?></a>
        </div>
    <?php endif; ?>
    <!--//   Form Buttons-->
</form>