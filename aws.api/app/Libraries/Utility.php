<?php 

namespace App\Libraries;

class Utility 
{

    public function form_titles($form_id)
    {
        $db = \Config\Database::connect();
        $form = $db->table('question_form')->where('form_id', $form_id)->get()->getRow();
        if (count(json_decode($form->title_fields, true))) {
            $title_fields = json_decode($form->title_fields);
            $title = $title_fields->entry_title ?? [];
            $sub_title = $title_fields->entry_sub_title ?? [];
        }
        return array('title' => $title, 'sub_title' => $sub_title);
    }

        public function form_district_key($form_id)
    {
        $db = \Config\Database::connect();
        $form = $db->table('question_form')->where('form_id', $form_id)->get()->getRow();
        $question_ids = json_decode($form->question_list);
        $question_ids_list = implode(',', $question_ids);

        $district_key = NULL;
        if ($question_ids) {
            $questions = $db->table('question')->whereIn('question_id', $question_ids)->orderBy('FIELD(question_id, '.$question_ids_list.')')->get()->getResult();

            foreach ($questions as $question) {
                if (isset($question->answer_values) && !is_null($question->answer_values)) {
                    $answer_values = json_decode($question->answer_values, TRUE);
                    if (isset($answer_values['db_table']) && $answer_values['db_table'] === 'app_district') {
                        $district_key = $question->question_id;
                        break;
                    }
                }
            }
        }
        return $district_key;
    }

        public function follow_up_interval($form_id)
    {
        $db = \Config\Database::connect();
        $form = $db->table('question_form')->where('form_id', $form_id)->get()->getRow();
        if (isset($form->followup_interval)) {
            return $form->followup_interval;
        } else {
            return 7; // Default follow-up interval in days
        }
    }


    public function region_district_array($region_id)
    {
        $db = \Config\Database::connect();
        $districts = $db->table('district_view')->where('region_id', $region_id)->get()->getResult();
        $district_list = [];
        foreach ($districts as $district) {
            $district_list[] = $district->name;
        }
        return $district_list;
    }


    public function question_mapper($form_id)
    {
        $db = \Config\Database::connect();
        $form = $db->table('question_form')->where('form_id', $form_id)->get()->getRow();

        $form->renamed = json_decode($form->renamed, TRUE);
        $question_ids = json_decode($form->question_list);

        $question_list = $this->ids_to_question_list($question_ids, $form->renamed);

        $compilation = [];
        foreach ($question_list as $question) {
            $compilation['qn'.$question->question_id] = $question->question;
            // Store question metadata for value resolution
            $compilation['qn'.$question->question_id.'_meta'] = [
                'answer_type_id' => $question->answer_type_id ?? null,
                'answer_values' => $question->answer_values ?? null
            ];
        }
        return $compilation;
    }

    public function resolve_answer_value($question_key, $response_value, $question_meta)
    {
        // If no metadata or value is null/empty, return original value
        if (!$question_meta || empty($response_value)) {
            return $response_value;
        }

        $answer_values = $question_meta['answer_values'] ?? null;

        // If no answer_values, return original value
        if (!$answer_values) {
            return $response_value;
        }

        // Handle database table lookups
        if (isset($answer_values->db_table)) {
            $db = \Config\Database::connect();

            try {
                switch ($answer_values->db_table) {
                    case 'app_project':
                        $result = $db->table('app_project')->where('project_id', $response_value)->get()->getRow();
                        return $result ? $result->name : $response_value;

                    case 'app_village':
                        $result = $db->table('app_village')->where('village_id', $response_value)->get()->getRow();
                        return $result ? $result->name : $response_value;

                    case 'app_district':
                        $result = $db->table('app_district')->where('district_id', $response_value)->get()->getRow();
                        return $result ? $result->name : $response_value;

                    case 'app_parish':
                        $result = $db->table('app_parish')->where('parish_id', $response_value)->get()->getRow();
                        return $result ? $result->name : $response_value;

                    case 'app_sub_county':
                        $result = $db->table('app_sub_county')->where('sub_county_id', $response_value)->get()->getRow();
                        return $result ? $result->name : $response_value;

                    default:
                        // For other db_table values, try a generic approach
                        $table_name = $answer_values->db_table;
                        $id_field = str_replace('app_', '', $table_name) . '_id';
                        $result = $db->table($table_name)->where($id_field, $response_value)->get()->getRow();
                        return $result ? ($result->name ?? $result->title ?? $response_value) : $response_value;
                }
            } catch (\Exception $e) {
                // If database lookup fails, return original value
                return $response_value;
            }
        }

        // For static answer values, check if it's an array with mappings
        if (is_array($answer_values)) {
            // Check if response_value is a valid array key type (string or integer)
            if (is_string($response_value) || is_int($response_value)) {
                // If response_value exists as a key in the array, return the mapped value
                return $answer_values[$response_value] ?? $response_value;
            }
            // If response_value is an array or object, return it as-is
            return $response_value;
        }

        // Return original value if no mapping found
        return $response_value;
    }





