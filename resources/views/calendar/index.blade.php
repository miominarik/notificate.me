@extends('layouts.app')

@section('content')

    <section style="height: 100%">
        <div class="container-fluid py-5">
            <div id='calendar'></div>
        </div>
    </section>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let calendarEl = document.getElementById('calendar');
            let calendar = new FullCalendar.Calendar(calendarEl, {
                timeZone: 'Europe/Bratislava',
                firstDay: 1,
                @if((new \Jenssegers\Agent\Agent())->isMobile())
                initialView: 'listWeek',
                @else
                initialView: 'dayGridMonth',
                @endif
                nowIndicator: true,
                dayMaxEvents: true,
                editable: true,
                eventDurationEditable: false,
                themeSystem: 'bootstrap5',
                eventLongPressDelay: 1000,
                @if(app()->getLocale() == 'sk')
                locale: 'sk',
                @endif
                height: 'auto',
                eventDidMount: function (info) {
                    if (info.event.extendedProps.description) {
                        const tooltip = new bootstrap.Tooltip(info.el, {
                            title: info.event.extendedProps.description,
                            placement: 'top',
                            trigger: 'hover',
                            container: 'body'
                        })
                    }
                },
                eventDrop: function (info) {
                    axios.post('/calendar/update_task_time/' + info.event.id, null, {
                        params: {
                            time: info.event.start.toISOString(),
                        }
                    })
                },
                eventClick: function (info) {
                    if (info.event.id) {
                        let hash_id = info.event.id;
                        window.location.href = "/tasks/" + hash_id;
                    }
                },
                events: '{{route('calendar.data_feed')}}'
            });
            calendar.render();
        });
    </script>

@endsection
