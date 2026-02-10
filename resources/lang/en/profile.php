<?php

return [
    'edit' => [
        'title' => 'Edit Profile',
        'breadcrumb' => [
            'home' => 'Home',
            'profile' => 'Profile',
            'edit' => 'Edit',
        ],
        'back' => 'Back',
        'photo' => [
            'helper' => 'Max 2MB. Allowed: JPG, PNG',
        ],
        'fields' => [
            'full_name' => 'Full Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'member_type' => 'Member Type',
            'company_name' => 'Company Name',
            'address' => 'Address',
            'bio' => 'Bio',
        ],
        'email_note' => 'Email address cannot be changed.',
        'member_type_placeholder' => 'Select Member Type',
        'member_types' => [
            'ppui_pihk' => 'PPUI / PIHK Member',
            'pt' => 'Company (PT)',
            'personal' => 'Personal',
        ],
        'bio_note' => 'Max 1000 characters',
        'actions' => [
            'cancel' => 'Cancel',
            'save' => 'Save Changes',
        ],
    ],
];
