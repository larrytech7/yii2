<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * ContactForm is the model behind the contact form.
 */
class TransactionData extends Model
{
    public $name;
    public $email;
    public $transaction_id;
    public $v_transaction_id;
    public $amount;
    public $currency;
    public $transaction_status;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            [['amount', 'amount'], 'required'],
            [['currency', 'Currency'], 'required'],
        ];
    }
}
