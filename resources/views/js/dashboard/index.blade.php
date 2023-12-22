<script type="text/javascript">
    $(document).ready(function() { //when document is ready

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth'
        });

        calendar.render();

        @if ((isset($messageTitle) && isset($messageText)) && ($messageTitle && $messageText))
            Swal.fire({
                title: '{!! $messageTitle !!}',
                html: '{!! $messageText !!}',
                showCloseButton: true,
                confirmButtonText: 'Go to off-site circulations',
                icon: 'info',
                iconColor: '#1572E8',
                confirmButtonColor: '#1572E8',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('off.site.circulations.index') }}';
                }
            })
        @endif


    });
</script>
