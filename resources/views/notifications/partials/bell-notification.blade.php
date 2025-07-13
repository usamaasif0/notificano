<li class="nav-item dropdown">
    <a class="nav-link position-relative" href="#" id="notificationsDropdown" role="button"
        data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        <i class="fa fa-bell"></i>
        <span class="notification-count badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle d-{{ empty($initialUnreadCount) ? 'none' : '' }}">{{ $initialUnreadCount }}</span>
    </a>
    <ul class="dropdown-menu dropdown-menu-end pb-0" aria-labelledby="notificationsDropdown"
        style="width: 300px;">
        <li>
            <div class="dropdown-header d-flex justify-content-between align-items-center px-3 py-2 ">
                <span>Notifications</span>
                <span class="badge bg-primary rounded-0" data-max-notification="{{ $max_notifications }}">
                    Max {{ $max_notifications }}
                </span>
                <!-- <a href="{{ route('notifications.index') }}" class="text-decoration-none">View all</a> -->
            </div>
        </li>
        <li>
            <hr class="dropdown-divider mb-0">
        </li>
        <li>
            <div class="notifications-list" style="max-height: 300px; overflow-y: auto; overflow-x: hidden; ">
                <!-- Notifications will be loaded here -->

                @forelse($unreadNotifications as $notification)
                @php
                $url = !empty($notification->url) ? url($notification->url) : 'javascript:void(0)';
                @endphp
                <a href="{{ $url }}" class="notification-item dropdown-item d-flex align-items-center {{ ($loop->last) ? '' : 'border-bottom' }} {{ ($notification->read_at) ? 'read-notification' : 'mark-as-read' }}" data-id="{{ $notification->id }}">
                    <div class="me-3">
                        <img src="{{ getImage('storage/avatars/'.$notification->fromUser->avatar, 'avatar') }}" class="rounded-circle" height="45">
                    </div>
                    <div class="w-75">
                        <small class="fw-bold">{{ $notification->fromUser->name }}</small>
                        <div class="">{{ $notification->title }}</div>
                        <span class="small text-primary">{{ $notification->created_at->diffForHumans() }}</span>
                    </div>
                    @if(!($notification->read_at))
                    <i class="fa fa-circle unread-icon text-primary float-end ms-auto"></i>
                    @endif
                </a>
                @empty
                <div class="no-notification text-center text-danger fw-bold py-1">No Notification Available</div>
                @endforelse
            </div>
        </li>
        <li>
            <hr class="dropdown-divider my-0">
        </li>
        <a href="{{ route('notifications.index') }}" class="py-1 text-center text-decoration-none row col-md-12 mx-auto all-notification-link">
            <span>View All</span>
        </a>
    </ul>
</li>



@if(auth()->user())
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script type="module">
        function updateNotificationCount(filterType, change = 0) {
            let link = $(`[data-filter='${filterType}']`);

            if (link.length) {
                let text = link.text();
                let match = text.match(/\((\d+)\)/);

                if (match) {
                    let currentCount = parseInt(match[1]);
                    let newCount = currentCount + change;

                    // Prevent negative numbers
                    newCount = Math.max(0, newCount);

                    let updatedText = text.replace(/\(\d+\)/, `(${newCount})`);
                    link.text(updatedText);
                }
            }
        }

        
        $(document).ready(function () {
            let notificationsList = $(".notifications-list");
            let notificationCount = $(".notification-count");
            let to_user = @json(auth()->user()->id); // Correct way to pass Blade variable

            window.Echo.private("notifications." + to_user)
            .listen(".notification", (data) => {
                let timeAgo = "Just now";

                // Check if parent has #notificationsContainer
                let inNotificationsContainer = notificationsList.closest("#notificationsContainer").length > 0;
                
                // Build attributes conditionally
                let statusAttr = inNotificationsContainer ? 'data-status="unread"' : "";

                // Create notification item dynamically
                let newNotification = `
                    <a href="${data.url || "javascript:void(0)"}" class="notification-item px-3 dropdown-item d-flex align-items-center border-bottom mark-as-read" data-id="${data.id}" ${statusAttr}>
                        <div class="me-3">
                            <img src="${data.avatar}" class="rounded-circle" height="45">
                        </div>
                        <div class="w-75">
                            <small class="fw-bold">${data.from_user ?? "No User"}</small>
                            <div>${data.title || "New Notification"}</div>
                            <span class="small text-primary">${timeAgo}</span>
                        </div>
                        <i class="fa fa-circle unread-icon text-primary float-end ms-auto"></i>
                    </a>
                `;

                $('.no-notification, #noNotificationMessage').remove()

                if($('.dropdown-menu .notification-item').length > 4){
                    $('.dropdown-menu .notification-item').last().remove()
                }

                // Append new notification
                notificationsList.prepend(newNotification);

                // Update notification count
                let currentCount = parseInt(notificationCount.text()) || 0;
                notificationCount.text(currentCount + 1).removeClass("d-none");
                updateNotificationCount('all', +1)
                updateNotificationCount('unread', +1)
            })
            .listen(".notification.read", (eventData) => {
                let notificationId = eventData.notification_id;

                // Find the notification item with the matching data-id
                let notificationItem = $(".notification-item[data-id='" + notificationId + "']");

                if (notificationItem.length) {
                    notificationItem.blur();
                    notificationItem.removeClass("mark-as-read").addClass("read-notification")
                    if (notificationItem.attr("data-status") !== undefined) {
                        notificationItem.attr("data-status", "read");
                        
                    }
                    // Find and remove the unread icon inside the notification item
                    notificationItem.find(".unread-icon").remove();
                }
            })
            .listen(".notification.count", (eventData) => {
                let unreadCount = eventData.count;

                // Update the notification count badge
                if (notificationCount.length) {
                    notificationCount.text(unreadCount);

                    if (unreadCount === 0) {
                        notificationCount.hide();
                    } else {
                        notificationCount.show();
                    }
                }
            });

        });


        $(document).on("click", ".mark-as-read", function () {
            let clickedItem = $(this); // Store reference to clicked element
            let notificationId = clickedItem.data("id");
            let markAsReadRoute = "{{ route('notifications.mark-as-read', ':notification') }}";
            let url = markAsReadRoute.replace(":notification", notificationId); // Replace placeholder with actual ID
            let to_user = "{{ auth()->user()->id }}";
            let filter = $('#notificationTabs .nav-link.active').data('filter');


            // Send AJAX request to Laravel to mark as read
            $.ajax({
                url: url,
                method: "POST",
                contentType: "application/json",
                headers: {
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content"),
                },
                data: JSON.stringify({
                    notification_id: notificationId,
                    to_user: to_user,
                }),
                success: function (data) {
                    if (data.success) {
                        updateNotificationCount('read', +1);
                        updateNotificationCount('unread', -1);

                        if (clickedItem.attr("data-status") !== undefined) {
                            clickedItem.attr("data-status", "read");
                        }
                        filterNotificationsByTab(filter)

                        console.log("✅ Notification marked as read:", data);
                    }
                },
                error: function (error) {
                    console.error("❌ Error:", error);
                },
            });
        });

    </script>
@endif