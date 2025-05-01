<?php

use fluXtore\Core\Admin\Providers\fluXtoreS3FreeTemplateProvider;
use fluXtore\Core\Admin\Providers\fluXtoreS3ProTemplateProvider;

function fluxtore_get_templates() {
	$templates = [[
        'template' => '_blank',
        'title' => 'Blank',
        'thumbnail' => 'https://fluxtore-template-free.s3.eu-central-1.amazonaws.com/blank.png',
        'description' => 'Start with a blank template',
    ]];

	foreach (fluXtoreS3FreeTemplateProvider::listFolders() as $folder) {
		$folder = trim($folder, '/');
		$title = str_replace('-', ' ', $folder);
		$title = ucwords($title);

		$template = [
			'template' => $folder,
			'title' => $title,
			'thumbnail' => fluXtoreS3FreeTemplateProvider::getPublicURI($folder . '/preview.jpg'),
			'description' => fluXtoreS3FreeTemplateProvider::getContent($folder . '/description.txt')
		];

		$templates []= $template;
	}

	return $templates;
}

function fluxtore_get_gutenburg_templates() {
	$templates = [[
        'template' => '_blank',
        'title' => 'Blank',
        'thumbnail' => 'https://fluxtore-template-free.s3.eu-central-1.amazonaws.com/blank.png',
        'description' => 'Start with a blank template',
    ]];

	return $templates;
}

function fluxtore_get_pro_templates() {
	$templates = [];

	foreach (fluXtoreS3ProTemplateProvider::listFolders() as $folder) {
		$folder = trim($folder, '/');
		$title = str_replace('-', ' ', $folder);
		$title = ucwords($title);

		$template = [
			'template' => $folder,
			'title' => $title,
			'thumbnail' => fluXtoreS3ProTemplateProvider::getPublicURI($folder . '/preview.jpg'),
			'description' => fluXtoreS3ProTemplateProvider::getContent($folder . '/description.txt')
		];

		$templates []= $template;
	}

	return $templates;
}

function fluxtore_get_steps_templates() {
	$addedTags = [];
	$steps = [];

	foreach (fluXtoreS3FreeTemplateProvider::listFolders() as $folder) {
		foreach (fluXtoreS3FreeTemplateProvider::listFiles($folder) as $file) {
			$file = str_replace('.json', '', $file);
			$title = str_replace('-', ' ', $file);
			$title = explode('/', $title)[1];
			$title = ucwords($title);
			$tag = fluxtore_get_step_tag_by_step_name($title);

			if(FLUXTORE_DEFAULT_EDITIOR == "Elementor"){
				$step = [
					'title' => $title,
					'tag' => $tag,
					'file' => $file . '.json',
					'thumbnail' => fluXtoreS3FreeTemplateProvider::getPublicURI($file . '.jpg'),
					'order' => fluxtore_get_step_order($tag)
				];
				$steps []= $step;
			} else {
				if (!isset($addedTags[$tag])) {
					$addedTags[$tag] = true; 
					$step = [
						'template' => '_blank',
						'tag' => $tag,
						'file' => $file . '.json',  
						'title' => $title,
						'thumbnail' => 'https://fluxtore-template-free.s3.eu-central-1.amazonaws.com/blank.png',
						'description' => 'Start with a blank template',
					];

					$steps[] = $step;
				}
			}		
		}
	}

	usort($steps, function($a, $b) {return $a['order'] > $b['order'];});

	return $steps;
}

function fluxtore_get_pro_steps_templates() {
	$addedTags = [];
	$steps = [];

	foreach (fluXtoreS3ProTemplateProvider::listFolders() as $folder) {
		foreach (fluXtoreS3ProTemplateProvider::listFiles($folder) as $file) {
			$file = str_replace('.json', '', $file);
			$title = str_replace('-', ' ', $file);
			$title = explode('/', $title)[1];
			$title = ucwords($title);
			$tag = fluxtore_get_step_tag_by_step_name($title);

			if(FLUXTORE_DEFAULT_EDITIOR == "Elementor"){
				$step = [
					'title' => $title,
					'tag' => $tag,
					'file' => $file . '.json',
					'thumbnail' => fluXtoreS3ProTemplateProvider::getPublicURI($file . '.jpg'),
					'order' => fluxtore_get_step_order($tag)
				];			
		
				$steps []= $step;
			} else {
				if (!isset($addedTags[$tag]) && strtolower($title) !== "checkout" && strtolower($title) !== "landing page" && strtolower($title) !== "thank you page") {
					$addedTags[$tag] = true; 
					$step = [
						'template' => '_blank',
						'tag' => $tag,
						 'file' => $file . '.json',
						'title' => $title,
						'thumbnail' => 'https://fluxtore-template-free.s3.eu-central-1.amazonaws.com/blank.png',
						'description' => 'Start with a blank template',
					];

					$steps[] = $step;
				}
			}
		}
	}

	usort($steps, function($a, $b) {return $a['order'] > $b['order'];});

	return $steps;
}



