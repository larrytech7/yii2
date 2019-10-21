<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Payment Processing';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?php $form = ActiveForm::begin(['action' => 'index.php?r=payment-processor%2Fprocess']); ?>
        <script
            src="https://checkout.stripe.com/checkout.js" class="stripe-button"
            data-key="pk_test_95FLAm6kCNHBSvGEWMrJIhuQ"
            data-amount="<?= ($amount ?? 0) * 100 ?>"
            data-email="<?= $email ?>"
            data-panel-label="Send {{amount}}"
            data-name="V Payment Processing"
            data-description="Vouri Payment Purchase"
            data-image="<?= ('/assets/images/logo/logo-trans.png')?>"
            data-locale="auto"
            data-currency="<?= $currency ?? 'EUR' ?>">
        </script>
        <?= $form->field($model, 'name')->hiddenInput(['value' => $name])->label('') ?>
        <?= $form->field($model, 'email')->hiddenInput(['value' => $email])->label('') ?>
        <?= $form->field($model, 'v_transaction_id')->hiddenInput(['value' => $v_transaction_id])->label('') ?>
        <?= $form->field($model, 'amount')->hiddenInput(['value' => $amount])->label('')?>
        <?= $form->field($model, 'currency')->hiddenInput(['value' => $currency])->label('') ?>

        <?php ActiveForm::end(); ?>
    </p>
</div>
