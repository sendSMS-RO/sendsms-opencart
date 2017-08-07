<?php
class ControllerSendsmsTest extends Controller {

    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/sendsms');
        $this->document->setTitle($this->language->get('heading_test'));

        $this->load->model('design/layout');
        $data['heading_title'] = $this->language->get('heading_test');
        $data['user_token'] = $this->session->data['user_token'];

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            # send the message
            $this->load->model('extension/sendsms/send');
            $this->model_extension_sendsms_send->send_sms($this->request->post['module_sendsms_phone'], $this->request->post['module_sendsms_message'], 'test');

            $this->session->data['success'] = $this->language->get('text_success_test_send');

            $this->response->redirect($this->url->link('sendsms/test', 'user_token=' . $this->session->data['user_token'], true));
        }

        # breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/module/sendsms', 'user_token=' . $this->session->data['user_token'], true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_test'),
            'href' => $this->url->link('sendsms/test', 'user_token=' . $this->session->data['user_token'], true)
        );

        # page links
        $data['history_link'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'], true);
        $data['history_text'] = $this->language->get('text_history');
        $data['about_link'] = $this->url->link('sendsms/about', 'user_token=' . $this->session->data['user_token'], true);
        $data['about_text'] = $this->language->get('text_about');
        $data['campaign_link'] = $this->url->link('sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true);
        $data['campaign_text'] = $this->language->get('text_campaign');
        $data['test_link'] = $this->url->link('sendsms/test', 'user_token=' . $this->session->data['user_token'], true);
        $data['test_text'] = $this->language->get('text_test');

        # texts
        $data['button_save'] = $this->language->get('button_send');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['action'] = $this->url->link('sendsms/test', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('extension/module/sendsms', 'user_token=' . $this->session->data['user_token'], true);
        $data['entry_phone'] = $this->language->get('test_entry_phone');
        $data['entry_message'] = $this->language->get('test_entry_message');
        $data['entry_characters_left'] = $this->language->get('entry_characters_left');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $data['success'] = '';
        }

        # common template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sendsms/test', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('access', 'sendsms/test')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['module_sendsms_phone'])) {
            if (isset($this->error['warning'])) {
                $this->error['warning'] .= '<br />'.$this->language->get('error_phone_required');
            } else {
                $this->error['warning'] = $this->language->get('error_phone_required');
            }
        }

        if (empty($this->request->post['module_sendsms_message'])) {
            if (isset($this->error['warning'])) {
                $this->error['warning'] .= '<br />'.$this->language->get('error_message_required');
            } else {
                $this->error['warning'] = $this->language->get('error_message_required');
            }
        }

        return !$this->error;
    }
}
