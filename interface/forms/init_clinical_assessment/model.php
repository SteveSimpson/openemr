<?php

// this should take care of authorization to use the form
if (! isset($encounter) || ! $encounter) { // comes from globals.php
	die(xlt("Internal error: we do not seem to be in an encounter!"));
}


$form_fields = array(
	'presenting_problem'=>'Presenting Problem',
	'medical_issues'=>'Medical Issues',
	'sleep'=>'Sleep',
	'interests'=>'Interests',
	'guilt_concerns'=>'Guilt/Concerns',
	'energy'=>'Energy',
	'concentration'=>'Concentration',
	'appetite'=>'Appetite',
	'psychomotor'=>'Psychomotor',
	'suicidal_ideation'=>'Suicidal Ideation',
	'education'=>'Education/Employment',
	'family_history'=>'Family History',
	'trauma_history'=>'Trauma History',
	'past_psych_history'=>'Past Psychiatric History',
	'substance_use'=>'Substance Use',
	'current_coping_mechanisms'=>'Current Coping Mechanisms',
	'spirituality_support_systems'=>'Spirituality/Support Systems',
	'client_goal'=>'Client Goal',
	'interpretive_summary'=>'Interpretive Summary',
	'dsm_v_criteria'=>'Diagnostic Impression/DSM V Criteria',
);

$form_headers = array(
	'education' => 'Psycho-Social History',
	'current_coping_mechanisms' => 'Strengths and Needs',
);
