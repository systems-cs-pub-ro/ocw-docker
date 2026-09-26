<?php
// Course list file, relative to dokuwiki's conf/ directory.
// The file may either do $courses = [...] or return [...];
// see the starter conf/courses.php for the format.
$conf['courses_file'] = 'courses.php';

// courses URL prefix
$conf['courses_path'] = "/courses";

// Media path patterns used to auto-detect a course logo (first existing wins).
// Allowed formats:
//  - media URLs (e.g., `:namespace:logo.png`);
//  - `/courses/{{code}}`: searches inside the Farm Animal with that code;
// {{code}} is replaced with the course key.
$conf['logo_patterns'] = [
	'{{COURSES}}/{{code}}/_media/logo.svg',
	'{{COURSES}}/{{code}}/_media/logo.png',
	':course_logos:{{code}}.png',
	':course_logos:default_logo.png',
];

// Logo width in px for the built-in layout, 0 = natural size.
$conf['logo_width'] = 0;

// Item template as a DokuWiki page id, '' = built-in layout (logo left, bold
// title right). Placeholders: {{LOGO}} (media id), {{TITLE}},
// {{CONTENT}} (bold-ready fragment: [[title|link]] or plain title).
$conf['template'] = '';

// Category heading as a DokuWiki snippet, {{NAME}} = category name, '' = none.
$conf['category_template'] = '===== {{NAME}} =====';

