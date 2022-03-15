<?php

namespace statikbe\sentry\models;

use craft\base\Model;

class Settings extends Model
{
    public $enabled = true;
    public $anonymous = true;
    public $clientDsn;
    public $excludedCodes = ['400', '404'];
    public $release;

    public function rules(): array
    {
        return [
            [['enabled', 'anonymous'], 'boolean'],
            [['clientDsn', 'excludedCodes', 'release'], 'string'],
            [['clientDsn'], 'required'],
        ];
    }
}