<?php
class ControllerSendsmsCampaign extends Controller {

    private $error = array();

    public function index()
    {
        $this->load->language('extension/module/sendsms');
        $this->document->setTitle($this->language->get('heading_campaign'));

        $this->load->model('design/layout');
        $data['heading_title'] = $this->language->get('heading_campaign');
        $data['user_token'] = $this->session->data['user_token'];

        # post
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $url = '';
            if (isset($this->request->post['filter_date_start'])) {
                $url .= '&filter_date_start=' . urlencode(html_entity_decode($this->request->post['filter_date_start'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->post['filter_date_end'])) {
                $url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->post['filter_date_end'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->post['filter_sum'])) {
                $url .= '&filter_sum=' . urlencode(html_entity_decode($this->request->post['filter_sum'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->post['filter_product'])) {
                $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->post['filter_product'], ENT_QUOTES, 'UTF-8'));
            }
            if (isset($this->request->post['filter_county'])) {
                $url .= '&filter_county=' . urlencode(html_entity_decode($this->request->post['filter_county'], ENT_QUOTES, 'UTF-8'));
            }
            $this->response->redirect($this->url->link('sendsms/campaign/filter', 'user_token=' . $this->session->data['user_token']. $url, true));
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
            'text' => $this->language->get('heading_campaign'),
            'href' => $this->url->link('sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true)
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
        $data['button_save'] = $this->language->get('button_filter');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['action'] = $this->url->link('sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel'] = $this->url->link('extension/module/sendsms', 'user_token=' . $this->session->data['user_token'], true);
        $data['heading_filter'] = $this->language->get('heading_filter');
        $data['campaign_date'] = $this->language->get('campaign_date');
        $data['campaign_start_date'] = $this->language->get('campaign_start_date');
        $data['campaign_end_date'] = $this->language->get('campaign_end_date');
        $data['campaign_sum'] = $this->language->get('campaign_sum');
        $data['campaign_product'] = $this->language->get('campaign_product');
        $data['campaign_billing_county'] = $this->language->get('campaign_billing_county');

        # list of products
        $this->load->model('catalog/product');
        $data['products'] = $this->model_catalog_product->getProducts();

        # list of billing counties
        $this->load->model('extension/sendsms/orders');
        $data['counties'] = $this->model_extension_sendsms_orders->getCounties();

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

        $this->response->setOutput($this->load->view('sendsms/campaign', $data));
    }

    protected function validate()
    {
        if (!$this->user->hasPermission('access', 'sendsms/campaign')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        return !$this->error;
    }

    public function filter()
    {
        $this->load->language('extension/module/sendsms');
        $this->document->setTitle($this->language->get('heading_campaign'));

        $this->load->model('design/layout');
        $data['heading_title'] = $this->language->get('heading_campaign');
        $data['user_token'] = $this->session->data['user_token'];

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate_filtered()) {
            # send the message
            $this->load->model('extension/sendsms/send');
            foreach ($this->request->post['module_sendsms_phones'] as $phone) {
                $this->model_extension_sendsms_send->send_sms($phone, $this->request->post['module_sendsms_message'], 'campaign');
            }

            $this->session->data['success'] = $this->language->get('text_success_campaign_send');

            $this->response->redirect($this->url->link('sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true));
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
            'text' => $this->language->get('heading_campaign'),
            'href' => $this->url->link('sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true)
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

        # build url
        $url = '';
        if (isset($this->request->get['filter_date_start'])) {
            $url .= '&filter_date_start=' . urlencode(html_entity_decode($this->request->get['filter_date_start'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_date_end'])) {
            $url .= '&filter_date_end=' . urlencode(html_entity_decode($this->request->get['filter_date_end'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_sum'])) {
            $url .= '&filter_sum=' . urlencode(html_entity_decode($this->request->get['filter_sum'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
        }
        if (isset($this->request->get['filter_county'])) {
            $url .= '&filter_county=' . urlencode(html_entity_decode($this->request->get['filter_county'], ENT_QUOTES, 'UTF-8'));
        }

        # texts
        $data['button_save'] = $this->language->get('button_send');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['action'] = $this->url->link('sendsms/campaign/filter', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $data['cancel'] = $this->url->link('sendsms/campaign', 'user_token=' . $this->session->data['user_token'], true);
        $data['heading_filtered'] = $this->language->get('heading_filtered');
        $data['entry_phones'] = $this->language->get('campaign_entry_phones');
        $data['entry_message'] = $this->language->get('test_entry_message');
        $data['entry_characters_left'] = $this->language->get('entry_characters_left');

        # get phone numbers
        $filters = array(
            'filter_date_start' => isset($this->request->get['filter_date_start'])?$this->request->get['filter_date_start']:'',
            'filter_date_end' => isset($this->request->get['filter_date_end'])?$this->request->get['filter_date_end']:'',
            'filter_sum' => isset($this->request->get['filter_sum'])?$this->request->get['filter_sum']:'',
            'filter_product' => isset($this->request->get['filter_product'])?$this->request->get['filter_product']:'',
            'filter_county' => isset($this->request->get['filter_county'])?$this->request->get['filter_county']:''
        );
        $this->load->model('extension/sendsms/orders');
        $data['phones'] = $this->model_extension_sendsms_orders->getPhoneNumbers($filters);

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        # common template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sendsms/filtered', $data));
    }

    protected function validate_filtered()
    {
        if (!$this->user->hasPermission('access', 'sendsms/campaign')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (empty($this->request->post['module_sendsms_phones'])) {
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
