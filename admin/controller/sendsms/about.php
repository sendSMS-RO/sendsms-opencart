<?php
class ControllerSendsmsAbout extends Controller {
    public function index()
    {
        $this->load->language('extension/module/sendsms');
        $this->document->setTitle($this->language->get('heading_about'));

        $this->load->model('design/layout');
        $data['heading_title'] = $this->language->get('heading_about');
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
            'text' => $this->language->get('heading_about'),
            'href' => $this->url->link('sendsms/about', 'user_token=' . $this->session->data['user_token'], true)
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
        $data['about_line1'] = $this->language->get('about_line1');
        $data['about_line2'] = $this->language->get('about_line2');
        $data['about_line3'] = $this->language->get('about_line3');
        $data['about_line4'] = $this->language->get('about_line4');
        $data['about_line5'] = $this->language->get('about_line5');
        $data['about_line6'] = $this->language->get('about_line6');
        $data['about_line7'] = $this->language->get('about_line7');
        $data['about_line8'] = $this->language->get('about_line8');
        $data['about_line9'] = $this->language->get('about_line9');

        # common template
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sendsms/about', $data));
    }
}
