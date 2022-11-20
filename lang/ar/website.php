<?php

return [
    'auth' => [
        'success_sign_up' => 'تم تسجيل بنجاح برجاء تأكيد رقم الهاتف',
        'not_registered_try_again' => 'لم يتم التسجيل حاول مرة أخرى',
        'wrong_code_please_try_again' => 'الكود المدخل غير صحيح',
        'sent_code_successfully' => 'تم ارسال الكود بنجاح',
        'code_is_true' => 'الكود المدخل صحيح',
        'user_not_found' => 'المستخدم غير موجود',
        'phone_not_true_or_account_deactive' => 'الهاتف ليس صحيحًا أو الحساب معطل',
        'success_change_password' => 'تم تغيير كلمة المرور بنجاح',
        'the_old_password_is_incorrect' => 'كلمة المرور القديمة غير صحيحه ',
        'code_not_true' => 'الكود المدخل غير صحيح',
        'code_true' => 'الكود صحيح',
        'signed_out_successfully' => 'تم تسجيل الخروج بنجاح',
        'Trying_to_sign_up_for_admin_account' => 'محاولة تسجيل بحساب ادمن',
        'account_banned_by_admin' => 'هذا الحساب تم حظرة من قبل الادارة لـ :ban_reason',

        'account_is_not_activated' => 'هذا الحساب غير مفعل ',
        'verified_code_is' => 'كود تفعيل الحساب :code',
        'failed'   => 'بيانات الاعتماد هذه غير متطابقة مع البيانات المسجلة لدينا.',
        'phone_incorrect_or_not_verified' => 'تأكد من الهاتف او كود التفعيل',
        'cant_change_password' => 'لم يتم تغير كلمة السر.',
        'new_code_sent_successfuly' => 'تم إرسال كود جديد برجاء إدخاله حتي تتمكن من تغير كلمة السر بنجاح.',
        'success_change_phone' => 'تم تغير رقم الهاتف بنجاح.',
        'cant_change_phone' => 'لم يتم تغير رقم الهاتف.',
    ],

    'messages' => [
        'something_went_wrong_please_try_again' => 'حدث خطأ ما. من فضلك أعد المحاولة.',
        'account_deactive' => 'الحساب غير مفعل .تواصل مع الادارة.',
        'account_blocked' => 'تم حظر حسابك رجاء التواصل مع الادارة للتفعيل.',
    ],

    'profile' => [
        'profile_data_updated'              => 'تم تعديل بيانات الحساب بنجاح.',
        'old_password_is_not_correct'       => 'كلمة المرور القديمة غير صحيحه.',
        'password_updated_successfully'     => 'تم تغيير كلمة المرور بنجاح.',
    ],

    'error' => [
        'fail'                              => 'حدث خطأ ما برجاء مراجعة الادارة.'
    ],

    'delete' => [
        'fail'                              => 'لم يتم الحذف حاول مرة اخرى.',
        'admin'                             => 'تم حذف الادمن بنجاح',
        'country'                           => 'تم حذف الدولة بنجاح',
        'city'                              => 'تم حذف المدينه بنجاح',
        'district'                          => 'تم حذف المنطقه بنجاح',
        'brand'                             => 'تم حذف الماركة بنجاح',
        'model'                             => 'تم حذف الموديل بنجاح',
        'package'                           => 'تم حذف الباقة بنجاح',
        'vehicle'                           => 'تم حذف المركبة بنجاح',
    ],

    'update' => [
        'fail'                              => 'لم يتم التعديل حاول مرة اخرى.',
        'successfully'                      => 'تم التعديل بنجاح.',
        'country'                           => 'تم تعديل الدولة بنجاح.',
        'city'                              => 'تم تعديل المدينه بنجاح.',
        'district'                          => 'تم تعديل المنطقه بنجاح.',
    ],

    'create' => [
        'successfully'                      => 'تمت الاضافة بنجاح.',
        'fail'                              => 'لم يتم الحفظ حاول مرة اخرى.',
        'country'                           => 'تمت اضافة الدولة بنجاح.',
        'city'                              => 'تمت اضافة المدينه بنجاح.',
        'district'                          => 'تمت اضافة المنطقه بنجاح.',
    ],

    'tender' => [
        'notifications' => [
            'title' => [
                'new_tender' => 'تم إضافة صفقة جديدة :tender_id',
                'add_offer'  => 'تم تقديم عرض جديد علي صفقتك :tender_id',
            ],
            'body' => [
                'new_tender' => 'تم إضافة صفقة جديده رقم :tender_id من قبل العميل :client_name',
                'add_offer'  => 'تم تقديم عرض جديد علي صفقتك رقم :tender_id من قبل العميل :client_name',
            ],
        ],
    ],

    'expiration' => [
        'notifications' => [
            'title' => [
                'new_expiration' => 'تم إضافة تصفيات وهوالك دوريه جديدة :expiration_id',
            ],
            'body' => [
                'new_expiration' => 'تم إضافة تصفيات وهوالك دوريه جديده رقم :expiration_id من قبل العميل :client_name',
            ],
        ],
    ],

    'agent' => [
        'notifications' => [
            'title' => [
                'new_agent' => 'تم إضافة طلب وكاله او موزع جديدة :agent_id',
                'add_offer'  => 'تم تقديم عرض جديد علي طلب الوكاله او الموزع الخاصين بك :agent_id',
            ],
            'body' => [
                'new_agent' => 'تم إضافة طلب وكاله او موزع جديده رقم :agent_id من قبل العميل :client_name',
                'add_offer'  => 'تم تقديم عرض جديد علي طلب الوكاله او الموزع الخاصين بك رقم :agent_id من قبل العميل :client_name',
            ],
        ],
    ],

    'job' => [
        'notifications' => [
            'title' => [
                'new_job' => 'تم إضافة اعلان توظيف جديدة :job_id',
                'applay_on'  => 'تم تقديم علي اعلان التوظيف الخاص بك :job_id',
            ],
            'body' => [
                'new_job' => 'تم إضافة اعلان توظيف جديده رقم :job_id من قبل العميل :client_name',
                'applay_on'  => 'تم تقديم علي اعلان التوظيف الخاص بك رقم :job_id من قبل العميل :client_name',
            ],
        ],
    ],

    'user' => [
        'you_must_have_previous_work' => 'يجب إضافة السيرة الذاتيه أولا',
    ]
];
