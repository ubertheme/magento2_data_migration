<?php $this->beginContent('/layouts/main'); ?>
<div class="container">
    <div class="row">

        <div id="menu" class="col-lg-3">
            <nav class="">
                <?php $this->widget('UserMenu', array()); ?>
            </nav> <!--// sidebar -->
        </div>

        <div id="main" class="container col-lg-9">
            <div id="content">
                <div class="progress">
                    <?php
                        $totalSteps = MigrateSteps::model()->count();
                        $totalStepsFinished = MigrateSteps::model()->count("status = ".MigrateSteps::STATUS_DONE);
                        $percent = round(($totalStepsFinished/$totalSteps)*100);
                    ?>
                    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?=$percent?>"
                         aria-valuemin="0" aria-valuemax="100" style="width:<?=$percent?>%">
                        <?=$percent?>% Completed
                    </div>
                </div>

                <?php echo $content; ?>
            </div><!-- content -->
        </div>

    </div>
</div>
<?php $this->endContent(); ?>