<?php

/*
 * The database management module (Option A of the proposal), delivered in
 * four weeks per Revamp/iAudit_Database_Management_Estimate.pdf.
 * Progress is tracked in docs/database-management-progress.md.
 */
return [

    /*
     * Weeks of the delivery plan that are finished. Raise this when a week is
     * signed off: the Overview page marks every capability up to this week
     * as done.
     */
    'weeks_done' => 1,

    /*
     * The seven capabilities from the proposal, in roadmap order, shown on the
     * Overview page. 'route' is the screen that delivers it (null: no screen).
     */
    'capabilities' => [
        ['label' => 'On-screen editing of audit questions',                      'week' => 1, 'route' => 'questions.index'],
        ['label' => 'CSV and Excel import and export with field mapping',       'week' => 2, 'route' => 'questions.import.show'],
        ['label' => 'Import preview and diff before anything is saved',         'week' => 3, 'route' => 'questions.import.show'],
        ['label' => 'Version history with restore',                             'week' => 3, 'route' => 'versions.index'],
        ['label' => 'Audit trail with before and after values',                 'week' => 4, 'route' => 'audit-trail.index'],
        ['label' => 'Role-based access control',                                'week' => 4, 'route' => 'access.index'],
        ['label' => 'Service and API layer for the admin panel and mobile app', 'week' => 4, 'route' => null],
    ],

    /*
     * Text for sections that show the "Coming soon" page until their week.
     * When a section's real screen lands, point its route at the controller
     * and remove its entry here.
     */
    'coming_soon' => [

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

    ],

];
