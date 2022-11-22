<?php

return [
    'auth' => [
        'success_sign_up' => 'Signed in successfully, please confirm your phone number.',
        'not_registered_try_again' => 'Not registered, try again.',
        'wrong_code_please_try_again' => 'wrong code please try again',
        'sent_code_successfully' => 'sent code successfully',
        'phone_not_true_or_account_deactive' => 'phone not true or account deactivated',
        'success_change_password' => 'change password successfully',
        'the_old_password_is_incorrect' => 'the old password is incorrect',
        'code_not_true' => 'code is not true',
        'code_true' => 'code is true ',
        'signed_out_successfully' => 'signed out successfully',
        'Trying_to_sign_up_for_admin_account' => 'Trying to sign up for an admin account',
        'account_banned_by_admin' => 'This account has been banned by the admin for :ban_reason',
        'account_is_not_activated' => 'This account is not activated',
        'verified_code_is' => 'verified code is :code',
        'failed'   => 'These credentials do not match our registered data.',
        'phone_incorrect_or_not_verified' => 'Verify your phone number or activation code',
        'cant_change_password' => 'cant change password.',
        'new_code_sent_successfuly' => 'a new code has been sent, please enter it to be able to change the password successfully.',
        'success_change_phone' => 'phone changed successfully',
        'cant_change_phone' => 'cant change phone.',
        'credentials_not_found' => 'the data is incorrect.',
    ],

    'messages' => [
        'something_went_wrong_please_try_again' => 'something went wrong please try again.',
        'account_deactive' => 'the account is not activated. Contact the administration.',
        'account_blocked' => 'your account has been blocked. Please contact the administration to activate it.',
    ],

    'profile' => [
        'profile_data_updated' => 'Account data has been modified successfully.',
        'old_password_is_not_correct' => 'The old password is incorrect.',
        'password_updated_successfully' => 'Password changed successfully.',
    ],

    'error' => [
        'fail' => 'Something went wrong, please check with the administrator.'
    ],

    'delete' => [
        'fail' => 'Delete not done, try again.',
        'admin' => 'admin removed successfully',
        'country' => 'country deleted successfully',
        'city' => 'City deleted successfully',
        'district' => 'The region was deleted successfully',
        'brand' => 'Brand removed successfully',
        'model' => 'model deleted successfully',
        'package' => 'package deleted successfully',
        'vehicle' => 'vehicle deleted successfully',
        'successfully' => 'deleted successfully',
    ],

    'update' => [
        'fail' => 'No modification done, try again.',
        'successfully' => 'Modified successfully.',
        'country' => 'The country has been successfully modified.',
        'city' => 'The city has been modified successfully.',
        'district' => 'The region has been modified successfully.',
    ],

    'create' => [
        'successfully' => 'Added successfully.',
        'fail' => 'Not saved, try again.',
        'country' => 'Country added successfully.',
        'city' => 'City added successfully.',
        'district' => 'District added successfully.',
    ],

    'change_status' => [
        'admin_accept' => [
            'title' => [
                'change_status' => 'your order has been approved :model_id',
            ],
            'body' => [
                'change_status' => 'your order has been approved :model_id by admin',
            ],
        ],
        'admin_reject' => [
            'title' => [
                'change_status' => 'your order has been rejected :model_id',
            ],
            'body' => [
                'change_status' => 'your order has been rejected :model_id by admin',
            ],
        ],
    ],
];
