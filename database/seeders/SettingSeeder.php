<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::insert([
            [
                'key' => 'email',
                'value' => 'tender@example.com',
            ],
            [
                'key' => 'project_name',
                'value' => 'tender',
            ],
            [
                'key' => 'facebook',
                'value' => 'http://www.facebook.com',
            ],
            [
                'key' => 'twitter',
                'value' => 'http://www.twitter.com',
            ],
            [
                'key' => 'youtube',
                'value' => 'http://www.youtube.com',
            ],
            [
                'key' => 'instagram',
                'value' => 'http://www.instagram.com',
            ],
            [
                'key' => 'whatsapp',
                'value' => '01000000000',
            ],
            [
                'key' => 'address',
                'value' => 'test',
            ],
            [
                'key' => 'messenger',
                'value' => 'http://www.messenger.com',
            ],
            [
                'key' => 'linkedin',
                'value' => 'http://www.linkedin.com',
            ],
            [
                'key' => 'about_ar',
                'value' => "هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص أو شكل توضع الفقرات في الصفحة التي يقرأها",
            ],
            [
                'key' => 'about_en',
                'value' => 'There is a proven fact from a long time ago that the readable content of a page will not distract the reader from focusing on the external appearance of the text or the form of paragraphs placed on the page that he reads',
            ],
            [
                'key' => 'privacy_en',
                'value' => 'There is a proven fact from a long time ago that the readable content of a page will not distract the reader from focusing on the external appearance of the text or the form of paragraphs placed on the page that he reads',
            ],
            [
                'key' => 'privacy_ar',
                'value' => "هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص أو شكل توضع الفقرات في الصفحة التي يقرأها",
            ],
            [
                'key' => 'terms_en',
                'value' => 'There is a proven fact from a long time ago that the readable content of a page will not distract the reader from focusing on the external appearance of the text or the form of paragraphs placed on the page that he reads',
            ],
            [
                'key' => 'terms_ar',
                'value' => "هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص أو شكل توضع الفقرات في الصفحة التي يقرأها",
            ],
            [
                'key' => 'why_us_ar',
                'value' => "هناك حقيقة مثبتة منذ زمن طويل وهي أن المحتوى المقروء لصفحة ما سيلهي القارئ عن التركيز على الشكل الخارجي للنص أو شكل توضع الفقرات في الصفحة التي يقرأها",
            ],
            [
                'key' => 'why_us_en',
                'value' => 'There is a proven fact from a long time ago that the readable content of a page will not distract the reader from focusing on the external appearance of the text or the form of paragraphs placed on the page that he reads',
            ],
        ]);
    }
}