    public function form_subheader_mapper($form_id)
    {
        $db = \Config\Database::connect();
        $form = $db->table('question_form')->where('form_id', $form_id)->get()->getRow();

        $renamed = json_decode($form->renamed, TRUE);
        $question_ids = json_decode($form->question_list);
        $question_ids_list = implode(',', $question_ids);

        $questions = $db->table('question')->whereIn('question_id', $question_ids)->orderBy('FIELD(question_id, '.$question_ids_list.')')->get()->getResult();
        if ($questions) {
            $sub_header = [];
            foreach ($questions as $qn) {
                if (!in_array($qn->answer_type_id, ['1', '5', '6', '7', '8'])) {
                    if (!is_null($renamed) && isset($renamed['qn'.$qn->question_id])) $qn->question = $renamed['qn'.$qn->question_id];
                    $answer_values = (is_null($qn->answer_values) || $qn->answer_values === 'null' ) ? ['Total'] : (array) json_decode($qn->answer_values);
                    $main_header[] = (object) array('title' => $qn->question, 'colspan' => count($answer_values) ? count($answer_values) : 1);
                    $sub_header = array_merge($sub_header, $answer_values);
                    // Set default counter values
                    foreach ($answer_values as $value) {
                        $answer_counter['qn'.$qn->question_id][$value] = 0;
                    }
                    // Aggregatable questions list
                    $qn_keys[] = 'qn'.$qn->question_id;
                }
            }

            $headers = array('main_header' => $main_header, 'sub_header' => $sub_header, 'qn_keys' => $qn_keys, 'answer_counter' => $answer_counter);
        } else {
            $headers = array('main_header' => NULL, 'sub_header' => NULL, 'qn_keys' => NULL, 'answer_counter' => NULL);
        }
        return $headers;
    }





    public function ids_to_question_list(array $question_ids, array $renamed = NULL)
    {
        $question_ids_list = implode(',', $question_ids);
        
        $db = \Config\Database::connect();
        $questions = $db->table('view_question')->whereIn('question_id', $question_ids)->orderBy('FIELD(question_id, '.$question_ids_list.')')->get()->getResult();
        foreach ($questions as $question) {
            if (!is_null($renamed) && isset($renamed['qn'.$question->question_id])) {
                $question->question = $renamed['qn'.$question->question_id];
            }
            $question->answer_values = json_decode($question->answer_values);
        }
        return $questions ?? null;
    }


    public function mobile_user_mapper()
    {
        $db = \Config\Database::connect();
        $users = $db->table('user')->get()->getResult();

        $user_map = [];
        foreach ($users as $user) {
           $user_map[$user->user_id] = trim($user->first_name.' '.$user->last_name);
        }
        return $user_map;
    }




    public function conditional_logic_mapper($question_id, $conditional_logic)
    {
        $dom = NULL;
        // if (in_array($conditional_logic['qn'.$question_id], $conditional_logic)) {
        if (isset($conditional_logic['qn'.$question_id])) {
            $qn_logic = $conditional_logic['qn'.$question_id];
            $dom = '<ul class="unstyled-list">';
            foreach ($qn_logic as $key => $value) {
                if (isset($qn_logic->{$key})) {
                    $array_keys = array_keys((array)$value);
                    $target = $qn_logic->{$key}->{$array_keys[0]};
                    $dom .= '<li>Selecting <strong>"'.$key.'"</strong> will '.$array_keys[0].' '.count((array)$target).' Questions | <a href="javascript:;">Remove</a></li>';
                }			
            }
            $dom .= '</ul>';
        }
        return $dom;
		// echo $dom;
    }









}