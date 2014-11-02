<?php
/* @var $this \app\controllers\Admin */
echo \app\components\htmltools\Page::title('Config Edit');
\mpf\widgets\form\Form::get(array())->publishAssets();
?>

<div class="mform mform-default-wide">
    <form method="post">
        <?php if ($this->getRequest()->secure) { ?>
            <?= \mpf\web\helpers\Form::get()->hiddenInput($this->getRequest()->getCsrfKey(), $this->getRequest()->getCsrfValue()); ?>
        <?php } ?>
        <?php foreach ($models as $model) { ?>
            <?php /* @var $model \app\models\GlobalConfig */ ?>
            <div class="row">
                <label class="label"><?= $model->name; ?></label>
                <input class="input" type="text" name="GlobalConfig[<?= $model->id; ?>][value]"
                       value="<?= $model->value; ?>"/>
                <i style="float:left; clear:both; padding-left: 3%; color:#6f6f6f;"><?= $model->description; ?></i>
                <?php if ($errors = $model->getErrors('value')) { ?>
                    <div class="errors">
                        <?php if (is_array($errors)) { ?>
                            <ul>
                                <?php foreach ($errors as $error) { ?>
                                    <li><?= $error; ?></li>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <span><?= $errors; ?></span>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="row-buttons"><input type='submit' name='save' value='Save'/></div>
    </form>
</div>