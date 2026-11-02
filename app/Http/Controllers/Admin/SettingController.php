<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use QCod\AppSettings\SavesSettings;

class SettingController extends Controller
{
    use SavesSettings;

    public function __construct()
    {
        $this->middleware('permission:view-settings');
    }
}
