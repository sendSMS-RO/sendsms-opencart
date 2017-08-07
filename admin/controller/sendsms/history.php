<?php
class ControllerSendsmsHistory extends Controller {
    public function index()
    {
        $this->load->language('extension/module/sendsms');
        $this->document->setTitle($this->language->get('heading_history'));

        $this->load->model('design/layout');
        $this->load->model('extension/sendsms/history');
        $data['heading_title'] = $this->language->get('heading_history');
        $data['user_token'] = $this->session->data['user_token'];

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
            'text' => $this->language->get('heading_history'),
            'href' => $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'], true)
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
        $data['history_status'] = $this->language->get('history_status');
        $data['history_message'] = $this->language->get('history_message');
        $data['history_details'] = $this->language->get('history_details');
        $data['history_content'] = $this->language->get('history_content');
        $data['history_type'] = $this->language->get('history_type');
        $data['history_phone'] = $this->language->get('history_phone');
        $data['history_date'] = $this->language->get('history_date');
        $data['history_filter'] = $this->language->get('history_filter');

        # items
        if (isset($this->request->get['filter_status'])) {
            $filter_status = $this->request->get['filter_status'];
        } else {
            $filter_status = '';
        }

        if (isset($this->request->get['filter_message'])) {
            $filter_message = $this->request->get['filter_message'];
        } else {
            $filter_message = '';
        }

        if (isset($this->request->get['filter_details'])) {
            $filter_details = $this->request->get['filter_details'];
        } else {
            $filter_details = '';
        }

        if (isset($this->request->get['filter_content'])) {
            $filter_content = $this->request->get['filter_content'];
        } else {
            $filter_content = '';
        }

        if (isset($this->request->get['filter_type'])) {
            $filter_type = $this->request->get['filter_type'];
        } else {
            $filter_type = '';
        }

        if (isset($this->request->get['filter_phone'])) {
            $filter_phone = $this->request->get['filter_phone'];
        } else {
            $filter_phone = '';
        }

        if (isset($this->request->get['filter_date'])) {
            $filter_date = $this->request->get['filter_date'];
        } else {
            $filter_date = '';
        }

        if (isset($this->request->get['sort'])) {
            $sort = $this->request->get['sort'];
        } else {
            $sort = 'sent_on';
        }

        if (isset($this->request->get['order'])) {
            $order = $this->request->get['order'];
        } else {
            $order = 'DESC';
        }

        if (isset($this->request->get['page'])) {
            $page = $this->request->get['page'];
        } else {
            $page = 1;
        }

        $url = '';

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_message'])) {
            $url .= '&filter_message=' . urlencode(html_entity_decode($this->request->get['filter_message'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_details'])) {
            $url .= '&filter_details=' . urlencode(html_entity_decode($this->request->get['filter_details'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_content'])) {
            $url .= '&filter_content=' . urlencode(html_entity_decode($this->request->get['filter_content'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_type'])) {
            $url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_phone'])) {
            $url .= '&filter_phone=' . urlencode(html_entity_decode($this->request->get['filter_phone'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_date'])) {
            $url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['history'] = array();

        $filter_data = array(
            'filter_status' => $filter_status,
            'filter_message' => $filter_message,
            'filter_details' => $filter_details,
            'filter_content' => $filter_content,
            'filter_type' => $filter_type,
            'filter_phone' => $filter_phone,
            'filter_date' => $filter_date,
            'sort' => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_limit_admin'),
            'limit' => $this->config->get('config_limit_admin')
        );

        $history_total = $this->model_extension_sendsms_history->getTotalHistory($filter_data);

        $results = $this->model_extension_sendsms_history->getHistory($filter_data);

        foreach ($results as $result) {
            $data['history'][] = array(
                'status' => $result['status'],
                'message' => $result['message'],
                'details' => $result['details'],
                'content' => $result['content'],
                'type' => $result['type'],
                'sent_on' => $result['sent_on'],
                'phone' => $result['phone']
            );
        }

        if (isset($this->request->post['selected'])) {
            $data['selected'] = (array)$this->request->post['selected'];
        } else {
            $data['selected'] = array();
        }

        $url = '';

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_message'])) {
            $url .= '&filter_message=' . urlencode(html_entity_decode($this->request->get['filter_message'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_details'])) {
            $url .= '&filter_details=' . urlencode(html_entity_decode($this->request->get['filter_details'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_content'])) {
            $url .= '&filter_content=' . urlencode(html_entity_decode($this->request->get['filter_content'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_type'])) {
            $url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_phone'])) {
            $url .= '&filter_phone=' . urlencode(html_entity_decode($this->request->get['filter_phone'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_date'])) {
            $url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
        }

        if ($order == 'ASC') {
            $url .= '&order=DESC';
        } else {
            $url .= '&order=ASC';
        }

        if (isset($this->request->get['page'])) {
            $url .= '&page=' . $this->request->get['page'];
        }

        $data['sort_status'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
        $data['sort_message'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . '&sort=message' . $url, true);
        $data['sort_details'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . '&sort=details' . $url, true);
        $data['sort_content'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . '&sort=content' . $url, true);
        $data['sort_type'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . '&sort=type' . $url, true);
        $data['sort_sent_on'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . '&sort=sent_on' . $url, true);
        $data['sort_phone'] = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . '&sort=phone' . $url, true);

        $url = '';

        if (isset($this->request->get['filter_status'])) {
            $url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_message'])) {
            $url .= '&filter_message=' . urlencode(html_entity_decode($this->request->get['filter_message'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_details'])) {
            $url .= '&filter_details=' . urlencode(html_entity_decode($this->request->get['filter_details'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_content'])) {
            $url .= '&filter_content=' . urlencode(html_entity_decode($this->request->get['filter_content'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_type'])) {
            $url .= '&filter_type=' . urlencode(html_entity_decode($this->request->get['filter_type'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_phone'])) {
            $url .= '&filter_phone=' . urlencode(html_entity_decode($this->request->get['filter_phone'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['filter_date'])) {
            $url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
        }

        if (isset($this->request->get['sort'])) {
            $url .= '&sort=' . $this->request->get['sort'];
        }

        if (isset($this->request->get['order'])) {
            $url .= '&order=' . $this->request->get['order'];
        }

        $pagination = new Pagination();
        $pagination->total = $history_total;
        $pagination->page = $page;
        $pagination->limit = $this->config->get('config_limit_admin');
        $pagination->url = $this->url->link('sendsms/history', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

        $data['pagination'] = $pagination->render();

        $data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($history_total - $this->config->get('config_limit_admin'))) ? $history_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $history_total, ceil($history_total / $this->config->get('config_limit_admin')));

        $data['filter_status'] = $filter_status;
        $data['filter_message'] = $filter_message;
        $data['filter_details'] = $filter_details;
        $data['filter_content'] = $filter_content;
        $data['filter_type'] = $filter_type;
        $data['filter_phone'] = $filter_phone;
        $data['filter_date'] = $filter_date;

        $data['sort'] = $sort;
        $data['order'] = $order;

        # common template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sendsms/history', $data));
    }
}
