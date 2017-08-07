<?php
class ModelExtensionSendsmsHistory extends Model
{
    public function createSchema()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `". DB_PREFIX ."sendsms_history` (
              `history_id` int(11) NOT NULL AUTO_INCREMENT,
              `status` varchar(255) DEFAULT NULL,
              `message` varchar(255) DEFAULT NULL,
              `details` longtext,
              `content` longtext,
              `type` varchar(255) DEFAULT NULL,
              `sent_on` datetime NOT NULL,
              `phone` varchar(255) DEFAULT NULL,
              PRIMARY KEY (`history_id`)
            ) DEFAULT CHARSET=utf8;
        ");
    }

    public function deleteSchema()
    {
        $this->db->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "sendsms_history`");
    }

    public function addHistory($status, $message, $details, $content, $type, $phone)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "sendsms_history SET status = '" . $this->db->escape($status) . "', message = '" . $this->db->escape($message) . "', details = '" . $this->db->escape($details) . "', content = '" . $this->db->escape($content) . "', type = '" . $this->db->escape($type) . "', phone = '" . $this->db->escape($phone) . "', sent_on = NOW()");
    }

    public function getTotalHistory($data = array())
    {
        $sql = "SELECT COUNT(DISTINCT history_id) AS total FROM " . DB_PREFIX . "sendsms_history WHERE 1=1";

        if (isset($data['filter_status']) && !empty($data['filter_status'])) {
            $sql .= " AND status LIKE '%" . $this->db->escape($data['filter_status']) . "%'";
        }

        if (isset($data['filter_message']) && !empty($data['filter_message'])) {
            $sql .= " AND message LIKE '%" . $this->db->escape($data['filter_message']) . "%'";
        }

        if (isset($data['filter_details']) && !empty($data['filter_details'])) {
            $sql .= " AND details LIKE '%" . $this->db->escape($data['filter_details']) . "%'";
        }

        if (isset($data['filter_content']) && !empty($data['filter_content'])) {
            $sql .= " AND content LIKE '%" . $this->db->escape($data['filter_content']) . "%'";
        }

        if (isset($data['filter_type']) && !empty($data['filter_type'])) {
            $sql .= " AND type = '" . $this->db->escape($data['filter_type']) . "'";
        }

        if (isset($data['filter_phone']) && !empty($data['filter_phone'])) {
            $sql .= " AND phone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
        }

        if (isset($data['filter_date']) && !empty($data['filter_date'])) {
            $sql .= " AND sent_on LIKE '%" . $this->db->escape($data['filter_date']) . "%'";
        }

        $query = $this->db->query($sql);

        return $query->row['total'];
    }

    public function getHistory($data = array())
    {
        $sql = "SELECT * FROM " . DB_PREFIX . "sendsms_history WHERE 1=1";

        if (isset($data['filter_status']) && !empty($data['filter_status'])) {
            $sql .= " AND status LIKE '%" . $this->db->escape($data['filter_status']) . "%'";
        }

        if (isset($data['filter_message']) && !empty($data['filter_message'])) {
            $sql .= " AND message LIKE '%" . $this->db->escape($data['filter_message']) . "%'";
        }

        if (isset($data['filter_details']) && !empty($data['filter_details'])) {
            $sql .= " AND details LIKE '%" . $this->db->escape($data['filter_details']) . "%'";
        }

        if (isset($data['filter_content']) && !empty($data['filter_content'])) {
            $sql .= " AND content LIKE '%" . $this->db->escape($data['filter_content']) . "%'";
        }

        if (isset($data['filter_type']) && !empty($data['filter_type'])) {
            $sql .= " AND type = '" . $this->db->escape($data['filter_type']) . "'";
        }

        if (isset($data['filter_phone']) && !empty($data['filter_phone'])) {
            $sql .= " AND phone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
        }

        if (isset($data['filter_date']) && !empty($data['filter_date'])) {
            $sql .= " AND sent_on LIKE '%" . $this->db->escape($data['filter_date']) . "%'";
        }

        $sort_data = array(
            'status',
            'message',
            'details',
            'content',
            'type',
            'phone',
            'sent_on'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY history_id";
        }

        if (isset($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);

        return $query->rows;
    }
}
