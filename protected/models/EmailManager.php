<?php

class EmailManager {

    private $view_directory = '//mail/';
    private $email_sender;
    private $view_payment_receipt = 'paymentReceipt';
    private $view_refund = 'refundNotice';

    /*     * ****** Constructor ******* */

    public function __construct() {
        
    }

    public function sendEmailVerifyUser($email, $url) {
        //生成数据库数据
        $output = array('status' => 'ok');
        $model = new AuthSmsVerify();
        $model->createRecord($email, true);
        $user = User::model()->loadByUsernameAndRole($email);
        if (isset($user)) {
            $data = new stdClass();
            $data->name = $user->real_name;
            $data->url = $url . "?email={$email}&code=" . $model->code;
            $view = 'verifyUser';
            $subject = "user email verify";
            $to = array($email);
            $from = array('demo@ubene.com' => 'xlh');
            $bodyParams = array();
            $bodyParams['data'] = $data;
            $num = $this->sendEmail($from, $to, $subject, $bodyParams, $view);
            if ($num < 1) {
                $output['status'] = 'no';
            }
        }
        return $output;
    }

    private function renderView($view, $params) {
        return Yii::app()->controller->renderPartial($this->view_directory . $view, $params, true); // Make sure to return true since we want t
    }

    private function sendEmailTemplate(EmailTemplate $et, array $recipients, array $bodyParams) {
        return $this->sendEmail($et->getSender(), $recipients, $et->subject, $bodyParams, $et->view);
    }

    private function sendEmail($sender, array $recipients, $subject, $bodyParams, $view) {
        $count = 0;
        $bodyParams['sender'] = $sender;
        try {
            $mail = new YiiMailMessage;
            $mail->view = $view;
            $mail->setSubject($subject);
            $mail->setBody($bodyParams, 'text/html');
            $mail->setFrom($sender);

            foreach ($recipients as $address => $name) {
                if (is_int($address)) {
                    $mail->setTo($name);
                } else {
                    // $mail->addTo($to);
                    $mail->setTo(array($address => $name));
                }
                $count+= Yii::app()->mail->send($mail);
            }
        } catch (Swift_TransportException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
            return $count;
        } catch (Swift_RfcComplianceException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);

            return $count;
        }
        return $count;
    }

    public function sendEmailMessageQueue(MessageQueue $msgqueue) {
        $message = new YiiMailMessage();
        $message->setTo($msgqueue->to_email);
        $message->setFrom(array($msgqueue->from_email => $msgqueue->from_name));
        $message->setSubject($msgqueue->subject);
        $message->setBody($msgqueue->message, 'text/html');
        return $this->sendMailMessage($message) === 1;
    }

    public function getEmailTemplateByName($name) {
        return EmailTemplate::model()->getByName($name);
    }

    /**
     * Sends an email to the user.
     * This methods expects a complete message that includes to, from, subject, and body
     *
     * @param YiiMailMessage $message the message to be sent to the user
     *  @return integer returns 1 if the message was sent successfully or 0 if unsuccessful
     */
    private function sendMailMessage(YiiMailMessage $message) {
        $count = 0;
        try {
            $count = Yii::app()->mail->send($message);
        } catch (Swift_TransportException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
        } catch (Swift_RfcComplianceException $e) {
            Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, __METHOD__);
        }
        return $count;
    }

}
