@extends('layouts.app')

@section('content')
<div class="container">
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if(!$table_available)
            <div class="alert alert-danger py-2">
                Notifications Table Does Not Exists.
            </div>
            @endif
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center py-0 pe-0">
                    <span>Notifications
                        @if($notifications->where('is_read', false)->count() > 0)
                            <button class="btn btn-sm btn-link rounded-0 px-0" id="markAllAsRead">
                                Mark all as read
                            </button>
                        @endif
                    </span>
                    
                    <div>
                        <ul class="nav nav-pills" id="notificationTabs">
                            <li class="nav-item border-start">
                                <a class="nav-link rounded-0 active" href="#" data-filter="all">All ({{ $totalNotificationCount ?? 0 }})
                                </a>
                            </li>
                            <li class="nav-item border-start">
                                <a class="nav-link rounded-0" href="#" data-filter="read">Read ({{ $readNotificationCount ?? 0 }})</a>
                            </li>
                            <li class="nav-item border-start">
                                <a class="nav-link rounded-0" href="#" data-filter="unread">Unread ({{ $unreadNotificationCount ?? 0 }})</a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card-body p-0 notifications-list" id="notificationsContainer">
                    @include('notificano::notifications.partials.list')
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @if(true)
        <script>
            function filterNotificationsByTab(filter) {
                // Set active tab UI
                $("#notificationTabs .nav-link").removeClass("active");
                $(`#notificationTabs .nav-link[data-filter="${filter}"]`).addClass("active");

                let notifications = $("#notificationsContainer .notification-item");
                notifications.addClass("d-none");

                let visibleNotifications = 0;

                if (filter === "all") {
                    notifications.removeClass("d-none");
                    visibleNotifications = notifications.length;
                } else {
                    let matched = notifications.filter(`[data-status="${filter}"]`);
                    matched.removeClass("d-none");
                    visibleNotifications = matched.length;
                }

                // Message logic
                const messageText = filter === "all"
                    ? "No Notification Available"
                    : `No ${filter.charAt(0).toUpperCase() + filter.slice(1)} Notification Available`;

                let messageBox = $("#noNotificationMessage");
                if (visibleNotifications === 0) {
                    if (messageBox.length === 0) {
                        $("#notificationsContainer").append(`
                            <div id="noNotificationMessage" class="text-center text-danger fw-bold py-1">
                                ${messageText}
                            </div>
                        `);
                    } else {
                        messageBox.text(messageText);
                    }
                } else {
                    messageBox.remove();
                }
            }

            $(document).on("click", "#notificationTabs .nav-link", function (e) {
                e.preventDefault();
                const filter = $(this).data("filter");
                filterNotificationsByTab(filter);
            });


            $(document).on("click", "#markAllAsRead", function(){
                // Send AJAX request to Laravel to mark as read
                let url = "{{ route('notifications.mark-all-as-read') }}";
                $.ajax({
                    url: url,
                    method: "POST",
                    contentType: "application/json",
                    headers: {
                        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
                    },
                    success: function (data) {
                        if (data.success) {
                            location.reload()
                        }
                    },
                    error: function (error) {
                        console.error("❌ Error:", error);
                    },
                });
            })

        </script>
    @endif
@endsection