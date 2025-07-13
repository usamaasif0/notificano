@forelse($notifications as $notification)
    @php
        $url = !empty($notification->url) ? url($notification->url) : 'javascript:void(0)';
    @endphp
    <a href="{{ $url }}" class="notification-item px-3 dropdown-item d-flex align-items-center {{ ($loop->last) ? '' : 'border-bottom' }} {{ ($notification->read_at) ? 'read-notification' : 'mark-as-read' }}" data-id="{{ $notification->id }}" data-status="{{ $notification->read_at ? 'read' : 'unread' }}">
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
    <div id="noNotificationMessage" class="text-center text-danger fw-bold py-1">No Notification Available</div>
@endforelse