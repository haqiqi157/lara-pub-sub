<?php

namespace App\Http\Controllers\Admin;

use App\Events\InvoiceNotification;
use App\Helpers\ResponseAPI;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminNotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return ResponseAPI::success($notifications, 'Notifications fetched successfully');
    }
    public function send(Request $request)
    {
        $user_id = Notification::with('users')->firstOrFail();

        if (!$user_id)
        {
            return ResponseAPI::error('there is no user_id', 404);
        }

        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'type' => 'required|string|email|max:255|unique:users'
        ]);

        try {
            $notification = Notification::create([
                'user_id' => $request->input('user_id'),
                'title' => $request->input('title'),
                'message' => $request->input('message'),
                'type' => $request->input('type'),
            ]);

            broadcast(new InvoiceNotification($request->user_id, $notification));

            return ResponseAPI::success($notification,'success sent notification');
        } catch (\Exception $e)
        {
            return ResponseAPI::error($e->getMessage(), 500);
        }

    }
}
