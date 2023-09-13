<?php

namespace NamePlugin;

class NameApi {
    public $api_url;

    public function list_vacansies($post, $vid = 0) {
        global $wpdb;

        $ret = array();

        if (!is_object($post)) {
            return false;
        }

        $page = 0;
        $found = false;
        l1:
        $params = "status=all&id_user=" . $this->self_get_option('superjob_user_id') . "&with_new_response=0&order_field=date&order_direction=desc&page={$page}&count=100";
        $res = $this->api_send($this->api_url . '/hr/vacancies/?' . $params);
        $res_o = json_decode($res);
        if ($res !== false && is_object($res_o) && isset($res_o->objects)) {
            $ret = array_merge($res_o->objects, $ret);
            if ($vid > 0) // Для конкретной вакансии, иначе возвращаем все
                foreach ($res_o->objects as $key => $value) {
                    if ($value->id == $vid) {
                        $found = $value;
                        break;
                    }
                }

            if ($found === false && $res_o->more) {
                $page++;
                goto l1;
            } else {
                if (is_object($found)) {
                    return $found;
                } else {
                    return $ret;
                }
            }
        } else {
            return false;
        }

        return false;
    }    
    public function api_send($link) {
        
        $curl = curl_init();

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $link,
            CURLOPT_HTTPHEADER => ['Content-Type:application/json'],
            CURLOPT_HEADER => false,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ];

        curl_setopt_array($curl, $options);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        $code = (int) $code;
        $response = json_decode($out, true);
        if (200 != $code) {
            return false;
        } else {
            return $response;
        }
    }
    public function self_get_option($option_name) {
        return '';
    }
}