<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Traits\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->notifications;
        $notifications =  NotificationResource::collection($notifications)->response()->getData(true);

        return Response::successResponseWithData($notifications);
    }

    /**
     * Display all read notifications
     *
     * @return JsonResponse
     */
    public function read() : JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->readNotifications;
        $notifications =  NotificationResource::collection($notifications)->response()->getData(true);

        return Response::successResponseWithData($notifications);
    }

    /**
     * Display all unread notifications.
     *
     * @return JsonResponse
     */
    public function unread() : JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->unreadNotifications;
        $notifications =  NotificationResource::collection($notifications)->response()->getData(true);

        return Response::successResponseWithData($notifications);
    }

    /**
     * Display the specified resource.
     *
     * @param Notification $notification
     * @return JsonResponse
     */
    public function show(Notification $notification) : JsonResponse
    {
        $notification = new NotificationResource($notification);

        return Response::successResponseWithData($notification);
    }

    public function mark(DatabaseNotification $notification) : JsonResponse
    {
        if ( $notification->notifiable_id === auth()->id() ) {
            $notification->markAsRead();
            return Response::successResponse('Notification marked as read');
        }

        return Response::errorResponse('Something bad happened', 400);
    }

    public function markAll() : JsonResponse
    {
        $user = auth()->user();
        $user->unreadNotifications->markAsRead();

        return Response::successResponse('Notifications marked as read');
    }
}
