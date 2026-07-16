<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;

class AdminMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(10);

        return view('admin.messages.index', compact('messages'));
    }
}
