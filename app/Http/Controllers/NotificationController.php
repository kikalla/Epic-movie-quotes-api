<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
	public function sendNotifications()
	{
		return response(Notification::where('to_id', jwtUser()->id)->with('from')->orderBy('created_at', 'desc')->get(), 200);
	}

	public function readNotification(Request $request)
	{
		Notification::where('id', $request->notification_id)->update(['is_read' => true]);
		return response('Notification readed', 204);
	}

	public function readAllNotifications()
	{
		Notification::where('to_id', jwtUser()->id)->update(['is_read' => true]);
		return response('Notifications readed', 204);
	}

	public function deleteAllNotifications()
	{
		Notification::where('to_id', jwtUser()->id)->delete();
		return response('Notifications deleted', 200);
	}
}
