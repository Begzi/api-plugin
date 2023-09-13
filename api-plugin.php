<?php

namespace NamePlugin;

class NameApi {
    public $apiUrl;

    public function listVacansies($post, $vid = 0) {
        global $wpdb;

        $vanacies = array();

        if (!is_object($post)) {
            return false;
        }

        $page = 0;
        $foundVacancy = false;
        l1:
        $params = "status=all&id_user=" . $this->selfGetOption('superjob_user_id') . "&with_new_response=0&order_field=date&order_direction=desc&page={$page}&count=100";
        $response = $this->apiSend($this->apiUrl . '/hr/vacancies/?' . $params);
        $responseOut = json_decode($response);
        if ($response !== false && is_object($responseOut) && isset($responseOut->objects)) {
            $vanacies = array_merge($responseOut->objects, $vanacies);
            if ($vid > 0) // Для конкретной вакансии, иначе возвращаем все
                foreach ($responseOut->objects as $key => $value) {
                    if ($value->id == $vid) {
                        $foundVacancy = $value;
                        break;
                    }
                }

            if ($foundVacancy === false && $responseOut->more) {
                $page++;
                goto l1;
            } else {
                if (is_object($foundVacancy)) {
                    return $foundVacancy;
                } else {
                    return $vanacies;
                }
            }
        }

        return false;
    }    
    public function apiSend($link) {
        return '';
    }
    public function selfGetOption($optionMame) {
        return '';
    }
}