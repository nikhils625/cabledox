<?php

return [

    'uploadPaths' => [
        'uploadProfileImage'   => public_path().'/uploads/user_profile/',
        'uploadDrawingImage'   => public_path().'/uploads/job_drawing/',
        'viewProfileImage'     => '/uploads/user_profile/',
        'viewCompanyLogo'      => '/uploads/company_logo/',
        'viewDrawingImage'     => '/uploads/job_drawing/',

        'jobs'                      => public_path().'/uploads/jobs/',
        'viewJobs'                  => '/uploads/jobs/',
        'inspectorSignatureImage'   => 'signatures/inspector_signatures/',
        'pcInspectorSignatureImage' => 'signatures/pc_inspector_signatures/',
        'finalCheckSeetDocument'    => 'final_check_sheet_document/',
        'singleLineDrawing'         => 'drawing/singleLine/',
        'schematicDrawing'          => 'drawing/schematic/',
        'locationDrawing'           => 'drawing/location/',
        'jobCablePdf'               => 'pdf/',
    ],

    'static' => [
        'staticProfileImage' => '/assets/images/user.png',
        'cabeldoxLogo' => '/assets/images/logo.png',
    ],

    'restriction' => [
    ],
];