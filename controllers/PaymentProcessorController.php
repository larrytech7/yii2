<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use Omnipay\Omnipay;
use app\models\TransactionData as TransactionData;

class PaymentProcessorController extends Controller
{
    private $STRIPE_KEYS = [
        'live' => '',//TODO : put your API secret server keys here
        'test' => 'sk_test_9mxeeBqmnVlKyd2qPEuFRSES'
    ];
    private $redirect_url = [
        'live' => 'https://vouriinc.com/paymentprocess/callback?',
        'test' => 'http://localhost/vouri/paymentprocess/callback?'
    ];

    /**
     * Ask user to confirm payment via Stripe or other payment processor.
     *
     * @return string
     */
    public function actionIndex()
    {
        $params = Yii::$app->request->get(); //encrypted data sent from vouri
        //die(var_dump($params));
        $data = [
            'name' => urldecode($this->decrypt($params['name'])),
            'email' => urldecode($this->decrypt($params['email'])) ?? 'hello@vouriinc.com',
            'v_transaction_id' => $this->decrypt($params['v_transaction_id']),
            'amount' => $this->decrypt($params['amount']) ?? 0,
            'currency' => $this->decrypt($params['currency']) ?? 'EUR',
            'X-Auth-Client' => urldecode($this->decrypt($params['X-Auth-Client'])) ?? 'invalid',
        ];
        $model = new TransactionData();
        $model->amount = $data['amount'];
        $model->currency= $data['currency'];

        $data['model'] = $model;
        if($data['X-Auth-Client'] != 'vouriinc.com/process')
            return $this->render('unauthorized');
        return $this->render('processor', $data);
    }

    private function decrypt($encrypted){
        $method = 'AES-256-CBC';
        $key = 'e$25LxacllokgeH..e$25LxacllokgeH';
        //set the IV
        list($data, $ivalue) = explode(':', $encrypted);
        $iv = base64_decode($ivalue);
        $decrypted = openssl_decrypt($data, $method, $key, 0, $iv);
        return $decrypted;
    }

    private function encrypt($data){
        $method = 'AES-256-CBC';
        $key = 'e$25LxacllokgeH..e$25LxacllokgeH';
        //generate the IV
        $length = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($length);
        $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted).':'.base64_encode($iv);
    }

    /**
     * Redirect the user to Vouri once payment operation is completed!
     * @return Response
     */
    public function actionProcess()
    {
        $data = [
            'token' => Yii::$app->request->post('stripeToken'),
            'name' => Yii::$app->request->post('TransactionData')['name'],
            'email' => Yii::$app->request->post('TransactionData')['email'],
            'transaction_id' => '', //from stripe
            'v_transaction_id' => Yii::$app->request->post('TransactionData')['v_transaction_id'], //from vouri
            'amount' => Yii::$app->request->post('TransactionData')['amount'],
            'currency' => Yii::$app->request->post('TransactionData')['currency'],
            'transaction_status' => 0
        ];
        try { //process transaction
            $gateway = Omnipay::create('Stripe');
            $gateway->setApiKey($this->STRIPE_KEY['test']);//TODO : Switch key to live when in production
            $token = $data['token'];
            $response = $gateway->purchase([
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'returnUrl' => 'https://muzikol.com/payment-process',
                'token' => $token
            ])->send();
            if ($response->isSuccessful()) {
                $data['transaction_id'] = $response->getTransactionReference();
                $data['transaction_status'] = 1;
            } elseif ($response->isRedirect()) {
             //redirect to offsite payment gateway
                $response->redirect();
            } else { //payment failed
                $data['transaction_status'] = -1;
            }
            //die(var_dump($data));
        }catch(\Omnipay\Common\Http\Exception $ex){
            $data['error'] = $ex->getMessage();
            $data['trace'] = $ex->getTraceAsString();
        }catch(\Omnipay\Common\Exception\InvalidCreditCardException $cce){
            $data['error'] = $cce->getMessage();
            $data['trace'] = $cce->getTraceAsString();
        }catch(\Exception $ex){
            $data['error'] = $ex->getMessage();
            $data['trace'] = $ex->getTraceAsString();
        }
        //encrypt all data values
        $mdata = [];
        foreach($data as $key => $value){
            $mdata[$key] = urlencode($this->encrypt($value));
        }
        //TODO: Change to live url when in production
        $redirect_url = $this->redirect_url['test'].http_build_query($mdata);
        return $this->redirect($redirect_url, 301);
    }
}