function fluxtore_get_step_tag_by_step_name($step_name) {
	switch (true) {
		case string_contains($step_name, 'Checkout'):
			$tag = FLUXTORE_CHECKOUT_TAG;
			break;
		case string_contains($step_name, 'Thank'):
			$tag = FLUXTORE_THANK_YOU_TAG;
			break;
		case string_contains($step_name, 'Upsell'):
			$tag = FLUXTORE_UPSELL_TAG;
			break;
		case string_contains($step_name, 'Downsell'):
			$tag = FLUXTORE_DOWNSELL_TAG;
			break;
		case string_contains($step_name, 'Landing'):
		default:
			$tag = FLUXTORE_LANDING_TAG;
			break;
	}

	return $tag;
}

function fluxtore_get_step_order($tag) {
	switch (true) {
		case $tag === FLUXTORE_CHECKOUT_TAG:
			$result = 1;
			break;
		case $tag === FLUXTORE_UPSELL_TAG:
			$result = 2;
			break;
		case $tag === FLUXTORE_DOWNSELL_TAG:
			$result = 3;
			break;
		case $tag === FLUXTORE_THANK_YOU_TAG:
			$result = 4;
			break;
		default:
			$result = 0;
			break;
	}

	return $result;
}

function fluxtore_get_steps_from_template( $template ) {
	$steps = [];

    if ($template === "_blank") {
        return [[
            'title' => 'Landing Page',
            'tag' => FLUXTORE_LANDING_TAG,
            'file' => '',
            'thumbnail' => 'https://fluxtore-template-free.s3.eu-central-1.amazonaws.com/blank.png',
            'order' => 0
        ], [
            'title' => 'Checkout Page',
            'tag' => FLUXTORE_CHECKOUT_TAG,
            'file' => '',
            'thumbnail' => 'https://fluxtore-template-free.s3.eu-central-1.amazonaws.com/blank.png',
            'order' => 1
        ], [
            'title' => 'Thank You Page',
            'tag' => FLUXTORE_THANK_YOU_TAG,
            'file' => '',
            'thumbnail' => 'https://fluxtore-template-free.s3.eu-central-1.amazonaws.com/blank.png',
            'order' => 2
        ]];
    }

	foreach (fluXtoreS3FreeTemplateProvider::listFiles($template . '/') as $file) {
		$file = str_replace('.json', '', $file);
		$title = str_replace('-', ' ', $file);
		$title = explode('/', $title)[1];
		$title = ucwords($title);
		$tag = fluxtore_get_step_tag_by_step_name($title);

		$step = [
			'title' => $title,
			'tag' => $tag,
			'file' => $file . '.json',
			'thumbnail' => fluXtoreS3FreeTemplateProvider::getPublicURI($file . '.jpg'),
			'order' => fluxtore_get_step_order($tag)
		];

		$steps [] = $step;
	}

	usort($steps, function($a, $b) {return $a['order'] > $b['order'];});

	return $steps;
}

function fluxtore_get_pro_steps_from_template( $template ) {
	$steps = [];

	foreach (fluXtoreS3ProTemplateProvider::listFiles($template . '/') as $file) {
		$file = str_replace('.json', '', $file);
		$title = str_replace('-', ' ', $file);
		$title = explode('/', $title)[1];
		$title = ucwords($title);
		$tag = fluxtore_get_step_tag_by_step_name($title);

		$step = [
			'title' => $title,
			'tag' => $tag,
			'file' => $file . '.json',
			'thumbnail' => fluXtoreS3ProTemplateProvider::getPublicURI($file . '.jpg'),
			'order' => fluxtore_get_step_order($tag)
		];

		$steps [] = $step;
	}

	usort($steps, function($a, $b) {return $a['order'] > $b['order'];});

	return $steps;
}