<?php

// Create this file.
// file: application/helpers/analytics_helper.php

if (!function_exists('send_analytic_event')) {
    function send_analytic_event($event, $member, $extra = []) {
        $CI = &get_instance();
        $CI->load->library('segment');
        $CI->segment->init($_ENV['SEGMENT_KEY']);
        
        /*$CI->segment->identify([
            "userId" => $member ? $member->member_id : $CI->session->userdata('member_id'),
            "traits" => [
                "name" => $member ? ($member->first_name . ' ' . $member->last_name) : $CI->session->userdata('member_name'),
                "email" => $member ? $member->email : $CI->session->userdata('member_email'),
                "timestamp" => date('Y-m-d H:i:s')
            ]
        ]);*/
        
        $properties = [
            "name" => $member ? ($member->first_name . ' ' . $member->last_name) : $CI->session->userdata('member_name'),
            "email" => $member ? $member->email : $CI->session->userdata('member_email'),
            "timestamp" => date('Y-m-d H:i:s')
        ];
        if ($extra) {
            $properties = array_merge($properties, $extra);
        }
        
        $CI->segment->track([
            "userId" => $member ? $member->member_id : $CI->session->userdata('member_id'),
            "event" => $event,
            "properties" => $properties
        ]);
        
        $CI->segment->flush();
    }
}