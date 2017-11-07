<?php
/**
 * Created by PhpStorm.
 * User: danishmmir
 * Date: 4/10/17
 * Time: 9:17 PM
 */

return [

        /**
         * Your affiliate associate tag.
         */
        'associate_tag' => env('ASSOCIATE_TAG', ''),

        /**
         * Your access key.
         */
        'access_key_id' => env('ACCESS_KEY_ID', ''),

        /**
         * Your secret key.
         */
        'secret_access_key' => env('SECRET_KEY', ''),


        /**
         * Preferred locale
         */
        'locale' => env('LOCALE', 'com'),

        /**
         * Preferred response group
         */
        'response_group' => env('RESPONSE_GROUP', 'Images,ItemAttributes'),

        /**
         * Preferred search index
         */
        'search_index' => env('SEARCH_INDEX', 'All'),


    ];