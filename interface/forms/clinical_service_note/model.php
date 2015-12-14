<?php

// this should take care of authorization to use the form
if (! isset($encounter) || ! $encounter) { // comes from globals.php
	die(xlt("Internal error: we do not seem to be in an encounter!"));
}


$form_fields = array(
	'goal'=>'Presenting Problem',
	'intervention'=>'Intervention/Patient Response',
	'progress'=>'Progress of the Patient',
	'plan'=>'Plan for Next Session',
);

$form_headers = array();

