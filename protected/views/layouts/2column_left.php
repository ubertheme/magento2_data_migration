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
                <?php if(Yii::app()->user->hasFlash('error')):  ?>
                    <div id="message" class="flash-error">
                        <?php echo Yii::app()->user->getFlash('error'); ?>
                    </div>
                <?php endif;?>
                <?php if(Yii::app()->user->hasFlash('note')):  ?>
                    <div id="message" class="flash-notice">
                        <?php echo Yii::app()->user->getFlash('note'); ?>
                    </div>
                <?php endif;?>
                <?php if(Yii::app()->user->hasFlash('success')):  ?>
                    <div id="message" class="flash-success">
                        <?php echo Yii::app()->user->getFlash('success'); ?>
                    </div>
                <?php endif;?>

                <?php echo $content; ?>
            </div><!-- content -->
        </div>
    </div>
</div>
<?php $this->endContent(); ?>