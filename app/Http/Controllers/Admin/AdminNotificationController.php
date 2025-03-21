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

        return ResponseAPI::success($notifications, 'notifications fetched successfully');
    }
    public function send(Request $request)
    {
        try {
            $userId = User::find($request->input('user_id'));

            if (!$userId)
            {
                throw new \Exception("there is no user id");
            }

            $notification = Notification::create([
                'user_id' => $request->input('user_id'),
                'title' => $request->input('title'),
                'message' => $request->input('message'),
                'type' => $request->input('type'),
            ]);

            event(new InvoiceNotification($request->user_id, $notification));

            return ResponseAPI::success($notification,'success sent notification');
        } catch (\Exception $e)
        {
            return ResponseAPI::error($e->getMessage(), 404);
        }

    }
}
