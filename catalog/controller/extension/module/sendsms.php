<?php
class ControllerExtensionModuleSendsms extends Controller
{
    public function status_change($route, $data)
    {
        $orderStatusId = $data[1];
        $orderId = $data[0];

        $this->load->model('checkout/order');
        $order = $this->model_checkout_order->getOrder($orderId);
        $oldOrderStatusId = $order['order_status_id'];

        $enabled = $this->config->get('module_sendsms_status');

        if ($oldOrderStatusId != $orderStatusId && $enabled == '1') {
            # get text for event
            $this->load->model('setting/setting');
            $message = $this->config->get('module_sendsms_message_'.$orderStatusId);

            # replace variables in text
            $replace = array(
                '{billing_first_name}' => $order['payment_firstname'],
                '{billing_last_name}' => $order['payment_lastname'],
                '{shipping_first_name}' => $order['shipping_firstname'],
                '{shipping_last_name}' => $order['shipping_lastname'],
                '{order_number}' => $order['order_id'],
                '{order_date}' => $order['date_added'],
                '{order_total}' => round($order['total']*$order['currency_value'], 2).' '.$order['currency_code']
            );
            foreach ($replace as $key => $value) {
                $message = str_replace($key, $value, $message);
            }
            
            # send sms
            $this->send_sms($order['telephone'], $message);
        }
    }

    public function send_sms($phone, $message, $type = 'order')
    {
        $this->load->model('setting/setting');
        $username = $this->config->get('module_sendsms_username');
        $password = $this->config->get('module_sendsms_password');
        $from = $this->config->get('module_sendsms_label');
        $simulation = $this->config->get('module_sendsms_simulation');
        if ($simulation) {
            $phone = $this->config->get('module_sendsms_simulation_phone');
        }
        $phone = $this->validatePhone($phone);
        $message = $this->cleanDiacritice($message);

        if (!empty($phone) && !empty($username) && !empty($password) && !empty(trim($message))) {
            $curl = curl_init();
            $params = array(
                'action' => 'message_send',
                'username' => $username,
                'password' => $password,
                'to' => $phone,
                'text' => $message
            );
            if (!empty(trim($from))) {
                $params['from'] = $from;
            }
            curl_setopt($curl, CURLOPT_HEADER, 0);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_URL, 'https://api.sendsms.ro/json?'.http_build_query($params));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array("Connection: keep-alive"));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $status = curl_exec($curl);
            $status = json_decode($status, true);

            # add to history
            $this->load->model('extension/sendsms/history');
            $this->model_extension_sendsms_history->addHistory(
                isset($status['status'])?$status['status']:'',
                isset($status['message'])?$status['message']:'',
                isset($status['details'])?$status['details']:'',
                $message,
                $type,
                $phone
            );
        }
    }

    public function validatePhone($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (substr($phone, 0, 1) == '0' && strlen($phone) == 10) {
            $phone = '4'.$phone;
        } elseif (substr($phone, 0, 1) != '0' && strlen($phone) == 9) {
            $phone = '40'.$phone;
        } elseif (strlen($phone) == 13 && substr($phone, 0, 2) == '00') {
            $phone = substr($phone, 2);
        }
        return $phone;
    }

    /**
     * @param $string
     * @return string
     */
    public function cleanDiacritice($string)
    {
        $bad = array(
            "\xC4\x82",
            "\xC4\x83",
            "\xC3\x82",
            "\xC3\xA2",
            "\xC3\x8E",
            "\xC3\xAE",
            "\xC8\x98",
            "\xC8\x99",
            "\xC8\x9A",
            "\xC8\x9B",
            "\xC5\x9E",
            "\xC5\x9F",
            "\xC5\xA2",
            "\xC5\xA3",
            "\xC3\xA3",
            "\xC2\xAD",
            "\xe2\x80\x93");
        $cleanLetters = array("A", "a", "A", "a", "I", "i", "S", "s", "T", "t", "S", "s", "T", "t", "a", " ", "-");
        return str_replace($bad, $cleanLetters, $string);
    }
}
