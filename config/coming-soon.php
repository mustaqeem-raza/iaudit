<?php

/*
 * Text for the "Coming soon" pages of the database management module.
 *
 * Each section shows this page until its week of the four-week delivery plan
 * (Revamp/iAudit_Database_Management_Estimate.pdf), then its route is pointed
 * at the real screen and its entry here is removed.
 */
return [

    'questions' => [
        'title'    => 'Questions',
        'subtitle' => 'Create, edit, retire',
        'week'     => 1,
        'intro'    => 'Manage audit questions on screen, without editing the workbook.',
        'items'    => [
            'Question list with search, section filter and status filter',
            'Add and edit questions: section, wording, answer options, priority',
            'Retire, reactivate and permanently delete questions',
        ],
    ],

    'versions' => [
        'title'    => 'Versions',
        'subtitle' => 'History and rollback',
        'week'     => 3,
        'intro'    => 'Every save and every import is kept as a version you can compare and restore.',
        'items'    => [
            'Dry run before saving, with each row\'s status and before and after values',
            'Missing questions kept or retired; conflicts resolved row by row',
            'Every save and every import stored as a version',
            'Version history: compare any version with the live one and restore it',
        ],
    ],

    'audit-trail' => [
        'title'    => 'Audit trail',
        'subtitle' => 'Who changed what',
        'week'     => 4,
        'intro'    => 'A record of every change made to the question bank.',
        'items'    => [
            'User, time, action and record for every change',
            'Before and after values for each edited field',
            'Filter the trail by action',
        ],
    ],

    'access' => [
        'title'    => 'Access',
        'subtitle' => 'Roles and permissions',
        'week'     => 4,
        'intro'    => 'Database administration limited to authorised users.',
        'items'    => [
            'Three roles: Administrator, Director and IPM Consultant',
            'Roles assigned per user',
            'Each role sees only what it is allowed to change',
        ],
    ],

];
